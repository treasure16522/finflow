<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\CurrencyHelper;

$this->title = $model->name . ' - Savings Goal';
$percentage = $model->getProgressPercentage();
$remaining = $model->getRemainingAmount();
$daysLeft = $model->getDaysLeft();
$monthlyNeeded = $model->getMonthlyNeeded();
?>

<!-- Back Button -->
<div style="margin-bottom: 16px;">
    <?= Html::a('← Back to Goals', ['index'], ['class' => 'btn btn-info']) ?>
</div>

<!-- Goal Header -->
<div class="grid-2" style="margin-bottom: 16px;">
    <div class="card" style="background-color: #0F1C32;">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="font-size: 64px;"><?= $model->icon ?? '🎯' ?></div>
            <div>
                <div style="font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: white;"><?= Html::encode($model->name) ?></div>
                <div style="display: flex; gap: 10px; margin-top: 5px; flex-wrap: wrap;">
                    <?php if ($model->is_completed): ?>
                        <span class="pill pill-gold" style="font-size: 12px; padding: 5px 15px;">✅ Completed <?= $model->completed_at ? date('M j, Y', strtotime($model->completed_at)) : '' ?></span>
                    <?php else: ?>
                        <span class="pill pill-blue" style="font-size: 12px; padding: 5px 15px;">⚡ In Progress</span>
                        <?php if ($daysLeft !== null): ?>
                            <span class="pill <?= $daysLeft < 30 ? 'pill-red' : 'pill-green' ?>" style="font-size: 12px; padding: 5px 15px;">
                                📅 <?= $daysLeft ?> days left
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div style="margin-left: auto; display: flex; gap: 10px;">
                <?= Html::a('✏️ Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
                <?= Html::a('🗑️ Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-primary btn-sm',
                    'data' => ['confirm' => 'Delete this goal?', 'method' => 'post']
                ]) ?>
            </div>
        </div>
    </div>
    
    <div class="card" style="background-color: #0F1C32;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div style="font-size: 12px; color: white;">Target Amount</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--gold2);"><?= CurrencyHelper::formatUGX($model->target_amount) ?></div>
            </div>
            <div>
                <div style="font-size: 12px; color: white;">Currently Saved</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--green2);"><?= CurrencyHelper::formatUGX($model->current_amount) ?></div>
            </div>
            <div>
                <div style="font-size: 12px; color: white;">Remaining</div>
                <div style="font-size: 20px; font-weight: 600; color: var(--text);"><?= CurrencyHelper::formatUGX($remaining) ?></div>
            </div>
            <div>
                <div style="font-size: 12px; color: white;">Monthly Contribution</div>
                <div style="font-size: 20px; font-weight: 600; color: var(--text);"><?= CurrencyHelper::formatUGX($model->monthly_contribution ?: 0) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Progress and Add Funds -->
<div class="grid-2">
    <div class="card" style="background-color: #0F1C32;">
        <div class="card-title"><span>📊</span> Progress</div>
        
        <div style="margin: 20px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-size: 14px; color: white;">Overall Progress</span>
                <span style="font-size: 14px; font-weight: 600; color: var(--accent2);"><?= $percentage ?>%</span>
            </div>
            <div class="progress-bar-bg" style="height: 12px;">
                <div class="progress-bar-fill" style="width: <?= $percentage ?>%; background: linear-gradient(to right, var(--accent), var(--accent2));"></div>
            </div>
        </div>
        
        <?php if (!$model->is_completed && $daysLeft && $daysLeft > 0): ?>
            <div style="margin-top: 20px; padding: 15px; background: var(--bg3); border-radius: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>To reach your goal by <?= date('F j, Y', strtotime($model->deadline)) ?></span>
                </div>
                <div style="font-size: 18px; font-weight: 600; color: var(--gold2);">
                    <?= CurrencyHelper::formatUGX($monthlyNeeded) ?>/month
                </div>
                <div style="font-size: 12px; color: var(--text3);">needed to save</div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!$model->is_completed): ?>
        <div class="card" style="background-color: #0F1C32;">
            <div class="card-title"><span>➕</span> Add Contribution</div>
            
            <?php $form = \yii\widgets\ActiveForm::begin([
                'action' => ['add-contribution', 'goalId' => $model->id],
                'method' => 'post',
                'options' => ['class' => 'form-grid']
            ]); ?>
            
            <div class="form-group full">
                <label>Amount (UGX)</label>
                <input type="number" name="GoalContribution[amount]" step="1000" min="1000" placeholder="e.g. 50000" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="GoalContribution[date]" value="<?= date('Y-m-d') ?>" required class="form-control">
            </div>
            
            <div class="form-group full">
                <label>Note (optional)</label>
                <input type="text" name="GoalContribution[note]" placeholder="e.g. Monthly savings, Bonus, Gift" class="form-control">
            </div>
            
            <div class="form-group full">
                <button type="submit" class="btn btn-green" style="width: 100%; padding: 12px;">➕ Add Contribution</button>
            </div>
            
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Contributions History -->
<div class="card" style="background-color: #0F1C32;margin-top: 16px;">
    <div class="card-title"><span>📋</span> Contribution History</div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount (UGX)</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $contributions = $contributionsProvider->getModels();
                if (!empty($contributions)): 
                    foreach ($contributions as $contribution): 
                ?>
                    <tr>
                        <td><?= Yii::$app->formatter->asDate($contribution->date, 'php:M j, Y') ?></td>
                        <td><span class="tx-amt pos">+<?= CurrencyHelper::formatUGX($contribution->amount, false) ?></span></td>
                        <td><?= Html::encode($contribution->note) ?></td>
                        <td class="td-actions">
                            <?= Html::a('🗑️', ['delete-contribution', 'id' => $contribution->id], [
                                'class' => 'btn btn-info btn-sm',
                                'data' => [
                                    'confirm' => 'Delete this contribution?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                    </tr>
                <?php 
                    endforeach;
                else: 
                ?>
                    <tr>
                        <td colspan="4" class="empty">
                            <div class="empty-icon">💰</div>
                            <div class="empty-text">No contributions yet</div>
                            <p style="font-size: 12px; margin-top: 5px;">Add your first contribution to start saving!</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($contributionsProvider->pagination && $contributionsProvider->pagination->totalCount > $contributionsProvider->pagination->pageSize): ?>
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            <?= LinkPager::widget([
                'pagination' => $contributionsProvider->pagination,
                'options' => ['class' => 'pagination'],
                'linkOptions' => ['class' => 'page-link'],
                'activePageCssClass' => 'active',
                'prevPageLabel' => '‹',
                'nextPageLabel' => '›',
            ]) ?>
        </div>
    <?php endif; ?>
</div>

<!-- Action Buttons -->
<div style="display: flex; gap: 10px; margin-top: 16px; justify-content: flex-end;">
    <?= Html::a('← Back to Goals', ['index'], ['class' => 'btn btn-info']) ?>
    <?= Html::a('✏️ Edit Goal', ['update', 'id' => $model->id], ['class' => 'btn btn-green']) ?>
</div>

<style>
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
/* Reuse styles from index */
.progress-bar-bg {
    background: var(--bg3);
    border-radius: 50px;
    height: 8px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 50px;
    transition: width 0.6s cubic-bezier(.4,0,.2,1);
}

.tx-amt.pos {
    color: var(--green2);
    font-weight: 600;
}

.tx-amt.pos::before {
    content: '+';
    margin-right: 2px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text3);
    padding: 10px 12px;
    border-bottom: 1px solid var(--border);
}

td {
    padding: 13px 12px;
    font-size: 13px;
    border-bottom: 1px solid rgba(99,179,237,0.06);
    vertical-align: middle;
}

.td-actions {
    display: flex;
    gap: 6px;
}

.pagination {
    display: flex;
    gap: 5px;
    list-style: none;
}

.page-link {
    display: block;
    padding: 8px 12px;
    background: var(--card2);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text2);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
}

.page-link:hover {
    background: var(--bg3);
    color: var(--text);
    border-color: var(--accent);
}

.active .page-link {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

.empty {
    text-align: center;
    padding: 30px 20px;
    color: var(--text3);
}

.empty-icon {
    font-size: 40px;
    margin-bottom: 10px;
    opacity: 0.7;
}
.card-title{
    color: white;
}
</style>