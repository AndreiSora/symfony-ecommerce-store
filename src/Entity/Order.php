<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customer_name = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'associated_orders')]
    private Collection $product_id;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer_address = null;

    #[ORM\Column(nullable: true)]
    private ?float $shipping_tax = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 255)]
    private ?string $payment_method = null;

    #[ORM\Column(length: 255)]
    private ?string $shipping_method = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orderId')]
    private Collection $orderItems;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function __construct()
    {
        $this->product_id = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerName(): ?string
    {
        return $this->customer_name;
    }

    public function setCustomerName(string $customer_name): static
    {
        $this->customer_name = $customer_name;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductId(): Collection
    {
        return $this->product_id;
    }

    public function addProductId(Product $productId): static
    {
        if (!$this->product_id->contains($productId)) {
            $this->product_id->add($productId);
        }

        return $this;
    }

    public function removeProductId(Product $productId): static
    {
        $this->product_id->removeElement($productId);

        return $this;
    }

    public function getCustomerAddress(): ?Customer
    {
        return $this->customer_address;
    }

    public function setCustomerAddress(?Customer $customer_address): static
    {
        $this->customer_address = $customer_address;

        return $this;
    }

    public function getShippingTax(): ?float
    {
        return $this->shipping_tax;
    }

    public function setShippingTax(?float $shipping_tax): static
    {
        $this->shipping_tax = $shipping_tax;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): static
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    public function getShippingMethod(): ?string
    {
        return $this->shipping_method;
    }

    public function setShippingMethod(string $shipping_method): static
    {
        $this->shipping_method = $shipping_method;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrderId($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrderId() === $this) {
                $orderItem->setOrderId(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
