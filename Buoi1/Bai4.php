<?php
class Student {
    public $name;
    public $age;
    public $score;

    public function __construct($name, $age, $score) {
        $this->name = $name;
        $this->age = $age;
        $this->score = $score;
    }

    public function getRank() {
        if ($this->score >= 8) return "Giỏi";
        if ($this->score >= 6.5) return "Khá";
        if ($this->score >= 5) return "Trung bình";
        return "Yếu";
    }

    public function isPassed() {
        return $this->score >= 5;
    }

    public function display() {
        $rank = $this->getRank();
        echo "Họ tên: {$this->name} | Tuổi: {$this->age} | Điểm: {$this->score} | Xếp loại: $rank\n";
    }
}

// Khởi tạo danh sách objects
$studentsList = [
    new Student("Nguyen Van An", 20, 8.5),
    new Student("Tran Thi Binh", 21, 6.5),
    new Student("Le Van Cuong", 19, 4.5),
    new Student("Pham Thi Dung", 20, 7.5)
];

// Hàm xử lý danh sách object
function getOopStats($studentsList) {
    $total = 0;
    $passed = 0;
    $best = $studentsList[0];

    echo "--- Danh sách sinh viên (OOP) ---\n";
    foreach ($studentsList as $student) {
        $student->display();
        $total += $student->score;
        if ($student->isPassed()) $passed++;
        if ($student->score > $best->score) $best = $student;
    }

    echo "Điểm trung bình: " . ($total / count($studentsList)) . "\n";
    echo "Sinh viên điểm cao nhất: {$best->name} ({$best->score} điểm)\n";
    echo "Số sinh viên đạt: $passed\n";
}

getOopStats($studentsList);
?>