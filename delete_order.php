<?php
include "connection.php";

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    try {
        // ១. ទាញយកឈ្មោះរូបភាពទុកសិន ដើម្បីលុប file ចេញពី folder ក្រោយពេលលុបទិន្នន័យ
        $stmt = $conn->prepare("SELECT image FROM tbl_order WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // ២. លុបទិន្នន័យចេញពី Database (ប្តូរពី $cn មក $conn ឱ្យដូចក្នុង connection.php)
        $stmt_del = $conn->prepare("DELETE FROM tbl_order WHERE id = :id");
        
        if ($stmt_del->execute([':id' => $id])) {

            // ៣. លុបរូបភាពចេញពី Folder បើមាន
            if ($row && !empty($row['image']) && file_exists($row['image'])) {
                unlink($row['image']);
            }

            echo "<script>
                alert('✅ customer deleted successfully');
                window.location.href='order.php';
            </script>";
        } else {
            echo "Error deleting record.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
