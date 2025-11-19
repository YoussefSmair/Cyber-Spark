<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company_db";

// الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// التحقق من إرسال البيانات عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من وجود القيم المدخلة في النموذج
    if (isset($_POST['employee_name'], $_POST['client_name'], $_POST['project_name'], $_POST['task_details'])) {
        $employee_name = $_POST['employee_name'];
        $client_name = $_POST['client_name'];
        $project_name = $_POST['project_name'];
        $task_details = $_POST['task_details'];

        // استخدام Prepared Statement لإدخال البيانات بأمان
        $stmt = $conn->prepare("INSERT INTO company_data (employee_name, client_name, project_name, task_details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $employee_name, $client_name, $project_name, $task_details);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>✔️ تم إدخال البيانات بنجاح</p>";
        } else {
            echo "<p style='color: red;'>❌ خطأ: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>❌ لم يتم إرسال جميع البيانات.</p>";
    }
}

// دالة لعرض البيانات من قاعدة البيانات
function showData($conn, $column, $label) {
    $sql = "SELECT DISTINCT $column FROM company_data WHERE $column IS NOT NULL AND $column != ''";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div style='margin: 20px 0;'><h3>$label</h3><ul>";
        while($row = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row[$column]) . "</li>";
        }
        echo "</ul></div>";
    }
}

echo "<hr>";
showData($conn, 'employee_name', 'الموظفون');
showData($conn, 'client_name', 'العملاء');
showData($conn, 'project_name', 'المشاريع');
showData($conn, 'task_details', 'المهام');

// إغلاق الاتصال
$conn->close();
?>
