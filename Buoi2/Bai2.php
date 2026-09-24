<?php

class Movie
{
    private $id;
    private $title;
    private $price;
    private $totalSeats;
    private $availableSeats;

    public function __construct($id, $title, $price, $totalSeats)
    {
        if (!is_numeric($price) || $price <= 0) {
            throw new InvalidArgumentException('Giá vé phải lớn hơn 0.');
        }

        if (!is_int($totalSeats) || $totalSeats <= 0) {
            throw new InvalidArgumentException('Tổng số ghế phải là số nguyên lớn hơn 0.');
        }

        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->totalSeats = $totalSeats;
        $this->availableSeats = $totalSeats;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getTotalSeats()
    {
        return $this->totalSeats;
    }

    public function getAvailableSeats()
    {
        return $this->availableSeats;
    }

    public function bookTicket($quantity)
    {
        if (!is_int($quantity) || $quantity <= 0) {
            echo "Số lượng vé đặt phải là số nguyên lớn hơn 0.\n";
            return false;
        }

        if ($quantity > $this->availableSeats) {
            echo "Không đủ số ghế còn lại để đặt vé.\n";
            return false;
        }

        $this->availableSeats -= $quantity;
        return true;
    }

    public function cancelTicket($quantity)
    {
        if (!is_int($quantity) || $quantity <= 0) {
            echo "Số lượng vé hủy phải là số nguyên lớn hơn 0.\n";
            return false;
        }

        if ($quantity > $this->getSoldSeats()) {
            echo "Không thể hủy nhiều vé hơn số vé đã bán.\n";
            return false;
        }

        $this->availableSeats += $quantity;
        return true;
    }

    public function getSoldSeats()
    {
        return $this->totalSeats - $this->availableSeats;
    }

    public function getRevenue()
    {
        return $this->getSoldSeats() * $this->price;
    }

    public function displayInfo()
    {
        echo "Mã phim: " . $this->id . "\n";
        echo "Tên phim: " . $this->title . "\n";
        echo "Giá vé: " . $this->price . "\n";
        echo "Tổng số ghế: " . $this->totalSeats . "\n";
        echo "Số ghế còn lại: " . $this->availableSeats . "\n";
        echo "Số vé đã bán: " . $this->getSoldSeats() . "\n";
        echo "Doanh thu: " . $this->getRevenue() . "\n";
    }
}

function findMovieById($movies, $id)
{
    foreach ($movies as $movie) {
        if ($movie->getId() == $id) {
            return $movie;
        }
    }

    return null;
}

function getTotalRevenue($movies)
{
    $totalRevenue = 0;

    foreach ($movies as $movie) {
        $totalRevenue += $movie->getRevenue();
    }

    return $totalRevenue;
}

function getBestSellingMovie($movies)
{
    $bestSellingMovie = null;
    $maxSoldSeats = 0;

    foreach ($movies as $movie) {
        if ($bestSellingMovie === null || $movie->getSoldSeats() > $maxSoldSeats) {
            $bestSellingMovie = $movie;
            $maxSoldSeats = $movie->getSoldSeats();
        }
    }

    return $bestSellingMovie;
}

// Chương trình chính
$movies = [
    new Movie(1, 'Avengers', 100000, 100),
    new Movie(2, 'Avatar', 120000, 80),
    new Movie(3, 'Batman', 90000, 120),
];

$avengers = findMovieById($movies, 1);
if ($avengers !== null) {
    $avengers->bookTicket(10);
    $avengers->cancelTicket(3);
}

$avatar = findMovieById($movies, 2);
if ($avatar !== null) {
    $avatar->bookTicket(15);
}

echo "Thông tin các phim:\n\n";
foreach ($movies as $movie) {
    $movie->displayInfo();
}

echo "Tổng doanh thu tất cả phim: " . getTotalRevenue($movies) . "\n";

$bestSellingMovie = getBestSellingMovie($movies);
if ($bestSellingMovie !== null) {
    echo "Phim bán chạy nhất: " . $bestSellingMovie->getTitle()
        . " (" . $bestSellingMovie->getSoldSeats() . " vé)\n";
}
