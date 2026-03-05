<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\CurrencyHelper;

$this->title = 'Budget Planning';
$monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$currentMonthName = date('F Y', strtotime($selectedMonth));
?>

<!-- Statistics Cards -->
<div class="stat-grid">
    <div class="stat-card blue">
        <div class="stat-icon">📊</div>
        <div class="stat-label">Total Budget</div>
        <div class="stat-val blue"><?= CurrencyHelper::formatUGX($totalBudget) ?></div>
        <div class="stat-change">Monthly plan</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">💸</div>
        <div class="stat-label">Total Spent</div>
        <div class="stat-val red"><?= CurrencyHelper::formatUGX($totalSpent) ?></div>
        <div class="stat-change down"><?= $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100) : 0 ?>% of budget used</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Remaining</div>
        <div class="stat-val green"><?= CurrencyHelper::formatUGX($remaining) ?></div>
        <div class="stat-change up"><?= date('t', strtotime($selectedMonth)) - date('j') ?> days left</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">⚠️</div>
        <div class="stat-label">Overspent</div>
        <div class="stat-val gold"><?= $overspentCount ?></div>
        <div class="stat-change">Categories over budget</div>
    </div>
</div>

<!-- Month Selector -->
<div class="card" style="margin-bottom: 16px; background-color: #0F1C32;">
    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 18px;">📅</span>
            <select class="month-select" onchange="window.location.href = '<?= Url::to(['index']) ?>?month=' + this.value">
                <?php 
                // Generate last 6 months
                for ($i = 2; $i >= -3; $i--): 
                    $monthDate = date('Y-m-01', strtotime("$i months"));
                    $monthValue = date('Y-m', strtotime($monthDate)); // YYYY-MM format for URL
                    $displayMonth = date('F Y', strtotime($monthDate));
                    $selected = ($monthDate == $selectedMonth) ? 'selected' : '';
                ?>
                    <option value="<?= $monthValue ?>" <?= $selected ?>><?= $displayMonth ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <?= Html::a('+ Set Budget', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
    </div>
</div>

<!-- Budget per Category -->
<div class="card" style="background-color: #0F1C32;">
    <div class="card-title" style="color:white;"><span>🎯</span> Budget per Category</div>
    <div class="progress-wrap">
        <?php if ($dataProvider->totalCount > 0): ?>
            <?php foreach ($dataProvider->getModels() as $budget): ?>
                <div class="progress-item">
                    <div class="progress-top">
                        <span class="progress-name">
                            <?= $budget->category->icon ?? '📊' ?> <?= Html::encode($budget->category->name ?? 'Unknown') ?>
                        </span>
                        <span class="progress-vals">
                            <?= CurrencyHelper::formatUGX($budget->actual_amount, false) ?> / 
                            <?= CurrencyHelper::formatUGX($budget->planned_amount, false) ?>
                            <span class="pill pill-<?= $budget->getStatus() ?>" style="margin-left:6px">
                                <?= $budget->getSpentPercentage() ?>% · <?= $budget->getStatusText() ?>
                            </span>
                        </span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:<?= min($budget->getSpentPercentage(), 100) ?>%;
                            background: <?= $budget->getStatus() == 'danger' ? 'linear-gradient(to right, var(--red), var(--red2))' : 
                                ($budget->getStatus() == 'warning' ? 'linear-gradient(to right, var(--gold), var(--gold2))' : 
                                'linear-gradient(to right, var(--accent), var(--accent2))') ?>;">
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                        <small style="color: var(--text3);">
                            Remaining: <?= CurrencyHelper::formatUGX($budget->getRemainingAmount(), false) ?>
                        </small>
                        <div class="td-actions">
                            <?= Html::a('✏️ Edit', ['update', 'id' => $budget->id], ['class' => 'btn btn-info btn-sm']) ?>
                            <?= Html::a('🗑️ Delete', ['delete', 'id' => $budget->id], [
                                'class' => 'btn btn-primary btn-sm',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this budget?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty">
                <div class="empty-icon">📊</div>
                <div class="empty-text">No budgets set for <?= $currentMonthName ?></div>
                <?= Html::a('+ Set Your First Budget', ['create'], ['class' => 'btn btn-info btn-sm', 'style' => 'margin-top:10px']) ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Available Categories to Add -->
    <?php if (!empty($availableCategories)): ?>
        <hr class="divider">
        <div style="margin-top: 15px;">
            <div style="font-size: 12px; color: var(--text3); margin-bottom: 10px;">Quick add budgets for:</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <?php foreach ($availableCategories as $category): ?>
                    <?= Html::a(
                        $category->icon . ' ' . Html::encode($category->name),
                        ['create', 'category_id' => $category->id],
                        ['class' => 'btn btn-green btn-sm']
                    ) ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Monthly Budget Summary Table -->
<div class="card" style="margin-top: 16px; background-color: #0F1C32;">
    <div class="card-title" style="color:white;"><span>📋</span> Monthly Budget Summary - <?= $currentMonthName ?></div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Budgeted (UGX)</th>
                    <th>Spent (UGX)</th>
                    <th>Remaining (UGX)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($dataProvider->totalCount > 0): ?>
                    <?php foreach ($dataProvider->getModels() as $budget): ?>
                        <tr>
                            <td>
                                <?= $budget->category->icon ?? '📊' ?> 
                                <?= Html::encode($budget->category->name ?? 'Unknown') ?>
                            </td>
                            <td style="text-align: right; font-family: monospace;"><?= CurrencyHelper::formatUGX($budget->planned_amount, false) ?></td>
                            <td style="text-align: right; font-family: monospace;"><?= CurrencyHelper::formatUGX($budget->actual_amount, false) ?></td>
                            <td style="text-align: right; font-family: monospace; color: <?= $budget->getRemainingAmount() >= 0 ? 'var(--green2)' : 'var(--red2)' ?>;">
                                <?= CurrencyHelper::formatUGX($budget->getRemainingAmount(), false) ?>
                            </td>
                            <td>
                                <span class="pill pill-<?= $budget->getStatus() ?>">
                                    <?= $budget->getStatusText() ?>
                                </span>
                            </td>
                            <td class="td-actions">
                                <?= Html::a('✏️', ['update', 'id' => $budget->id], ['class' => 'btn btn-info btn-sm']) ?>
                                <?= Html::a('🗑️', ['delete', 'id' => $budget->id], [
                                    'class' => 'btn btn-primary btn-sm',
                                    'data' => [
                                        'confirm' => 'Delete this budget?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty">
                            <div class="empty-icon">📊</div>
                            <div class="empty-text">No budget data for this month</div>
                            <?= Html::a('+ Create Budget', ['create'], ['class' => 'btn btn-primary btn-sm', 'style' => 'margin-top:10px']) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Budget Tips -->
<div class="card" style="margin-top: 16px; background: linear-gradient(135deg, var(--bg3), var(--card));">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div style="font-size: 40px;">💡</div>
        <div>
            <div style="font-weight: 600; margin-bottom: 5px; color: white;">Budget Tips</div>
            <div style="font-size: 13px; color: var(--text2);">
                <?php if ($remaining > 0): ?>
                    You have <?= CurrencyHelper::formatUGX($remaining) ?> left to spend this month. 
                    Try to save at least 20% of it!
                <?php elseif ($remaining < 0): ?>
                    You've overspent by <?= CurrencyHelper::formatUGX(abs($remaining)) ?>. 
                    Consider adjusting your budget or reducing expenses.
                <?php else: ?>
                    You've exactly met your budget. Great planning!
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.pill-red { background: rgba(239,68,68,0.15); color: var(--red2); }
.pill-gold { background: rgba(245,158,11,0.15); color: var(--gold2); }
.pill-green { background: rgba(16,185,129,0.15); color: var(--green2); }
.pill-blue { background: rgba(59,130,246,0.15); color: var(--accent2); }
.td-actions { display: flex; gap: 5px; }
.empty { text-align: center; padding: 40px; color: var(--text3); }
.empty-icon { font-size: 48px; margin-bottom: 10px; }
.divider { border: none; border-top: 1px solid var(--border); margin: 20px 0; }
</style>