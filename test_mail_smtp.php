<?php
require_once 'config.php';

// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require_once 'PHPMailer/PHPMailer-6.8.0/src/PHPMailer.php';
require_once 'PHPMailer/PHPMailer-6.8.0/src/SMTP.php';
require_once 'PHPMailer/PHPMailer-6.8.0/src/Exception.php';

function sendEmailSMTP($to, $subject, $html) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}

// Test configuration
$testEmail = 'shivam.it1311@gmail.com';
$testSubject = 'Test Email - India Day Trip SMTP System';
$testHtml = '
<html>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #1CA8CB; text-align: center;">Test Email - India Day Trip</h2>
        <div style="background: #e8f4f8; padding: 15px; border-left: 4px solid #1CA8CB; margin: 20px 0;">
            <h3 style="margin: 0 0 10px 0; color: #113D48;">SMTP Mail System Test</h3>
            <p style="margin: 0;">This is a test email to verify that your SMTP mail system is working correctly.</p>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">Test Date:</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . date('Y-m-d H:i:s') . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">Server:</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . $_SERVER['HTTP_HOST'] ?? 'Unknown' . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">SMTP Host:</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . SMTP_HOST . ':' . SMTP_PORT . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">From Email:</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . SMTP_FROM_EMAIL . '</td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #113D48; color: white; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px;">If you receive this email, your SMTP mail system is working correctly!</p>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.8;">India Day Trip SMTP Mail System Test</p>
        </div>
    </div>
</body>
</html>';

echo "<h1>Testing SMTP Email System</h1>";
echo "<p>Sending test email to: <strong>" . htmlspecialchars($testEmail) . "</strong></p>";

// Check configuration first
echo "<h2>Configuration Check:</h2>";
echo "<ul>";
echo "<li>SMTP_HOST: " . SMTP_HOST . "</li>";
echo "<li>SMTP_PORT: " . SMTP_PORT . "</li>";
echo "<li>SMTP_USERNAME: " . SMTP_USERNAME . "</li>";
echo "<li>SMTP_PASSWORD: " . (SMTP_PASSWORD === 'your_app_password' ? '<span style="color: red;">NEEDS APP PASSWORD</span>' : '<span style="color: green;">Set</span>') . "</li>";
echo "<li>SMTP_FROM_EMAIL: " . SMTP_FROM_EMAIL . "</li>";
echo "</ul>";

echo "<h2>Gmail Setup Instructions:</h2>";
echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>Important: Use Gmail App Password</h3>";
echo "<ol>";
echo "<li>Go to your Google Account settings</li>";
echo "<li>Enable 2-Step Verification if not already enabled</li>";
echo "<li>Go to Security > App Passwords</li>";
echo "<li>Generate a new app password for 'Mail'</li>";
echo "<li>Use this 16-character password in SMTP_PASSWORD</li>";
echo "</ol>";
echo "</div>";

// Send test email
echo "<h2>Sending Test Email...</h2>";
$result = sendEmailSMTP($testEmail, $testSubject, $testHtml);

if ($result['success']) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Success!</h3>";
    echo "<p>" . htmlspecialchars($result['message']) . "</p>";
    echo "<p>Check your inbox at " . htmlspecialchars($testEmail) . "</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Error!</h3>";
    echo "<p>Failed to send email: " . htmlspecialchars($result['error']) . "</p>";
    echo "</div>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If successful, check your email inbox</li>";
echo "<li>If failed, set up Gmail App Password as shown above</li>";
echo "<li>Update SMTP_PASSWORD in config.php with the app password</li>";
echo "<li>Run this test again</li>";
echo "</ol>";

echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
