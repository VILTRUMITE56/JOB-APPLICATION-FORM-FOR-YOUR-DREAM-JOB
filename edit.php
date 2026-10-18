<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$messageType = '';
$record = null;

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id <= 0) {
    die('Invalid application ID.');
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $education = trim($_POST['educational_attainment'] ?? '');
    $years = trim($_POST['years_experience'] ?? '');
    $skills = trim($_POST['technical_skills'] ?? '');

    if ($firstName === '' || $lastName === '' || $email === '' || $contact === '' || $address === '' || $education === '' || $years === '' || $skills === '') {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif (!validateEmail($email)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif (!is_numeric($years) || $years < 0) {
        $message = 'Years of experience must be a non-negative number.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE applications SET first_name=?, last_name=?, email=?, contact_number=?, address=?, educational_attainment=?, years_experience=?, technical_skills=? WHERE id=?");
            $stmt->execute([$firstName, $lastName, $email, $contact, $address, $education, $years, $skills, $id]);
            $message = 'Application updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Failed to update application. Please try again.';
            $messageType = 'error';
        }
    }
}

// Load existing record
try {
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) {
        die('Application not found.');
    }
} catch (PDOException $e) {
    die('Failed to load application.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display: block; margin-top: 8px; }
        input, textarea { width: 100%; max-width: 400px; }
        .msg-success { color: green; }
        .msg-error { color: red; }
    </style>
</head>
<body>
    <h1>Edit Application</h1>

    <?php if ($message !== ''): ?>
        <p class="<?php echo $messageType === 'success' ? 'msg-success' : 'msg-error'; ?>">
            <?php echo sanitize($message); ?>
        </p>
    <?php endif; ?>

    <form method="post" action="edit.php?id=<?php echo (int)$id; ?>">
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <label>First Name: <input type="text" name="first_name" value="<?php echo sanitize($record['first_name']); ?>" required></label>
        <label>Last Name: <input type="text" name="last_name" value="<?php echo sanitize($record['last_name']); ?>" required></label>
        <label>Email Address: <input type="email" name="email" value="<?php echo sanitize($record['email']); ?>" required></label>
        <label>Contact Number: <input type="text" name="contact_number" value="<?php echo sanitize($record['contact_number']); ?>" required></label>
        <label>Address: <textarea name="address" rows="3" required><?php echo sanitize($record['address']); ?></textarea></label>
        <label>Educational Attainment: <input type="text" name="educational_attainment" value="<?php echo sanitize($record['educational_attainment']); ?>" required></label>
        <label>Years of Programming Experience: <input type="number" name="years_experience" min="0" value="<?php echo (int)$record['years_experience']; ?>" required></label>
        <label>Technical Skills: <textarea name="technical_skills" rows="3" required><?php echo sanitize($record['technical_skills']); ?></textarea></label>
        <p>Date Added: <?php echo sanitize($record['date_added']); ?> (not editable)</p>
        <br>
        <button type="submit" name="update_application">Update Application</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
