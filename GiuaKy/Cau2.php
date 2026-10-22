<?php
$prices = [100000, 250000, 150000, 300000, 200000];

// Tính tổng giá trị các sản phẩm
function calculateTotal($prices) {
    $total = 0;

    foreach ($prices as $price) {
        $total += $price;
    }

    return $total;
}

// Tìm giá sản phẩm cao nhất
function findMaxPrice($prices) {
    $maxPrice = $prices[0];

    foreach ($prices as $price) {
        if ($price > $maxPrice) {
            $maxPrice = $price;
        }
    }

    return $maxPrice;
}

// Gọi hàm và hiển thị kết quả
$total = calculateTotal($prices);
$maxPrice = findMaxPrice($prices);

echo "Tổng giá trị sản phẩm: " . number_format($total, 0, ',', '.') . " đồng<br>";
echo "Giá sản phẩm cao nhất: " . number_format($maxPrice, 0, ',', '.') . " đồng";
?>