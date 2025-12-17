<?php
// ---------------------------------------------
// 1. SAFE SESSION START
// ---------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------
// 2. LOGIC (MERGED: Handles Login, Updates, and Flash messages)
// ---------------------------------------------
$message = null;
$type = 'info';

// CHECK 1: Custom Update Logic (Existing Feature)
if (!empty($_SESSION['update_msg'])) {
    $message = $_SESSION['update_msg'];
    $type = $_SESSION['update_type'] ?? 'info';
    unset($_SESSION['update_msg'], $_SESSION['update_type']);
}
// CHECK 2: Login Auth Logic (NEW: Added this to catch your login errors)
elseif (!empty($_SESSION['login_message'])) {
    $message = $_SESSION['login_message'];
    $type = $_SESSION['login_message_type'] ?? 'info';
    unset($_SESSION['login_message'], $_SESSION['login_message_type']);
}
// CHECK 3: Flash Logic (Existing Feature)
elseif (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $extraClass = $_SESSION['flash_class'] ?? '';

    // Map class names to types
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

// Fallback to info if type is unknown
$currentVisual = $visuals[$type] ?? $visuals['info'];
?>

<?php if ($message): ?>
    <div id="miniNotify" class="mini-pill-notify <?= $currentVisual['class'] ?>">
        <i class="bi <?= $currentVisual['icon'] ?> pill-icon"></i>
        <span class="pill-text">
            <?= nl2br(htmlspecialchars($message)) ?>
        </span>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var notify = document.getElementById('miniNotify');
            if (notify) {
                // Remove after 3 seconds
                setTimeout(function() {
                    notify.style.transform = "translateX(-50%) translateY(-150%)"; // Slide Up
                    notify.style.opacity = "0";
                    setTimeout(function() {
                        notify.remove();
                    }, 500); // Remove from DOM
                }, 3000);
            }
        });
    </script>

    <style>
        .mini-pill-notify {
            position: fixed !important;
            top: 25px !important;
            left: 50% !important;
            transform: translateX(-50%);
            z-index: 999999 !important;

            /* Box sizing */
            width: auto;
            min-width: 260px;
            max-width: 92%;
            padding: 14px 18px;

            /* Box design */
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);

            /* Layout */
            display: flex;
            align-items: flex-start;
            gap: 12px;

            /* Text */
            color: #fff !important;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
            font-weight: 500;
            white-space: normal;

            /* Animation */
            animation: dropDown 0.35s ease forwards;
            transition: transform 0.4s ease, opacity 0.4s ease;
        }

        .pill-icon {
            font-size: 1.25rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .pill-text {
            display: block;
            max-width: 100%;
            text-align: left;
            line-height: 1.5;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        /* Colors */
        .pill-success {
            background-color: #17a505ff !important;
        }

        .pill-error {
            background-color: #ef4444 !important;
        }

        .pill-warning {
            background-color: #f59e0b !important;
        }

        .pill-info {
            background-color: #3b82f6 !important;
        }

        /* Mobile adjustments */
        @media (max-width: 576px) {
            .mini-pill-notify {
                padding: 12px 14px;
                font-size: 13px;
                border-radius: 8px;
            }

            .pill-text {
                text-align: center;
            }
        }

        /* Animation */
        @keyframes dropDown {
            from {
                top: -60px;
                opacity: 0;
            }

            to {
                top: 25px;
                opacity: 1;
            }
        }
    </style>

<?php endif; ?>