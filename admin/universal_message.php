<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['update_msg'])):
    $type = $_SESSION['update_type'] ?? 'error'; // default type

    // Map type to CSS class & icon
    $classes = [
        'success' => ['class' => 'global-success-msg', 'icon' => 'bi-check-circle-fill'],
        'error' => ['class' => 'global-error-msg', 'icon' => 'bi-x-circle-fill'],
        'info' => ['class' => 'global-info-msg', 'icon' => 'bi-info-circle-fill'],
        'warning' => ['class' => 'global-warning-msg', 'icon' => 'bi-exclamation-triangle-fill']
    ];

    $msgClass = $classes[$type]['class'] ?? 'global-error-msg';
    $msgIcon = $classes[$type]['icon'] ?? 'bi-x-circle-fill';
    ?>
    <div class="<?= $msgClass; ?>">
        <i class="bi <?= $msgIcon; ?> me-2"></i>
        <span class="fw-bold"><?= htmlspecialchars($_SESSION['update_msg']); ?></span>
    </div>
    <?php
    unset($_SESSION['update_msg'], $_SESSION['update_type']);
endif;
?>