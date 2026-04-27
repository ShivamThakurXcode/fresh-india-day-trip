<?php
require_once '../config.php';

session_start();

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSRF Token Generation
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Verification
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Load .env file
$envFile = '../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $envLines = explode("\n", $envContent);
    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// OPTIMIZED RATE LIMITING - Memory-based with periodic cleanup
function checkRateLimit($ip) {
    static $rateCache = null;
    static $lastCleanup = 0;
    $now = time();
    
    // Initialize cache if needed
    if ($rateCache === null) {
        $rateFile = '../rate_limit_mail.json';
        if (file_exists($rateFile)) {
            $json = @file_get_contents($rateFile);
            $rateCache = $json ? @json_decode($json, true) : [];
        } else {
            $rateCache = [];
        }
        $lastCleanup = $now;
    }
    
    // Cleanup old entries every 5 minutes
    if ($now - $lastCleanup > 300) {
        foreach ($rateCache as $k => $data) {
            if ($now - $data['time'] > 300) {
                unset($rateCache[$k]);
            }
        }
        $lastCleanup = $now;
        // Write cleanup to file
        @file_put_contents('../rate_limit_mail.json', json_encode($rateCache), LOCK_EX);
    }
    
    // Check rate limit: 3 requests per 5 minutes per IP
    if (isset($rateCache[$ip])) {
        if ($rateCache[$ip]['count'] >= 3) {
            http_response_code(429);
            echo json_encode(['success' => false, 'error' => 'Too many requests. Please try again in 5 minutes.']);
            exit;
        }
        $rateCache[$ip]['count']++;
    } else {
        $rateCache[$ip] = ['count' => 1, 'time' => $now];
    }
}

// Enhanced bot detection with multiple checks
function blockBots() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'] ?? '';
    
    // Block non-POST requests early
    if ($method !== 'POST') {
        http_response_code(405);
        exit('Method not allowed');
    }
    
    // Block common bot user agents
    $botPatterns = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 
        'scrapy', 'java', 'perl', 'ruby', 'go-http', 'aiohttp', 'python-requests',
        'bingbot', 'googlebot', 'facebookexternalhit', 'meta-externalagent',
        'slurp', 'msnbot', 'teoma', 'yandex', 'httpclient', 'okhttp',
        'postman', 'insomnia', 'httpie', 'axios', 'fetch'
    ];
    
    foreach ($botPatterns as $pattern) {
        if (stripos($userAgent, $pattern) !== false) {
            http_response_code(403);
            exit('Access denied');
        }
    }
    
    // Block requests without proper browser headers
    if (empty($userAgent) || empty($accept)) {
        http_response_code(403);
        exit('Access denied');
    }
    
    // Validate Accept header - should contain text/html or application/json
    if (!preg_match('/text\/html|application\/json/i', $accept)) {
        http_response_code(403);
        exit('Access denied');
    }
    
    // Block requests from suspicious IPs (from your logs)
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $blockedIPs = ['191.96.168.95'];
    if (in_array($ip, $blockedIPs)) {
        http_response_code(403);
        exit('Access denied');
    }
    
    // Check for suspicious request patterns
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, '../') !== false || strpos($requestUri, '://') !== false) {
        http_response_code(403);
        exit('Access denied');
    }
}

blockBots();

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
checkRateLimit($ip);

// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require_once '../PHPMailer/PHPMailer-6.8.0/src/PHPMailer.php';
require_once '../PHPMailer/PHPMailer-6.8.0/src/SMTP.php';
require_once '../PHPMailer/PHPMailer-6.8.0/src/Exception.php';

function sendEmailSMTP($to, $subject, $html) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_EMAIL'] ?? 'indiadaytrip@gmail.com';
        $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? 'your_app_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom($_ENV['SMTP_EMAIL'] ?? 'indiadaytrip@gmail.com', 'India Day Trip');
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

// POST method is already enforced in blockBots() function

$honeypot = $_POST['website_url'] ?? '';
if (!empty($honeypot)) {
    exit;  // Silently exit - don't send anything to bots
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

// Strict input validation and sanitization
function validateInput($data, $type, $maxLength = 255) {
    $data = trim($data);
    
    // Check length
    if (strlen($data) > $maxLength) {
        return false;
    }
    
    // Check for null bytes and control characters
    if (strpos($data, "\0") !== false || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $data)) {
        return false;
    }
    
    switch ($type) {
        case 'name':
            // Allow letters, spaces, hyphens, apostrophes only
            return preg_match('/^[a-zA-Z\s\-\'\.]{1,50}$/', $data);
            
        case 'email':
            // Strict email validation
            return filter_var($data, FILTER_VALIDATE_EMAIL) && 
                   preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data);
            
        case 'phone':
            // Allow international phone numbers with +, digits, spaces, hyphens, parentheses
            return preg_match('/^[\+]?[0-9\s\-\(\)]{7,20}$/', $data);
            
        case 'text':
            // General text - allow most characters but limit dangerous ones
            return !preg_match('/<script|javascript:|on\w+=/i', $data);
            
        case 'alphanumeric':
            // Letters and numbers only
            return preg_match('/^[a-zA-Z0-9\s\-\'\.,]+$/', $data);
            
        default:
            return false;
    }
}

// Validate and sanitize all inputs
$formType = isset($_POST['form_type']) && in_array($_POST['form_type'], ['booking', 'contact']) ? $_POST['form_type'] : 'booking';

$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$tourName = $_POST['tour_name'] ?? '';
$travelDate = $_POST['travel_date'] ?? '';
$guests = $_POST['guests'] ?? '';
$message = $_POST['message'] ?? '';

// Validate required fields
$errors = [];
if (!validateInput($firstName, 'name')) {
    $errors[] = 'Invalid first name';
}
if (!validateInput($lastName, 'name')) {
    $errors[] = 'Invalid last name';
}
if (!validateInput($email, 'email')) {
    $errors[] = 'Invalid email address';
}

// Validate optional fields
if (!empty($phone) && !validateInput($phone, 'phone')) {
    $errors[] = 'Invalid phone number';
}
if (!empty($tourName) && !validateInput($tourName, 'text', 100)) {
    $errors[] = 'Invalid tour name';
}
if (!empty($travelDate)) {
    // Validate date format (YYYY-MM-DD or DD/MM/YYYY)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$|^\d{2}\/\d{2}\/\d{4}$/', $travelDate)) {
        $errors[] = 'Invalid travel date format';
    }
}
if (!empty($guests) && (!is_numeric($guests) || $guests < 1 || $guests > 50)) {
    $errors[] = 'Invalid number of guests';
}
if (!empty($message) && !validateInput($message, 'text', 1000)) {
    $errors[] = 'Invalid message content';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => 'Validation failed: ' . implode(', ', $errors)]);
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

$result = sendEmailSMTP($adminEmail, 'New Booking - India Day Trip', $emailHtml);

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
    
    sendEmailSMTP($email, 'Booking Received - India Day Trip', $confirmationHtml);
    
    echo json_encode(['success' => true, 'message' => 'Booking submitted successfully']);
} else {
    echo json_encode(['success' => false, 'error' => $result['error']]);
}