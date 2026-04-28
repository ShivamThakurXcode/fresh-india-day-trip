<?php
session_start();
require_once '../config.php';
checkAdminLogin();

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (!$type || !$id) {
    echo 'Invalid request.';
    exit;
}

try {
    if ($type === 'contact') {
        $stmt = $pdo->prepare("SELECT * FROM contact_inquiries WHERE id = ?");
        $stmt->execute([$id]);
        $inquiry = $stmt->fetch();
        
        if (!$inquiry) {
            echo 'Inquiry not found.';
            exit;
        }
        
        // Update status to read if pending
        if ($inquiry['status'] === 'pending') {
            $pdo->prepare("UPDATE contact_inquiries SET status = 'read' WHERE id = ?")->execute([$id]);
        }
        
        echo '<table class="table table-bordered">';
        echo '<tr><th>Name</th><td>' . htmlspecialchars($inquiry['name']) . '</td></tr>';
        echo '<tr><th>Email</th><td>' . htmlspecialchars($inquiry['email']) . '</td></tr>';
        echo '<tr><th>Phone</th><td>' . htmlspecialchars($inquiry['phone'] ?: '-') . '</td></tr>';
        echo '<tr><th>Subject</th><td>' . htmlspecialchars($inquiry['subject']) . '</td></tr>';
        echo '<tr><th>Status</th><td>' . ucfirst($inquiry['status']) . '</td></tr>';
        echo '<tr><th>IP Address</th><td>' . htmlspecialchars($inquiry['ip_address'] ?: '-') . '</td></tr>';
        echo '<tr><th>Submitted</th><td>' . date('Y-m-d H:i:s', strtotime($inquiry['created_at'])) . '</td></tr>';
        echo '<tr><th>Message</th><td>' . nl2br(htmlspecialchars($inquiry['message'])) . '</td></tr>';
        echo '</table>';
        
    } elseif ($type === 'booking') {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            echo 'Booking not found.';
            exit;
        }
        
        // Update status to read if pending
        if ($booking['status'] === 'pending') {
            $pdo->prepare("UPDATE bookings SET status = 'read' WHERE id = ?")->execute([$id]);
        }
        
        echo '<table class="table table-bordered">';
        echo '<tr><th>First Name</th><td>' . htmlspecialchars($booking['first_name']) . '</td></tr>';
        echo '<tr><th>Last Name</th><td>' . htmlspecialchars($booking['last_name']) . '</td></tr>';
        echo '<tr><th>Email</th><td>' . htmlspecialchars($booking['email']) . '</td></tr>';
        echo '<tr><th>Phone</th><td>' . htmlspecialchars($booking['phone'] ?: '-') . '</td></tr>';
        echo '<tr><th>Tour Name</th><td>' . htmlspecialchars($booking['tour_name'] ?: 'Not specified') . '</td></tr>';
        echo '<tr><th>Tour Type</th><td>' . htmlspecialchars($booking['tour_type'] ?: '-') . '</td></tr>';
        echo '<tr><th>Travel Date</th><td>' . htmlspecialchars($booking['travel_date'] ?: '-') . '</td></tr>';
        echo '<tr><th>Adults</th><td>' . htmlspecialchars($booking['adults'] ?: '-') . '</td></tr>';
        echo '<tr><th>Children</th><td>' . htmlspecialchars($booking['children'] ?: '0') . '</td></tr>';
        echo '<tr><th>Special Requests</th><td>' . nl2br(htmlspecialchars($booking['special_requests'] ?: 'None')) . '</td></tr>';
        echo '<tr><th>Status</th><td>' . ucfirst($booking['status']) . '</td></tr>';
        echo '<tr><th>IP Address</th><td>' . htmlspecialchars($booking['ip_address'] ?: '-') . '</td></tr>';
        echo '<tr><th>Submitted</th><td>' . date('Y-m-d H:i:s', strtotime($booking['created_at'])) . '</td></tr>';
        echo '</table>';
        
    } else {
        echo 'Invalid type.';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
