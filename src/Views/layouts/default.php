<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>Shehroz ERP</title>
    <?php echo \App\Core\WhiteLabel::getAssetTags(); ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="active">
            <div class="sidebar-header">
                <img src="<?php echo \App\Core\WhiteLabel::$companyLogo; ?>" alt="<?php echo \App\Core\WhiteLabel::$companyName; ?>" class="logo">
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li>
                    <a href="#purchaseSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-shopping-cart"></i> Purchase Orders
                    </a>
                    <ul class="collapse list-unstyled" id="purchaseSubmenu">
                        <li>
                            <a href="/purchase/create"><i class="fas fa-plus"></i> Create PO</a>
                        </li>
                        <li>
                            <a href="/purchase/list"><i class="fas fa-list"></i> PO List</a>
                        </li>
                        <li>
                            <a href="/purchase/approvals"><i class="fas fa-check"></i> Approvals</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/suppliers"><i class="fas fa-building"></i> Suppliers</a>
                </li>
                <li>
                    <a href="/inventory"><i class="fas fa-boxes"></i> Inventory</a>
                </li>
                <li>
                    <a href="/reports"><i class="fas fa-chart-bar"></i> Reports</a>
                </li>
                <li>
                    <a href="/settings"><i class="fas fa-cog"></i> Settings</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="/profile"><i class="fas fa-id-card me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="container-fluid mt-3">
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <?php echo $viewContent; ?>
            </div>

            <!-- Footer -->
            <footer class="mt-auto py-3 bg-light">
                <div class="container-fluid">
                    <div class="text-center">
                        &copy; <?php echo date('Y'); ?> <?php echo \App\Core\WhiteLabel::$companyName; ?> | Shehroz ERP
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    
    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
</body>
</html>