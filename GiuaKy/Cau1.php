<?php
$students = [
    ["name" => "An",    "score" => 10],
    ["name" => "Bình",  "score" => 8],
    ["name" => "Chi",   "score" => 7.9],
    ["name" => "Dũng",  "score" => 6.5],
    ["name" => "Hà",    "score" => 6.4],
    ["name" => "Linh",  "score" => 5],
    ["name" => "Minh",  "score" => 4.9],
    ["name" => "Nam",   "score" => 0],
];

foreach ($students as $student) {
    $name = $student["name"];
    $score = $student["score"];

    if ($score >= 8) {
        $rank = "Giỏi";
    } elseif ($score >= 6.5) {
        $rank = "Khá";
    } elseif ($score >= 5) {
        $rank = "Trung bình";
    } else {
        $rank = "Yếu";
    }

    echo "Tên học sinh: " . $name . "<br>";
    echo "Điểm: " . $score . "<br>";
    echo "Xếp loại: " . $rank . "<br><hr>";
}
?>