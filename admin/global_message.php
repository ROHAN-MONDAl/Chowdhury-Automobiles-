<?php
// ---------------------------------------------
// 1. SAFE SESSION START
//    Ensure session is running before accessing $_SESSION
// ---------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------
// 2. RETRIEVE & CLEAR MESSAGES
//    Priority: Success > Error > Login Message
// ---------------------------------------------
$message = null;
$messageType = 'info'; // Default fallback

if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = 'success';
    unset($_SESSION['success_message']); // Clear immediately
} 
elseif (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    $messageType = 'error';
    unset($_SESSION['error']);
} 
elseif (isset($_SESSION['login_message'])) {
    $message = $_SESSION['login_message'];
    $messageType = $_SESSION['login_message_type'] ?? 'info';
    unset($_SESSION['login_message'], $_SESSION['login_message_type']);
}

// ---------------------------------------------
// 3. DEFINE BOOTSTRAP STYLES
// ---------------------------------------------
$styles = [
    'success' => [
        'box'  => 'alert-success border-start border-5 border-success',
        'icon' => 'bi-check-circle-fill text-success'
    ],
    'error' => [
        'box'  => 'alert-danger border-start border-5 border-danger',
        'icon' => 'bi-exclamation-triangle-fill text-danger'
    ],
    'warning' => [
        'box'  => 'alert-warning border-start border-5 border-warning',
        'icon' => 'bi-exclamation-circle-fill text-warning'
    ],
    'info' => [
        'box'  => 'alert-primary border-start border-5 border-primary',
        'icon' => 'bi-info-circle-fill text-primary'
    ]
];

// Determine active style
$currentStyle = $styles[$messageType] ?? $styles['info'];
?>

<?php if ($message): ?>
    <div id="globalMsg" 
         class="alert <?= $currentStyle['box'] ?> shadow-lg position-fixed top-0 start-50 translate-middle-x mt-4 py-3 px-4 rounded-4 d-flex align-items-center" 
         style="z-index: 10000; min-width: 320px; max-width: 90%; animation: slideDown 0.5s ease-out;"
         role="alert">

        <i class="bi <?= $currentStyle['icon'] ?> fs-4 me-3"></i>

        <div class="fw-bold text-dark text-truncate" style="max-width: 600px;">
            <?= htmlspecialchars($message) ?>
        </div>

        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alertElement = document.getElementById('globalMsg');
                if (alertElement) {
                    // Fade out effect before removing
                    alertElement.style.transition = "opacity 0.5s ease";
                    alertElement.style.opacity = "0";
                    setTimeout(function() { alertElement.remove(); }, 500); 
                }
            }, 4000); // 4 seconds delay
        });
    </script>

    <style>
        @keyframes slideDown {
            from { transform: translate(-50%, -100%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
    </style>
<?php endif; ?>