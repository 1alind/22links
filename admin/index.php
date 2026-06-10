<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Load products data
$productsFile = __DIR__ . '/../shop/products.json';
$products = [];
if (file_exists($productsFile)) {
    $products = json_decode(file_get_contents($productsFile), true) ?: [];
}

// Load analytics data
$analyticsFile = __DIR__ . '/../data/analytics.json';
$analytics = [];
if (file_exists($analyticsFile)) {
    $analytics = json_decode(file_get_contents($analyticsFile), true) ?: [];
}

// Calculate stats
$totalProducts = count($products);
$visibleProducts = count(array_filter($products, fn($p) => !isset($p['hidden']) || !$p['hidden']));
$hiddenProducts = $totalProducts - $visibleProducts;
$totalViews = array_sum(array_column($analytics, 'views', 0));
$totalOrders = array_sum(array_column($analytics, 'orders', 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>22 Show - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>

<div class="admin-wrapper">
    
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cog"></i>
            <span>22 Show Admin</span>
        </div>
        
        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item active">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="products.php" class="menu-item">
                <i class="fas fa-box"></i>
                <span>Products</span>
                <span class="badge"><?php echo $totalProducts; ?></span>
            </a>
            <a href="analytics.php" class="menu-item">
                <i class="fas fa-chart-pie"></i>
                <span>Analytics</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <button onclick="logout()" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- TOP BAR -->
        <div class="topbar">
            <h1>Dashboard</h1>
            <div class="topbar-right">
                <span class="user-info">
                    <i class="fas fa-user-circle"></i>
                    Admin User
                </span>
            </div>
        </div>
        
        <!-- CONTENT -->
        <div class="dashboard-content">
            
            <!-- STATS GRID -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-products">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Products</span>
                        <span class="stat-value"><?php echo $totalProducts; ?></span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-visible">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Visible Products</span>
                        <span class="stat-value"><?php echo $visibleProducts; ?></span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-hidden">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Hidden Products</span>
                        <span class="stat-value"><?php echo $hiddenProducts; ?></span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-views">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Views</span>
                        <span class="stat-value"><?php echo number_format($totalViews); ?></span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Orders</span>
                        <span class="stat-value"><?php echo number_format($totalOrders); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- QUICK ACTIONS -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <a href="add-product.php" class="action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Product</span>
                    </a>
                    <a href="products.php" class="action-btn">
                        <i class="fas fa-list"></i>
                        <span>Manage Products</span>
                    </a>
                    <a href="analytics.php" class="action-btn">
                        <i class="fas fa-chart-pie"></i>
                        <span>View Analytics</span>
                    </a>
                </div>
            </div>
            
            <!-- RECENT PRODUCTS -->
            <div class="recent-section">
                <h2>Recent Products</h2>
                <?php if (empty($products)): ?>
                    <p class="empty-state">No products yet. <a href="add-product.php">Create one</a></p>
                <?php else: ?>
                    <div class="products-preview">
                        <?php foreach (array_slice($products, -5) as $product): ?>
                            <div class="product-preview">
                                <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>" alt="Product">
                                <div class="product-preview-info">
                                    <h3><?php echo htmlspecialchars($product['title']['english'] ?? 'N/A'); ?></h3>
                                    <p><?php echo htmlspecialchars($product['price'] ?? 'N/A'); ?></p>
                                    <?php if (isset($product['hidden']) && $product['hidden']): ?>
                                        <span class="badge-hidden"><i class="fas fa-eye-slash"></i> Hidden</span>
                                    <?php else: ?>
                                        <span class="badge-visible"><i class="fas fa-eye"></i> Visible</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}
</script>

</body>
</html>