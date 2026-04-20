<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        header('Location: index.php?error=1');
        exit;
    }

    // Here you can add code to send email or save to database
    // For now, just redirect with success
    header('Location: index.php?success=1');
    exit;
} else {
    header('Location: index.php');
    exit;
}
?>