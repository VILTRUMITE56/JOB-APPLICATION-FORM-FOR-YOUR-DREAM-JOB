<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Application deleted successfully.';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Failed to delete application.';
            $messageType = 'error';
        }
    } else {
        $message = 'Invalid application ID.';
        $messageType = 'error';
    }
} else {
    $message = 'No application selected for deletion.';
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .msg-success { color: green; }
        .msg-error { color: red; }
    </style>
</head>
<body>
    <h1>Delete Application</h1>
    <p class="<?php echo $messageType === 'success' ? 'msg-success' : 'msg-error'; ?>">
        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
    </p>
    <a href="index.php">Back to List</a>
</body>
</html>
