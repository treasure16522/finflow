<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\CurrencyHelper;

$this->title = 'Alerts';
?>

<!-- Stats Cards -->
<div class="stat-grid">
    <div class="stat-card red">
        <div class="stat-icon">🚨</div>
        <div class="stat-label">Critical Alerts</div>
        <div class="stat-val red"><?= $criticalCount ?></div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">⚠️</div>
        <div class="stat-label">Warnings</div>
        <div class="stat-val gold"><?= $warningCount ?></div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">ℹ️</div>
        <div class="stat-label">Reminders</div>
        <div class="stat-val blue"><?= $infoCount ?></div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Resolved</div>
        <div class="stat-val green"><?= $resolvedCount ?></div>
    </div>
</div>

<!-- Alerts List -->
<div class="card" style="background-color: #0F1C32;">
    <div class="alert-list">
        <?php foreach ($dataProvider->getModels() as $notification): ?>
            <div class="alert-item <?= $notification->getAlertClass() ?>" style="<?= $notification->is_read ? 'opacity: 0.6;' : '' ?>">
                <div class="alert-icon"><?= $notification->getIcon() ?></div>
                <div style="flex: 1;">
                    <div class="alert-title">
                        <?= Html::encode($notification->title) ?>
                        <?php if (!$notification->is_read): ?>
                            <span class="pill pill-blue" style="margin-left: 10px;">New</span>
                        <?php endif; ?>
                    </div>
                    <div class="alert-body"><?= Html::encode($notification->message) ?></div>
                </div>
                <div class="alert-time">
                    <?= $notification->getTimeAgo() ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>