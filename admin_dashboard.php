<?php
/**
 * Admin Dashboard
 * IPSC 2026 Registration System
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit;
}

require_once 'config.php';
require_once 'Database.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.html');
    exit;
}

// Get filter parameters
$searchTerm = $_GET['search'] ?? '';
$filterType = $_GET['type'] ?? '';
$filterPayment = $_GET['payment'] ?? '';

// Build query
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

$sql .= " ORDER BY registration_date DESC";

$stmt = $db->query($sql, $params);
$registrations = $stmt->fetchAll();

// Get statistics
$statsStmt = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN payment_status = 'Already Paid' THEN 1 ELSE 0 END) as paid,
    SUM(CASE WHEN payment_status = 'On Spot' THEN 1 ELSE 0 END) as on_spot,
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }
        
        .header {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 1.5rem;
            color: white;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.65rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .stat-card h3 {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #f472b6;
        }
        
        .filters {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filters-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }
        
        .filter-group label {
            display: block;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 0.95rem;
        }
        
        .filter-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
        }
        
        .table-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: rgba(99, 102, 241, 0.2);
        }
        
        th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: white;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            color: #cbd5e1;
        }
        
        tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }
        
        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }
        
        .export-btn {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            padding: 0.65rem 1.5rem;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .filters-row {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 0.85rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
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
            <div class="stat-card">
                <h3>Paid</h3>
                <div class="value"><?php echo $stats['paid']; ?></div>
            </div>
            <div class="stat-card">
                <h3>On Spot</h3>
                <div class="value"><?php echo $stats['on_spot']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Amount</h3>
                <div class="value">Rs. <?php echo number_format($stats['total_amount'], 2); ?></div>
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
                        <label>Registration Type</label>
                        <select name="type">
                            <option value="">All Types</option>
                            <option value="Students/Research Scholars (Indian)" <?php echo $filterType === 'Students/Research Scholars (Indian)' ? 'selected' : ''; ?>>Students (Indian)</option>
                            <option value="Faculty/Scientists/Research Staffs/Professionals/Experts (Indian)" <?php echo $filterType === 'Faculty/Scientists/Research Staffs/Professionals/Experts (Indian)' ? 'selected' : ''; ?>>Faculty (Indian)</option>
                            <option value="Students/Research Scholars/Faculty/Scientists/Research Staffs (Foreign)" <?php echo $filterType === 'Students/Research Scholars/Faculty/Scientists/Research Staffs (Foreign)' ? 'selected' : ''; ?>>Foreign</option>
                            <option value="Industry" <?php echo $filterType === 'Industry' ? 'selected' : ''; ?>>Industry</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Payment Status</label>
                        <select name="payment">
                            <option value="">All Status</option>
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
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="8" class="no-results">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                No registrations found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><?php echo $reg['id']; ?></td>
                                <td><?php echo htmlspecialchars($reg['prefix'] . ' ' . $reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                <td><?php echo htmlspecialchars($reg['registration_type']); ?></td>
                                <td>
                                    <span class="badge <?php echo $reg['payment_status'] === 'Already Paid' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $reg['payment_status']; ?>
                                    </span>
                                </td>
                                <td>Rs. <?php echo number_format($reg['amount_transferred'], 2); ?></td>
                                <td><?php echo date('d M Y', strtotime($reg['registration_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>