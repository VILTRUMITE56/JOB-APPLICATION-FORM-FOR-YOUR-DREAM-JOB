<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$messageType = '';

// Handle new application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
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
            $stmt = $pdo->prepare("INSERT INTO applications (first_name, last_name, email, contact_number, address, educational_attainment, years_experience, technical_skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $contact, $address, $education, $years, $skills]);
            $message = 'Application submitted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Failed to submit application. Please try again.';
            $messageType = 'error';
        }
    }
}

// Fetch all applications
try {
    $stmt = $pdo->query("SELECT * FROM applications ORDER BY id DESC");
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
    if ($message === '') {
        $message = 'Failed to load applications.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Software Engineer Job Application System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
        .msg-success { color: green; }
        .msg-error { color: red; }
        label { display: block; margin-top: 8px; }
        input, textarea { width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <h1>Software Engineer Job Application System</h1>

    <?php if ($message !== ''): ?>
        <p class="<?php echo $messageType === 'success' ? 'msg-success' : 'msg-error'; ?>">
            <?php echo sanitize($message); ?>
        </p>
    <?php endif; ?>

    <h2>Submit New Application</h2>
    <form method="post" action="index.php">
        <label>First Name: <input type="text" name="first_name" required></label>
        <label>Last Name: <input type="text" name="last_name" required></label>
        <label>Email Address: <input type="email" name="email" required></label>
        <label>Contact Number: <input type="text" name="contact_number" required></label>
        <label>Address: <textarea name="address" rows="3" required></textarea></label>
        <label>Educational Attainment: <input type="text" name="educational_attainment" required></label>
        <label>Years of Programming Experience: <input type="number" name="years_experience" min="0" required></label>
        <label>Technical Skills: <textarea name="technical_skills" rows="3" required></textarea></label>
        <br>
        <button type="submit" name="submit_application">Submit Application</button>
    </form>

    <h2>Submitted Applications</h2>
    <?php if (empty($applications)): ?>
        <p>No applications found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Education</th>
                <th>Years Exp.</th>
                <th>Technical Skills</th>
                <th>Date Added</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?php echo (int)$app['id']; ?></td>
                    <td><?php echo sanitize($app['first_name']); ?></td>
                    <td><?php echo sanitize($app['last_name']); ?></td>
                    <td><?php echo sanitize($app['email']); ?></td>
                    <td><?php echo sanitize($app['contact_number']); ?></td>
                    <td><?php echo sanitize($app['address']); ?></td>
                    <td><?php echo sanitize($app['educational_attainment']); ?></td>
                    <td><?php echo (int)$app['years_experience']; ?></td>
                    <td><?php echo sanitize($app['technical_skills']); ?></td>
                    <td><?php echo sanitize($app['date_added']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo (int)$app['id']; ?>">Edit</a>
                        <form method="post" action="delete.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this application?');">
                            <input type="hidden" name="id" value="<?php echo (int)$app['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
