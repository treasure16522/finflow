<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\CurrencyHelper;

$this->title = 'Dashboard';
?>

<!-- Stats Cards with UGX formatting -->
<div class="stat-grid">
    <div class="stat-card blue">
        <div class="stat-icon">💳</div>
        <div class="stat-label">Total Balance</div>
        <div class="stat-val blue"><?= CurrencyHelper::formatUGX($totalBalance) ?></div>
        <div class="stat-change up">↑ <?= $savingsRate ?>% savings rate</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Monthly Income</div>
        <div class="stat-val green"><?= CurrencyHelper::formatUGX($monthlyIncome) ?></div>
        <div class="stat-change up">Current month</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">🧾</div>
        <div class="stat-label">Monthly Expenses</div>
        <div class="stat-val red"><?= CurrencyHelper::formatUGX($monthlyExpenses) ?></div>
        <div class="stat-change down"><?= $monthlyIncome > 0 ? round(($monthlyExpenses / $monthlyIncome) * 100) : 0 ?>% of income</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">💎</div>
        <div class="stat-label">Savings Rate</div>
        <div class="stat-val gold"><?= $savingsRate ?>%</div>
        <div class="stat-change up">↑ <?= CurrencyHelper::formatUGX($monthlyIncome - $monthlyExpenses) ?> saved</div>
    </div>
</div>


<!-- Recent Transactions and Budget Overview -->
<div class="grid-2">
    <!-- Recent Transactions -->
    <div class="card"style="background-color:#0F1C32;">
        <div class="card-title" style="color:white;">
            <span>🕐</span> Recent Transactions 
            <div class="badge" style="cursor:pointer" onclick="window.location.href='<?= Url::to(['/expense/index']) ?>'">View all</div>
        </div>
        <div class="tx-list">
            <?php if (empty($recentTransactions)): ?>
                <div class="empty">
                    <div class="empty-icon">📭</div>
                    <div class="empty-text">No transactions yet</div>
                </div>
            <?php else: ?>
                <?php foreach ($recentTransactions as $transaction): ?>
                    <?php if (get_class($transaction) == 'app\models\Income'): ?>
                        <div class="tx-item">
                            <div class="tx-icon" style="background:rgba(16,185,129,0.15)">
                                <?= $transaction->category->icon ?? '💰' ?>
                            </div>
                            <div>
                                <div class="tx-name"><?= Html::encode($transaction->source) ?></div>
                                <div class="tx-cat"><?= Html::encode($transaction->category->name ?? 'Income') ?></div>
                            </div>
                            <div class="tx-date"><?= Yii::$app->formatter->asDate($transaction->date, 'php:M j') ?></div>
                            <div class="tx-amt pos">+<?= CurrencyHelper::formatUGX($transaction->amount, false) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="tx-item">
                            <div class="tx-icon" style="background:rgba(239,68,68,0.15)">
                                <?= $transaction->category->icon ?? '🧾' ?>
                            </div>
                            <div>
                                <div class="tx-name"><?= Html::encode($transaction->description) ?></div>
                                <div class="tx-cat"><?= Html::encode($transaction->category->name ?? 'Expense') ?></div>
                            </div>
                            <div class="tx-date"><?= Yii::$app->formatter->asDate($transaction->date, 'php:M j') ?></div>
                            <div class="tx-amt neg">-<?= CurrencyHelper::formatUGX($transaction->amount, false) ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Budget Overview -->
    <div class="card"style="background-color:#0F1C32;">
        <div class="card-title" style="color:white;"><span>📊</span> Budget Overview</div>
        <div class="progress-wrap">
            <?php if (empty($budgetOverview)): ?>
                <div class="empty">
                    <div class="empty-icon">📊</div>
                    <div class="empty-text">No budgets set for this month</div>
                    <button class="btn btn-primary btn-sm" style="margin-top:10px" onclick="window.location.href='<?= Url::to(['/budget/create']) ?>'">
                        + Set Budget
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($budgetOverview as $budget): ?>
                    <div class="progress-item">
                        <div class="progress-top">
                            <span class="progress-name"><?= $budget['icon'] ?> <?= Html::encode($budget['category']) ?></span>
                            <span class="progress-vals">
                                <?= CurrencyHelper::formatUGX($budget['actual'], false) ?> / 
                                <?= CurrencyHelper::formatUGX($budget['planned'], false) ?>
                                <span class="pill pill-<?= $budget['status'] ?>" style="margin-left:6px">
                                    <?= $budget['percentage'] ?>%
                                </span>
                            </span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width:<?= min($budget['percentage'], 100) ?>%;
                                background: <?= $budget['status'] == 'danger' ? 'linear-gradient(to right, var(--red), var(--red2))' : 
                                    ($budget['status'] == 'warning' ? 'linear-gradient(to right, var(--gold), var(--gold2))' : 
                                    'linear-gradient(to right, var(--accent), var(--accent2))') ?>;">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid-2" style="margin-top: 16px;">
    <div class="card"style="background-color:#0F1C32;">
        <div class="card-title" style="color:white;"><span>⚡</span> Quick Actions</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <button class="btn btn-primary" onclick="window.location.href='<?= Url::to(['/income/create']) ?>'">
                <span style="font-size:18px">💰</span> Add Income
            </button>
            <button class="btn btn-primary" onclick="window.location.href='<?= Url::to(['/expense/create']) ?>'">
                <span style="font-size:18px">🧾</span> Add Expense
            </button>
            <button class="btn btn-info" onclick="window.location.href='<?= Url::to(['/budget/index']) ?>'">
                <span style="font-size:18px">📊</span> Manage Budgets
            </button>
            <button class="btn btn-info" onclick="window.location.href='<?= Url::to(['/savings/index']) ?>'">
                <span style="font-size:18px">🎯</span> View Goals
            </button>
        </div>
    </div>
    
    <!-- Savings Goals Preview -->
    <div class="card"style="background-color:#0F1C32;">
        <div class="card-title" style="color:white;">
            <span>🎯</span> Savings Goals 
            <div class="badge" style="cursor:pointer" onclick="window.location.href='<?= Url::to(['/savings/index']) ?>'">View all</div>
        </div>
        <?php 
        $goals = \app\models\SavingsGoal::find()
            ->where(['user_id' => Yii::$app->user->id, 'is_completed' => false])
            ->orderBy(['deadline' => SORT_ASC])
            ->limit(3)
            ->all();
        ?>
        <?php if (empty($goals)): ?>
            <div class="empty">
                <div class="empty-icon">🏆</div>
                <div class="empty-text">No active goals</div>
                <button class="btn btn-primary btn-sm" style="margin-top:10px" onclick="window.location.href='<?= Url::to(['/savings/create']) ?>'">
                    + Create Goal
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($goals as $goal): ?>
                <div class="goal-card" style="margin-bottom: 10px; padding: 15px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="font-size: 24px;"><?= $goal->icon ?? '🎯' ?></span>
                        <div>
                            <div style="font-weight: 600; color: white;"><?= Html::encode($goal->name) ?></div>
                            <div style="font-size: 11px; color: var(--text3);">
                                <?= $goal->deadline ? 'Due ' . Yii::$app->formatter->asDate($goal->deadline, 'php:M Y') : 'No deadline' ?>
                            </div>
                        </div>
                    </div>
                    <div class="goal-amounts">
                        <div>
                            <div class="goal-saved"><?= CurrencyHelper::formatUGX($goal->current_amount, false) ?></div>
                            <div style="font-size:11px;color:var(--text3)">Saved</div>
                        </div>
                        <div style="text-align:right">
                            <div class="goal-target"><?= CurrencyHelper::formatUGX($goal->target_amount, false) ?></div>
                            <div style="font-size:11px;color:var(--text3)">Goal</div>
                        </div>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:<?= $goal->getProgressPercentage() ?>%; background:linear-gradient(to right, var(--accent), var(--accent2))"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
/* Additional styles for charts */
.pie-wrap {
    display: flex;
    align-items: center;
    gap: 20px;
    min-height: 150px;
}

.pie {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}

.pie-inner {
    position: absolute;
    inset: 22px;
    background: var(--card);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: var(--text3);
    font-weight: 600;
    flex-direction: column;
    gap: 1px;
}

.pie-legend {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.pie-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
}

.pie-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
}

.pie-name {
    color: var(--text2);
    flex: 1;
}

.pie-pct {
    color: var(--text);
    font-weight: 600;
}

.bar-chart {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    height: 140px;
    padding-bottom: 4px;
}

.bar-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    height: 100%;
    justify-content: flex-end;
}

.bars {
    display: flex;
    gap: 4px;
    align-items: flex-end;
    width: 100%;
    justify-content: center;
}

.bar {
    border-radius: 5px 5px 0 0;
    width: 14px;
    transition: opacity 0.2s;
    cursor: pointer;
}

.bar:hover {
    opacity: 0.75;
}

.bar.income {
    background: linear-gradient(to top, var(--accent), var(--accent2));
}

.bar.expense {
    background: linear-gradient(to top, #ef4444, #f87171);
}

.bar-label {
    font-size: 10px;
    color: var(--text3);
}

.chart-legend {
    display: flex;
    gap: 16px;
    margin-top: 14px;
    justify-content: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text2);
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text3);
}

.empty-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.empty-text {
    font-size: 14px;
}
:root {
            --bg: #0a0f1e;
            --bg2: #0d1526;
            --bg3: #111d35;
            --card: #0F1C32;
            --card2: #132040;
            --border: rgba(99,179,237,0.12);
            --accent: #3b82f6;
            --accent2: #60a5fa;
            --gold: #f59e0b;
            --gold2: #fbbf24;
            --green: #10b981;
            --green2: #34d399;
            --red: #ef4444;
            --red2: #f87171;
            --purple: #8b5cf6;
            --teal: #14b8a6;
            --text: #e2e8f0;
            --text2: #94a3b8;
            --text3: #64748b;
        }
</style>