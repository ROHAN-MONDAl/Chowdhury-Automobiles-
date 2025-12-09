  <?php
    // ---------------------------------------------
// 1. Start the session (only if not started)
// ---------------------------------------------
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ---------------------------------------------
// 2. Read the flash message from the session
//    and then delete it so it shows only once
// ---------------------------------------------
    $message = $_SESSION['login_message'] ?? null;
    $messageType = $_SESSION['login_message_type'] ?? 'info';

    // Remove message so it doesn't show again
    unset($_SESSION['login_message'], $_SESSION['login_message_type']);

    // ---------------------------------------------
// 3. Define the CSS classes + icons for each type
//    This makes it easy to add more types later
// ---------------------------------------------
    $styles = [
        'success' => [
            'box' => 'msg-success',
            'icon' => 'bi-check-circle-fill text-success'
        ],
        'error' => [
            'box' => 'msg-error',
            'icon' => 'bi-x-circle-fill text-danger'
        ],
        'warning' => [
            'box' => 'msg-warning',
            'icon' => 'bi-exclamation-triangle-fill text-warning'
        ],
        'info' => [
            'box' => 'msg-info',
            'icon' => 'bi-info-circle-fill text-info'
        ]
    ];

    // Use 'info' style if message type doesn't exist
    $currentStyle = $styles[$messageType] ?? $styles['info'];
    ?>

    <!-- ---------------------------------------------
     4. SHOW MESSAGE (Only if message exists)
---------------------------------------------- -->
    <?php if ($message): ?>
        <div id="globalMsg"
            class="shadow-lg global-msg position-fixed top-0 start-50 translate-middle-x mt-3 p-3 rounded-3 show <?= $currentStyle['box'] ?>">

            <!-- Icon -->
            <i class="bi me-2 <?= $currentStyle['icon'] ?>"></i>

            <!-- Message Text -->
            <span class="fw-bold">
                <?= htmlspecialchars($message) ?>
            </span>

        </div>
    <?php endif; ?>