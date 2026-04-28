<?php
ob_start();
session_start();
require_once '../config.php';
checkAdminLogin();

// Ensure PDO throws exceptions so errors aren't silent
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 $message = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    try {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Security validation failed. Please try again.');
        }
        
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? 'pending';
        
        if (!in_array($status, ['pending', 'read', 'processed', 'completed'])) {
            throw new Exception('Invalid status value.');
        }
        
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $_SESSION['message'] = 'Status updated successfully!';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
    }
    header('Location: bookings.php');
    exit;
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        if (!verifyCSRFToken($_GET['csrf_token'] ?? '')) {
            throw new Exception('Security validation failed. Please try again.');
        }
        
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['message'] = 'Booking deleted successfully!';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
    }
    header('Location: bookings.php');
    exit;
}

// Get filters
 $statusFilter = $_GET['status'] ?? '';
 $searchFilter = $_GET['search'] ?? '';

// Build query
 $where = ['1=1'];
 $params = [];

if ($statusFilter) {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}

if ($searchFilter) {
    $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR tour_name LIKE ?)';
    $searchParam = '%' . $searchFilter . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

 $whereClause = implode(' AND ', $where);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Inquiries - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-pending { background-color: #ffc107; color: #000; }
        .status-read { background-color: #17a2b8; color: #fff; }
        .status-processed { background-color: #007bff; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .modal { z-index: 10000 !important; }
        .modal-backdrop { z-index: 9999 !important; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <h1 class="page-title">Booking Inquiries</h1>
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read</option>
                                <option value="processed" <?php echo $statusFilter === 'processed' ? 'selected' : ''; ?>>Processed</option>
                                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search name, email, tour..." value="<?php echo htmlspecialchars($searchFilter); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-3">
                            <a href="bookings.php" class="btn btn-secondary w-100">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Export Button -->
            <div class="mb-3">
                <a href="export-csv.php?type=bookings<?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchFilter ? '&search=' . urlencode($searchFilter) : ''; ?>" class="btn btn-success">
                    <i class="fas fa-file-csv"></i> Export to CSV
                </a>
            </div>
            
            <!-- Stats -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Total</h5>
                            <h3><?php echo $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Pending</h5>
                            <h3><?php echo $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Processed</h5>
                            <h3><?php echo $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'processed'")->fetchColumn(); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Completed</h5>
                            <h3><?php echo $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn(); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Tour</th>
                        <th>Travel Date</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentPage = max(1, (int)($_GET['page'] ?? 1));
                    $perPage = 20;
                    
                    // Count total items
                    $countQuery = "SELECT COUNT(*) FROM bookings WHERE $whereClause";
                    $countStmt = $pdo->prepare($countQuery);
                    $countStmt->execute($params);
                    $totalItems = (int)$countStmt->fetchColumn();
                    
                    // Calculate pagination (with fallback if getPaginationInfo is missing or returns bad data)
                    $pagination = function_exists('getPaginationInfo') 
                        ? getPaginationInfo($currentPage, $totalItems, $perPage) 
                        : [];
                    
                    $limit       = (int)($pagination['per_page']   ?? $perPage);
                    $offset      = (int)($pagination['offset']     ?? (($currentPage - 1) * $perPage));
                    $totalPages  = (int)($pagination['total_pages'] ?? max(1, ceil($totalItems / $perPage)));
                    $hasPrevious = $pagination['has_previous'] ?? ($currentPage > 1);
                    $hasNext     = $pagination['has_next']     ?? ($currentPage < $totalPages);
                    
                    // --- THE KEY FIX: bind LIMIT and OFFSET as PDO::PARAM_INT ---
                    $query = "SELECT * FROM bookings WHERE $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
                    $stmt = $pdo->prepare($query);
                    
                    // Bind WHERE clause params (strings are fine here)
                    $paramIndex = 1;
                    foreach ($params as $p) {
                        $stmt->bindValue($paramIndex++, $p);
                    }
                    // Bind LIMIT and OFFSET explicitly as INTEGERS
                    $stmt->bindValue($paramIndex++, $limit,  PDO::PARAM_INT);
                    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
                    
                    $stmt->execute();
                    $csrfToken = urlencode(generateCSRFToken());
                    
                    if ($totalItems === 0) {
                        echo "<tr><td colspan='9' class='text-center'>No bookings found in database.</td></tr>";
                    } elseif ($stmt->rowCount() === 0) {
                        echo "<tr><td colspan='9' class='text-center'>No bookings found for current page. Total: $totalItems, Offset: $offset, Limit: $limit</td></tr>";
                    }
                    
                    while ($row = $stmt->fetch()) {
                        $statusClass = 'status-' . htmlspecialchars($row['status']);
                        $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                        $rawTourName = $row['tour_name'] ?: 'Not specified';
                        $tourName = htmlspecialchars(substr($rawTourName, 0, 30)) . (strlen($rawTourName) > 30 ? '...' : '');
                        echo "<tr>
                            <td>$fullName</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>" . htmlspecialchars($row['phone'] ?: '-') . "</td>
                            <td>$tourName</td>
                            <td>" . htmlspecialchars($row['travel_date'] ?: '-') . "</td>
                            <td>" . htmlspecialchars($row['adults'] ?: '-') . "</td>
                            <td><span class='badge $statusClass'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>
                            <td>" . date('Y-m-d H:i', strtotime($row['created_at'])) . "</td>
                            <td>
                                <button type='button' class='btn btn-sm btn-info' onclick='viewDetails({$row['id']})'>View</button>
                                <button type='button' class='btn btn-sm btn-warning' onclick='updateStatus({$row['id']})'>Status</button>
                                <a href='?action=delete&id={$row['id']}&csrf_token={$csrfToken}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this booking?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Bookings pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo !$hasPrevious ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo max(1, $currentPage - 1); ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchFilter ? '&search=' . urlencode($searchFilter) : ''; ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $p; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchFilter ? '&search=' . urlencode($searchFilter) : ''; ?>"><?php echo $p; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo !$hasNext ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo min($totalPages, $currentPage + 1); ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchFilter ? '&search=' . urlencode($searchFilter) : ''; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- View Details Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="close" onclick="$('#viewModal').modal('hide')">&times;</button>
                </div>
                <div class="modal-body" id="viewModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#viewModal').modal('hide')">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="close" onclick="$('#statusModal').modal('hide')">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" id="statusId">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="read">Read</option>
                                <option value="processed">Processed</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        function viewDetails(id) {
            $.get('get_inquiry.php?type=booking&id=' + id, function(data) {
                $('#viewModalBody').html(data);
                $('#viewModal').modal('show');
            });
        }
        
        function updateStatus(id) {
            $('#statusId').val(id);
            $('#statusModal').modal('show');
        }
    </script>
</body>
</html>