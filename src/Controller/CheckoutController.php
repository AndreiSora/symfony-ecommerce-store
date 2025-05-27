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

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function index(CartService $cartService, PaymentMethodRepository $paymentMethodRepository, ShippingMethodRepository $shippingMethodRepository): Response
    {
        $paymentMethods = $paymentMethodRepository->findBy(['active' => true]);
        $shippingMethods = $shippingMethodRepository->findBy(['active' => true]);

        $cart = $cartService->getCart();
        $countries = Countries::getNames();

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['product']->getPrice() * $item['quantity'];
        }

        $shipping = 5.00;

        return $this->render('checkout/index.html.twig', [
            'cart'     => $cart,
            'shipping' => $shipping,
            'countries' => $countries,
            'paymentMethods' => $paymentMethods,
            'shippingMethods' => $shippingMethods,
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

        $shippingMethodId = $request->request->get('shippingMethod');
        $paymentMethodId = $request->request->get('paymentMethod');

        $shippingMethod = $shippingMethodRepository->find($shippingMethodId);
        $paymentMethod = $paymentMethodRepository->find($paymentMethodId);

        if (!$shippingMethod || !$paymentMethod) {
            $this->addFlash('error', 'Invalid shipping or payment method selected.');
            return $this->redirectToRoute('cart_view');
        }

        $shipping = $shippingMethod->getPrice();
        $total = $subtotal + $shipping;

        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $name = $firstName . ' ' . $lastName;
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $country = $request->request->get('country');
        $zip = $request->request->get('zip');
        $region = $request->request->get('region');
        $city = $request->request->get('city');
        $street = $request->request->get('streetAddress');

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
}
