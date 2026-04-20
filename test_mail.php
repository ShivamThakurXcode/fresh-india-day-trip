<?php
require_once 'config.php';

// Test email function
function sendEmailResend($to, $subject, $html) {
    $apiKey = defined('RESEND_API_KEY') ? RESEND_API_KEY : getenv('RESEND_API_KEY');
    
    if (empty($apiKey) || $apiKey === 're_YOUR_API_KEY') {
        return ['success' => false, 'error' => 'RESEND_API_KEY not configured or still using placeholder'];
    }

    $data = [
        'from' => defined('RESEND_FROM_EMAIL') ? RESEND_FROM_EMAIL : 'onboarding@resend.dev',
        'to' => [$to],
        'subject' => $subject,
        'html' => $html
    ];

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'error' => 'Curl error: ' . $error];
    }
    
    $result = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300 && isset($result['id'])) {
        return ['success' => true, 'id' => $result['id'], 'response' => $result];
    }
    
    return ['success' => false, 'error' => $result['message'] ?? 'Unknown error', 'http_code' => $httpCode, 'response' => $result];
}

// Test configuration
$testEmail = 'shivam.it1311@gmail.com';
$testSubject = 'Test Email - India Day Trip Mail System';
$testHtml = '
<html>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #1CA8CB; text-align: center;">Test Email - India Day Trip</h2>
        <div style="background: #e8f4f8; padding: 15px; border-left: 4px solid #1CA8CB; margin: 20px 0;">
            <h3 style="margin: 0 0 10px 0; color: #113D48;">Mail System Test</h3>
            <p style="margin: 0;">This is a test email to verify that your mail system is working correctly.</p>
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
                <td style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">API Key Status:</td>
                <td style="padding: 10px; border: 1px solid #ddd;">' . (defined('RESEND_API_KEY') && RESEND_API_KEY !== 're_YOUR_API_KEY' ? 'Configured' : 'Not Configured') . '</td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #113D48; color: white; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px;">If you receive this email, your mail system is working correctly!</p>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.8;">India Day Trip Mail System Test</p>
        </div>
    </div>
</body>
</html>';

echo "<h1>Testing Email System</h1>";
echo "<p>Sending test email to: <strong>" . htmlspecialchars($testEmail) . "</strong></p>";

// Check configuration first
echo "<h2>Configuration Check:</h2>";
echo "<ul>";
echo "<li>RESEND_API_KEY: " . (defined('RESEND_API_KEY') ? (RESEND_API_KEY === 're_YOUR_API_KEY' ? '<span style="color: red;">PLACEHOLDER - needs real key</span>' : '<span style="color: green;">Set</span>') : '<span style="color: red;">Not defined</span>') . "</li>";
echo "<li>RESEND_FROM_EMAIL: " . (defined('RESEND_FROM_EMAIL') ? RESEND_FROM_EMAIL : '<span style="color: red;">Not defined</span>') . "</li>";
echo "<li>ADMIN_EMAIL: " . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '<span style="color: red;">Not defined</span>') . "</li>";
echo "</ul>";

// Send test email
echo "<h2>Sending Test Email...</h2>";
$result = sendEmailResend($testEmail, $testSubject, $testHtml);

if ($result['success']) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Success!</h3>";
    echo "<p>Email sent successfully!</p>";
    echo "<p>Message ID: " . htmlspecialchars($result['id']) . "</p>";
    echo "<p>Check your inbox at " . htmlspecialchars($testEmail) . "</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>Error!</h3>";
    echo "<p>Failed to send email: " . htmlspecialchars($result['error']) . "</p>";
    if (isset($result['http_code'])) {
        echo "<p>HTTP Code: " . htmlspecialchars($result['http_code']) . "</p>";
    }
    if (isset($result['response'])) {
        echo "<p>Response: <pre>" . htmlspecialchars(json_encode($result['response'], JSON_PRETTY_PRINT)) . "</pre></p>";
    }
    echo "</div>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If successful, check your email inbox</li>";
echo "<li>If failed, update your RESEND_API_KEY in config.php</li>";
echo "<li>Get API key from <a href='https://resend.com' target='_blank'>https://resend.com</a></li>";
echo "<li>Verify your sender domain is verified in Resend</li>";
echo "</ol>";

echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
