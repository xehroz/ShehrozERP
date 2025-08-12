<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo \App\Core\WhiteLabel::getAssetTags(); ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/auth.css">
    
    <style>
        body {
            height: 100vh;
            background-color: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            max-width: 450px;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .auth-logo {
            max-width: 200px;
            margin: 0 auto 30px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card auth-card bg-white">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="<?php echo \App\Core\WhiteLabel::$companyLogo; ?>" alt="<?php echo \App\Core\WhiteLabel::$companyName; ?>" class="auth-logo">
                        </div>
                        
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['flash_message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                        <?php endif; ?>
                        
                        <?php echo $viewContent; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    &copy; <?php echo date('Y'); ?> <?php echo \App\Core\WhiteLabel::$companyName; ?> | Procurement ERP
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>