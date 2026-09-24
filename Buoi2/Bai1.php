<?php

class CartItem
{
    private $name;
    private $price;
    private $quantity;

    public function __construct($name, $price, $quantity)
    {
        if (!is_numeric($price) || $price <= 0) {
            throw new InvalidArgumentException('Đơn giá sản phẩm phải lớn hơn 0.');
        }

        if (!is_numeric($quantity) || $quantity <= 0) {
            throw new InvalidArgumentException('Số lượng sản phẩm phải lớn hơn 0.');
        }

        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getTotal()
    {
        return $this->price * $this->quantity;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}

class ShoppingCart
{
    private $items = [];

    public function addItem(CartItem $item)
    {
        $this->items[] = $item;
    }

    public function removeItem($name)
    {
        foreach ($this->items as $key => $item) {
            if ($item->getName() === $name) {
                array_splice($this->items, $key, 1);
                echo "Đã xóa sản phẩm: $name.\n";
                return true;
            }
        }

        echo "Không tìm thấy sản phẩm: $name.\n";
        return false;
    }

    public function calculateTotal()
    {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    public function displayCart()
    {
        if (empty($this->items)) {
            echo "Giỏ hàng trống.\n";
            echo "Tổng tiền: 0\n";
            return;
        }

        echo "Danh sách sản phẩm:\n";
        foreach ($this->items as $item) {
            echo "- Tên: " . $item->getName()
                . " | Đơn giá: " . $item->getPrice()
                . " | Số lượng: " . $item->getQuantity()
                . " | Thành tiền: " . $item->getTotal() . "\n";
        }

        echo "Tổng tiền: " . $this->calculateTotal() . "\n";
    }
}

// Chương trình chính
$shoppingCart = new ShoppingCart();

$item1 = new CartItem('Sản phẩm 1', 100, 2);
$item2 = new CartItem('Sản phẩm 2', 200, 1);
$item3 = new CartItem('Sản phẩm 3', 150, 3);
$item4 = new CartItem('Sản phẩm 4', 50, 5);

$shoppingCart->addItem($item1);
$shoppingCart->addItem($item2);
$shoppingCart->addItem($item3);
$shoppingCart->addItem($item4);

echo "Giỏ hàng ban đầu:\n";
$shoppingCart->displayCart();

echo "\nXóa sản phẩm 2 khỏi giỏ hàng:\n";
$shoppingCart->removeItem('Sản phẩm 2');

echo "\nGiỏ hàng sau khi xóa:\n";
$shoppingCart->displayCart();
?>
