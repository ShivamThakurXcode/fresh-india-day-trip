<?php
require_once '../config.php';

header('Content-Type: application/json');

// EMERGENCY RATE LIMITING - Block DDoS attacks
function checkRateLimit($ip, $email = null) {
    $rateFile = '../rate_limit.json';
    $limits = [];
    
    // Try to read existing limits
    if (file_exists($rateFile)) {
        $json = file_get_contents($rateFile);
        if ($json !== false) {
            $limits = json_decode($json, true) ?: [];
        }
    }
    
    $now = time();
    $key = $email ? "$ip|$email" : "$ip|ip_only";
    
    // Clean old entries (older than 1 hour)
    $limits = array_filter($limits, function($data) use ($now) {
        return $now - $data['time'] < 3600;
    });
    
    // Check if IP is blocked
    if (isset($limits[$key]) && $limits[$key]['count'] >= 3) {
        http_response_code(429);
        echo json_encode(['success' => false, 'error' => 'Rate limit exceeded. Please try again later.']);
        exit;
    }
    
    // Update counter
    if (!isset($limits[$key])) {
        $limits[$key] = ['count' => 1, 'time' => $now];
    } else {
        $limits[$key]['count']++;
    }
    
    // Save back to file
    file_put_contents($rateFile, json_encode($limits));
    return true;
}

// Get client IP
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Block high-risk countries during attack
$blocked_countries = ['US', 'BR', 'MX', 'SA']; // Based on your analytics
$country_code = file_get_contents("http://ip-api.com/json/$ip?fields=countryCode");
if ($country_code !== false) {
    $country_data = json_decode($country_code, true);
    if (in_array($country_data['countryCode'] ?? '', $blocked_countries)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }
}

// Apply rate limiting immediately
checkRateLimit($ip);

function sendEmailResend($to, $subject, $html) {
    $apiKey = defined('RESEND_API_KEY') ? RESEND_API_KEY : getenv('RESEND_API_KEY');
    
    if (empty($apiKey)) {
        return ['success' => false, 'error' => 'RESEND_API_KEY not configured'];
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

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);
    
    if ($httpCode >= 200 && $httpCode < 300 && isset($result['id'])) {
        return ['success' => true, 'id' => $result['id']];
    }
    
    return ['success' => false, 'error' => $result['message'] ?? 'Unknown error', 'http_code' => $httpCode];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$honeypot = $_POST['website_url'] ?? '';
if (!empty($honeypot)) {
    echo json_encode(['success' => true, 'message' => 'Message received']);
    exit;
}

// Enhanced CAPTCHA validation
$captcha = $_POST['captcha'] ?? '';
$formTime = $_SESSION['form_time'] ?? 0;
$captchaAnswer = $_SESSION['captcha_answer'] ?? null;

// Check if CAPTCHA is correct
if (!$captchaAnswer || $captcha != $captchaAnswer) {
    echo json_encode(['success' => false, 'error' => 'Incorrect security answer']);
    exit;
}

// Check form submission time (prevent instant bot submissions)
if (time() - $formTime < 3) {
    echo json_encode(['success' => false, 'error' => 'Form submitted too quickly']);
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrfToken)) {
    echo json_encode(['success' => false, 'error' => 'Invalid token']);
    exit;
}

$formType = $_POST['form_type'] ?? 'booking';
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$tourName = trim($_POST['tour_name'] ?? '');
$travelDate = trim($_POST['travel_date'] ?? '');
$guests = trim($_POST['guests'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($firstName) || empty($lastName) || empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Required fields missing']);
    exit;
}

$adminEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'indiadaytrip@gmail.com';

$emailHtml = "
<html>
<body style='font-family: Arial, sans-serif; padding: 20px;'>
    <h2 style='color: #113D48;'>New Booking Request</h2>
    <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
        <tr style='background: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Name</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($firstName . ' ' . $lastName) . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Email</td>
            <td style='padding: 10px; border: 1px solid #ddd;'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></td>
        </tr>
        <tr style='background: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Phone</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($phone ?: 'Not provided') . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Tour</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($tourName ?: 'Not specified') . "</td>
        </tr>
        <tr style='background: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Travel Date</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($travelDate ?: 'Not specified') . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Guests</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($guests ?: 'Not specified') . "</td>
        </tr>
        <tr style='background: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold; vertical-align: top;'>Message</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . nl2br(htmlspecialchars($message ?: 'No message')) . "</td>
        </tr>
    </table>
</body>
</html>";

$result = sendEmailResend($adminEmail, 'New Booking - India Day Trip', $emailHtml);

if ($result['success']) {
    $confirmationHtml = "
    <html>
    <body style='font-family: Arial, sans-serif; padding: 20px;'>
        <h2 style='color: #113D48;'>Thank You for Your Booking Request!</h2>
        <p>Dear " . htmlspecialchars($firstName) . ",</p>
        <p>We have received your booking request. Our team will contact you within 24 hours.</p>
        <p><strong>Your Details:</strong></p>
        <ul>
            <li>Tour: " . htmlspecialchars($tourName ?: 'Not specified') . "</li>
            <li>Travel Date: " . htmlspecialchars($travelDate ?: 'Not specified') . "</li>
            <li>Guests: " . htmlspecialchars($guests ?: 'Not specified') . "</li>
        </ul>
        <p>Best regards,<br>India Day Trip Team</p>
    </body>
    </html>";
    
    sendEmailResend($email, 'Booking Received - India Day Trip', $confirmationHtml);
    
    echo json_encode(['success' => true, 'message' => 'Booking submitted successfully']);
} else {
    echo json_encode(['success' => false, 'error' => $result['error']]);
}