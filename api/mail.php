<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../php_errors.log');

require_once '../config.php';

session_start();

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

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
    
    // Block common bot user agents (relaxed - removed 'fetch' and 'axios' which are used by legitimate JS)
    $botPatterns = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 
        'scrapy', 'java', 'perl', 'ruby', 'go-http', 'aiohttp', 'python-requests',
        'bingbot', 'googlebot', 'facebookexternalhit', 'meta-externalagent',
        'slurp', 'msnbot', 'teoma', 'yandex', 'httpclient', 'okhttp',
        'postman', 'insomnia', 'httpie'
    ];
    
    foreach ($botPatterns as $pattern) {
        if (stripos($userAgent, $pattern) !== false) {
            http_response_code(403);
            exit('Access denied');
        }
    }
    
    // Only block if completely empty user agent (relaxed - allow empty accept header)
    if (empty($userAgent)) {
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
$subject = $_POST['subject'] ?? '';

// Validate required fields
$errors = [];
if ($formType === 'contact') {
    // Contact form uses single name field
    $fullName = $_POST['name'] ?? '';
    if (!validateInput($fullName, 'name')) {
        $errors[] = 'Invalid name';
    }
} else {
    // Booking form uses first_name and last_name
    if (!validateInput($firstName, 'name')) {
        $errors[] = 'Invalid first name';
    }
    if (!validateInput($lastName, 'name')) {
        $errors[] = 'Invalid last name';
    }
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
if (!empty($subject) && !validateInput($subject, 'text', 255)) {
    $errors[] = 'Invalid subject';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => 'Validation failed: ' . implode(', ', $errors)]);
    exit;
}

try {
    if ($formType === 'contact') {
        // Store contact inquiry
        $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $phone, $subject, $message, $ip]);
    } else {
        // Store booking inquiry
        // Parse guests into adults and children if needed
        $adults = !empty($guests) ? (int)$guests : null;
        $children = 0;
        
        // Format travel date to YYYY-MM-DD if in DD/MM/YYYY format
        if (!empty($travelDate) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $travelDate)) {
            $parts = explode('/', $travelDate);
            $travelDate = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        
        $stmt = $pdo->prepare("INSERT INTO bookings (first_name, last_name, email, phone, tour_name, travel_date, adults, children, special_requests, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $phone, $tourName, $travelDate, $adults, $children, $message, $ip]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Form submitted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
