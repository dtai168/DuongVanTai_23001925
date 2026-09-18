<?php
// Mảng dữ liệu sinh viên ban đầu
$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5]
];

// 1. Hàm tính điểm trung bình
function calculateAverageScore($students) {
    $total = 0;
    foreach ($students as $student) {
        $total += $student["score"];
    }
    return $total / count($students);
}

// 2. Hàm tính xếp loại dựa trên điểm
function getRank($score) {
    if ($score >= 8) {
        return "Giỏi";
    } elseif ($score >= 6.5) {
        return "Khá";
    } elseif ($score >= 5) {
        return "Trung bình";
    } else {
        return "Yếu";
    }
}

// 3. Hàm hiển thị thông tin 1 sinh viên (kèm xếp loại)
function displayStudent($student) {
    $rank = getRank($student["score"]); // Gọi hàm xếp loại
    echo "Họ tên: {$student['name']} - Tuổi: {$student['age']} - Điểm: {$student['score']} - Xếp loại: {$rank}\n";
}

// Duyệt danh sách và gọi displayStudent
foreach ($students as $student) {
    displayStudent($student);
}

// Gọi hàm calculateAverageScore
$average = calculateAverageScore($students);
echo "\nĐiểm trung bình của tất cả sinh viên: " . $average . "\n";
?>