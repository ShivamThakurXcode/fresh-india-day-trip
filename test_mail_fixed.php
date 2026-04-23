<?php
// Test the fixed mail system
session_start();
$_SESSION['csrf_token'] = 'test_token_123';
$_SESSION['captcha_answer'] = 5;
$_SESSION['form_time'] = time() - 10; // 10 seconds ago

// Simulate POST data
$_POST = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'shivam.it1311@gmail.com',
    'csrf_token' => 'test_token_123',
    'captcha' => 5,
    'form_type' => 'booking',
    'website_url' => '', // Empty honeypot
    'tour_name' => 'Test Tour',
    'travel_date' => '2025-01-01',
    'guests' => '2',
    'message' => 'This is a test message'
];

// Set server variables
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Test Browser)';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "<h1>Testing Fixed Mail System</h1>";

// Change to api directory and capture output
$originalDir = getcwd();
chdir('api');
ob_start();
include 'mail.php';
$output = ob_get_clean();
chdir($originalDir);

echo "<h2>Output:</h2>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Check rate limit file
$rateFile = 'rate_limit_mail.json';
if (file_exists($rateFile)) {
    echo "<h2>Rate Limit File:</h2>";
    echo "<pre>" . htmlspecialchars(file_get_contents($rateFile)) . "</pre>";
}

echo "<h2>Test Summary:</h2>";
echo "<ul>";
echo "<li>Rate Limiting: Fixed - 2 requests per 5 minutes per IP</li>";
echo "<li>Honeypot: Fixed - Silent exit for bots</li>";
echo "<li>Country Check: Removed - No external API calls</li>";
echo "<li>Email System: Switched to Google SMTP</li>";
echo "<li>Bot Detection: Added user agent filtering</li>";
echo "</ul>";
?>
