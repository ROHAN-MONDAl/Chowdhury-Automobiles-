<?php
// ---------------------------------------------
// 1. SAFE SESSION START
// ---------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------
// 2. LOGIC (Unchanged)
// ---------------------------------------------
$message = null;
$type = 'info';

// Check Custom Logic
if (!empty($_SESSION['update_msg'])) {
    $message = $_SESSION['update_msg'];
    $type = $_SESSION['update_type'] ?? 'info';
    unset($_SESSION['update_msg'], $_SESSION['update_type']);
} 
// Check Flash Logic
elseif (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $extraClass = $_SESSION['flash_class'] ?? '';
    if (strpos($extraClass, 'success') !== false) $type = 'success';
    elseif (strpos($extraClass, 'error') !== false) $type = 'error';
    elseif (strpos($extraClass, 'warning') !== false) $type = 'warning';
    unset($_SESSION['flash_message'], $_SESSION['flash_class']);
}

// ---------------------------------------------
// 3. DEFINE COLORS & ICONS
// ---------------------------------------------
$visuals = [
    'success' => ['class' => 'pill-success', 'icon' => 'bi-check-circle-fill'],
    'error'   => ['class' => 'pill-error',   'icon' => 'bi-x-circle-fill'],
    'warning' => ['class' => 'pill-warning', 'icon' => 'bi-exclamation-circle-fill'],
    'info'    => ['class' => 'pill-info',    'icon' => 'bi-info-circle-fill']
];

$currentVisual = $visuals[$type] ?? $visuals['info'];
?>

<?php if ($message): ?>
    <div id="miniNotify" class="mini-pill-notify <?= $currentVisual['class'] ?>">
        <i class="bi <?= $currentVisual['icon'] ?> pill-icon"></i>
        <span class="pill-text"><?= htmlspecialchars($message) ?></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var notify = document.getElementById('miniNotify');
            if (notify) {
                // Remove after 3 seconds for a quick interaction
                setTimeout(function() {
                    notify.style.transform = "translateX(-50%) translateY(-150%)"; // Slide Up Out
                    notify.style.opacity = "0";
                    setTimeout(function() { notify.remove(); }, 500);
                }, 3000);
            }
        });
    </script>

    <style>
        /* -------------------------------------------
           MINI PILL STYLES
        ------------------------------------------- */
        .mini-pill-notify {
            position: fixed !important;
            top: 25px !important;
            left: 50% !important;
            transform: translateX(-50%); /* Center perfectly */
            z-index: 999999 !important;
            
            /* Sizing: Small & Compact */
            width: fit-content !important;
            min-width: 120px;
            max-width: 90%; /* Prevent overflow on small phones */
            padding: 8px 16px;
            
            /* Shape */
            border-radius: 50px; /* Full Capsule Shape */
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            
            /* Flex Layout */
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            
            /* Text Default */
            color: #fff !important;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            
            /* Animation */
            animation: dropDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            transition: transform 0.4s ease, opacity 0.4s ease;
        }

        .pill-icon {
            font-size: 1.1rem;
        }
        
        /* -------------------------------------------
           COLORS (Vibrant Backgrounds)
        ------------------------------------------- */
        .pill-success { background-color: #17a505ff !important; } /* Emerald */
        .pill-error   { background-color: #ef4444 !important; } /* Red */
        .pill-warning { background-color: #f59e0b !important; } /* Amber */
        .pill-info    { background-color: #3b82f6 !important; } /* Blue */

        /* -------------------------------------------
           ANIMATION
        ------------------------------------------- */
        @keyframes dropDown {
            from { 
                top: -50px; 
                opacity: 0; 
            }
            to { 
                top: 25px; 
                opacity: 1; 
            }
        }
    </style>
<?php endif; ?>