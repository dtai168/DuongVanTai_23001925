<?php
$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5]
];

// 1. Tìm sinh viên có điểm cao nhất
function findBestStudent($students) {
    $best = $students[0]; 
    foreach ($students as $student) {
        if ($student["score"] > $best["score"]) {
            $best = $student; // Cập nhật nếu thấy điểm cao hơn
        }
    }
    return $best;
}

// 2. Tìm sinh viên có điểm thấp nhất
function findWorstStudent($students) {
    $worst = $students[0]; 
    foreach ($students as $student) {
        if ($student["score"] < $worst["score"]) {
            $worst = $student; 
        }
    }
    return $worst;
}

// 3. Đếm số sinh viên đạt (điểm >= 5)
function countPassedStudents($students) {
    $count = 0;
    foreach ($students as $student) {
        if ($student["score"] >= 5) {
            $count++;
        }
    }
    return $count;
}

// 4. Tìm sinh viên theo tên
function findStudentByName($students, $name) {
    foreach ($students as $student) {
        if (strtolower($student["name"]) == strtolower($name)) {
            return $student;
        }
    }
    return null; 
}


$bestStudent = findBestStudent($students);
echo "- Sinh viên điểm cao nhất: {$bestStudent['name']} ({$bestStudent['score']} điểm)\n";

$worstStudent = findWorstStudent($students);
echo "- Sinh viên điểm thấp nhất: {$worstStudent['name']} ({$worstStudent['score']} điểm)\n";

$passedCount = countPassedStudents($students);
echo "- Số sinh viên đạt (điểm >= 5): {$passedCount}\n";

// Test tìm kiếm sinh viên
$searchName = "Le Van Cuong";
$found = findStudentByName($students, $searchName);

if ($found) {
    echo "- Kết quả tìm kiếm '{$searchName}': Tuổi: {$found['age']} - Điểm: {$found['score']}\n";
} else {
    echo "- Không tìm thấy sinh viên nào tên là '{$searchName}'\n";
}

?>