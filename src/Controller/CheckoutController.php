<?php
// src/Controller/CheckoutController.php
namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Intl\Countries;
use App\Repository\ProductRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\ShippingMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CheckoutType;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function index(
        Request $request,
        CartService $cartService,
        PaymentMethodRepository $paymentMethodRepository,
        ShippingMethodRepository $shippingMethodRepository
    ): Response {
        $cart = $cartService->getCart();

        // Calculate subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['product']->getSpecialPrice() ?: $item['product']->getPrice();
            $subtotal += $price * $item['quantity'];
        }

        $shipping = 5.00; // default, can be dynamic

        $form = $this->createForm(CheckoutType::class, null, $this->getCheckoutFormOptions($paymentMethodRepository, $shippingMethodRepository));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('checkout_process');
        }

        $countries = Countries::getNames();
        $paymentMethods = $paymentMethodRepository->findBy(['active' => true]);
        $shippingMethods = $shippingMethodRepository->findBy(['active' => true]);

        return $this->render('checkout/index.html.twig', [
            'cart'             => $cart,
            'shipping'         => $shipping,
            'countries'        => $countries,
            'paymentMethods'   => $paymentMethods,
            'shippingMethods'  => $shippingMethods,
            'form'             => $form->createView(),
        ]);
    }

    #[Route('/checkout/process', name: 'checkout_process', methods: ['POST'])]
    public function process(
        Request $request,
        CartService $cartService,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        ShippingMethodRepository $shippingMethodRepository,
        PaymentMethodRepository $paymentMethodRepository
    ): Response {
        $cart = $cartService->getCart();
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['product']->getPrice() * $item['quantity'];
        }

        $form = $this->createForm(CheckoutType::class, null, $this->getCheckoutFormOptions($paymentMethodRepository, $shippingMethodRepository));
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Invalid form submission.');
            return $this->redirectToRoute('checkout');
        }

        $shippingMethodId = $form->get('shippingMethod')->getData();
        $paymentMethodId = $form->get('paymentMethod')->getData();

        $shippingMethod = $shippingMethodRepository->find($shippingMethodId);
        $paymentMethod = $paymentMethodRepository->find($paymentMethodId);

        if (!$shippingMethod || !$paymentMethod) {
            $this->addFlash('error', 'Invalid shipping or payment method selected.');
            return $this->redirectToRoute('cart_view');
        }

        $shipping = $shippingMethod->getPrice();
        $total = $subtotal + $shipping;
        $firstName = $form->get('firstName')->getData();
        $lastName = $form->get('lastName')->getData();
        $name = $firstName . ' ' . $lastName;
        $email = $form->get('email')->getData();
        $phone = $form->get('phone')->getData();
        $country = $form->get('country')->getData();
        $zip = $form->get('zip')->getData();
        $region = $form->get('county')->getData();
        $city = $form->get('city')->getData();
        $street = $form->get('streetAddress')->getData();

        $entityManager->beginTransaction();

        try {
            $address = new Address();
            $address->setCountry($country);
            $address->setPostcode($zip);
            $address->setCounty($region);
            $address->setCity($city);
            $address->setStreet($street);
            $entityManager->persist($address);

            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->addAddressId($address);
            $entityManager->persist($user);

            $order = new Order();
            $order->setUserName($name);
            $order->setUserAddress($user);
            $order->setShippingTax($shipping);
            $order->setPaymentMethod($paymentMethod->getName());
            $order->setShippingMethod($shippingMethod->getName());
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setStatus('new');

            foreach ($cart as $cartItem) {
                $product = $productRepository->find($cartItem['product']->getId());

                if (!$product || $product->getStock() < $cartItem['quantity']) {
                    throw new \Exception("Not enough stock for product: " . ($product ? $product->getName() : 'Unknown'));
                }

                $product->setStock($product->getStock() - $cartItem['quantity']);
                $entityManager->persist($product);

                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($cartItem['quantity']);
                $orderItem->setUnitPrice($product->getPrice());
                $entityManager->persist($orderItem);
                $order->addOrderItem($orderItem);
            }

            $order->setTotal($total);
            $entityManager->persist($order);

            $entityManager->flush();
            $entityManager->commit();

            $cartService->clear();

            return $this->redirectToRoute('checkout_success', [
                'orderId' => $order->getId(),
            ]);
        } catch (\Exception $e) {
            $entityManager->rollback();
            $this->addFlash('error', 'Order could not be processed: ' . $e->getMessage());
            return $this->redirectToRoute('cart_view');
        }
    }

    #[Route('/checkout/success/{orderId}', name: 'checkout_success')]
    public function success(int $orderId, EntityManagerInterface $em)
    {
        $order = $em->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw $this->createNotFoundException('Order not found.');
        }

        return $this->render('checkout/success.html.twig', [
            'order' => $order,
        ]);
    }

    private function getCheckoutFormOptions(
        PaymentMethodRepository $paymentMethodRepository,
        ShippingMethodRepository $shippingMethodRepository
    ): array {
        $countries = Countries::getNames();
        $paymentMethods = $paymentMethodRepository->findBy(['active' => true]);
        $shippingMethods = $shippingMethodRepository->findBy(['active' => true]);

        return [
            'countries' => array_flip($countries),
            'paymentMethods' => array_combine(
                array_map(fn($m) => $m->getId(), $paymentMethods),
                array_map(fn($m) => $m->getName(), $paymentMethods)
            ),
            'shippingMethods' => array_combine(
                array_map(fn($m) => $m->getId(), $shippingMethods),
                array_map(fn($m) => $m->getName() . ' - $' . number_format($m->getPrice(), 2), $shippingMethods)
            ),
        ];
    }
}
