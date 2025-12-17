<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Chowdhury Automobile</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light vh-100 d-flex align-items-center justify-content-center">

    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="mb-4 text-danger">
                    <i class="bi bi-car-front-fill" style="font-size: 5rem;"></i>
                </div>

                <h1 class="display-1 fw-bold text-dark">404</h1>
                <h2 class="h4 text-uppercase fw-bold text-secondary mb-3">Page Not Found</h2>
                
                <p class="text-muted mb-4">
                    Sorry, the page you are looking for is not available. It might have been moved or deleted.
                </p>

                <a href="index.php" class="btn btn-danger btn-lg px-4 rounded-pill shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Back to Homepage
                </a>
                
                <div class="mt-5 text-muted small">
                    &copy; <?php echo date("Y"); ?> Chowdhury Automobile
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>