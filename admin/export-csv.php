<?php
session_start();
require_once '../config.php';
checkAdminLogin();

$type = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$searchFilter = $_GET['search'] ?? '';

if (!$type || !in_array($type, ['contact_inquiries', 'bookings'])) {
    header('Location: index.php');
    exit;
}

// Build query
$where = ['1=1'];
$params = [];

if ($statusFilter) {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}

if ($searchFilter) {
    if ($type === 'contact_inquiries') {
        $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)';
    } else {
        $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR tour_name LIKE ?)';
    }
    $searchParam = '%' . $searchFilter . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$whereClause = implode(' AND ', $where);

// Get data
if ($type === 'contact_inquiries') {
    $query = "SELECT * FROM contact_inquiries WHERE $whereClause ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    $headers = ['ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'IP Address', 'Created At', 'Updated At'];
    $filename = 'contact_inquiries_' . date('Y-m-d') . '.csv';
} else {
    $query = "SELECT * FROM bookings WHERE $whereClause ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    $headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Tour Type', 'Travel Date', 'Adults', 'Children', 'Special Requests', 'Tour Name', 'Status', 'IP Address', 'Created At', 'Updated At'];
    $filename = 'bookings_' . date('Y-m-d') . '.csv';
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Write BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write headers
fputcsv($output, $headers);

// Write data
foreach ($data as $row) {
    if ($type === 'contact_inquiries') {
        $row_data = [
            $row['id'],
            $row['name'],
            $row['email'],
            $row['phone'] ?? '',
            $row['subject'],
            $row['message'],
            $row['status'],
            $row['ip_address'] ?? '',
            $row['created_at'],
            $row['updated_at']
        ];
    } else {
        $row_data = [
            $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['phone'] ?? '',
            $row['tour_type'] ?? '',
            $row['travel_date'] ?? '',
            $row['adults'] ?? '',
            $row['children'] ?? '',
            $row['special_requests'] ?? '',
            $row['tour_name'] ?? '',
            $row['status'],
            $row['ip_address'] ?? '',
            $row['created_at'],
            $row['updated_at']
        ];
    }
    fputcsv($output, $row_data);
}

fclose($output);
exit;
?>
