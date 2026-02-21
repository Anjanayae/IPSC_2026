<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.html');
    exit;
}

require_once 'config.php';
require_once 'Database.php';

$searchTerm = $_GET['search'] ?? '';
$filterType = $_GET['type'] ?? '';
$filterPayment = $_GET['payment'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT * FROM registrations WHERE 1=1";
$params = [];

if (!empty($searchTerm)) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params = array_fill(0, 4, $searchParam);
}

if (!empty($filterType)) {
    $sql .= " AND registration_type = ?";
    $params[] = $filterType;
}

if (!empty($filterPayment)) {
    $sql .= " AND payment_status = ?";
    $params[] = $filterPayment;
}

if (!empty($filterStatus)) {
    $sql .= " AND approval_status = ?";
    $params[] = $filterStatus;
}

$sql .= " ORDER BY registration_date DESC";

$stmt = $db->query($sql, $params);
$registrations = $stmt->fetchAll();

$statsStmt = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN payment_status = 'Already Paid' THEN 1 ELSE 0 END) as paid,
    SUM(CASE WHEN payment_status = 'On Spot' THEN 1 ELSE 0 END) as on_spot,
    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(amount_transferred) as total_amount
FROM registrations");
$stats = $statsStmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IPSC 2026</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-dashboard.css">
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-tachometer-alt"></i> Registration Dashboard</h1>
        <a href="?logout=1" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Registrations</h3>
                <div class="value"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card pending-card">
                <h3>Pending Review</h3>
                <div class="value"><?php echo $stats['pending']; ?></div>
            </div>
            <div class="stat-card approved-card">
                <h3>Approved</h3>
                <div class="value"><?php echo $stats['approved']; ?></div>
            </div>
            <div class="stat-card rejected-card">
                <h3>Rejected</h3>
                <div class="value"><?php echo $stats['rejected']; ?></div>
            </div>
        </div>
        
        <div class="filters">
            <form method="GET">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" name="search" placeholder="Name, email, or phone" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $filterStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $filterStatus === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $filterStatus === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Payment</label>
                        <select name="payment">
                            <option value="">All</option>
                            <option value="Already Paid" <?php echo $filterPayment === 'Already Paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="On Spot" <?php echo $filterPayment === 'On Spot' ? 'selected' : ''; ?>>On Spot</option>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        
        <a href="export_registrations.php" class="export-btn">
            <i class="fas fa-download"></i> Export to CSV
        </a>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="8" class="no-results">No registrations found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $reg): ?>
                            <tr data-id="<?php echo $reg['id']; ?>">
                                <td><?php echo str_pad($reg['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($reg['prefix'] . ' ' . $reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                <td><?php echo htmlspecialchars($reg['registration_type']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $reg['payment_status'] === 'Already Paid' ? 'success' : 'warning'; ?>">
                                        <?php echo $reg['payment_status']; ?>
                                    </span>
                                </td>
                                <td>Rs. <?php echo number_format($reg['amount_transferred'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $reg['approval_status'] === 'approved' ? 'success' : 
                                            ($reg['approval_status'] === 'rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo strtoupper($reg['approval_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($reg['approval_status'] === 'pending'): ?>
                                        <button class="action-btn approve-btn" onclick="approveRegistration(<?php echo $reg['id']; ?>)">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="action-btn reject-btn" onclick="rejectRegistration(<?php echo $reg['id']; ?>)">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    <?php else: ?>
                                        <span class="processed-text">Processed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h3>Reject Registration</h3>
            <p>Please provide a reason for rejection:</p>
            <textarea id="rejectReason" rows="4" placeholder="Enter reason..."></textarea>
            <div class="modal-actions">
                <button class="modal-btn cancel-btn" onclick="closeRejectModal()">Cancel</button>
                <button class="modal-btn confirm-btn" onclick="confirmReject()">Confirm Reject</button>
            </div>
        </div>
    </div>
    
    <script>
        let currentRejectId = null;
        
        async function approveRegistration(id) {
            if (!confirm('Are you sure you want to approve this registration?')) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'approve');
                formData.append('registration_id', id);
                
                const response = await fetch('admin_action.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Registration approved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }
        
        function rejectRegistration(id) {
            currentRejectId = id;
            document.getElementById('rejectModal').style.display = 'flex';
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejectReason').value = '';
            currentRejectId = null;
        }
        
        async function confirmReject() {
            const reason = document.getElementById('rejectReason').value.trim();
            
            if (!reason) {
                alert('Please provide a reason for rejection');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'reject');
                formData.append('registration_id', currentRejectId);
                formData.append('reason', reason);
                
                const response = await fetch('admin_action.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Registration rejected');
                    closeRejectModal();
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }
    </script>
</body>
</html>