<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use app\helpers\CurrencyHelper;

$this->title = 'Expenses';
?>

<!-- Stats Cards -->
<div class="stat-grid">
    <div class="stat-card red">
        <div class="stat-icon">🧾</div>
        <div class="stat-label">Total Spent</div>
        <div class="stat-val red"><?= CurrencyHelper::formatUGX($totalSpent) ?></div>
        <div class="stat-change down">This month</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">📅</div>
        <div class="stat-label">Daily Average</div>
        <div class="stat-val gold"><?= CurrencyHelper::formatUGX($dailyAverage) ?></div>
        <div class="stat-change"><?= date('F Y') ?></div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">📦</div>
        <div class="stat-label">Transactions</div>
        <div class="stat-val blue"><?= $transactionCount ?></div>
        <div class="stat-change">This month</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">💚</div>
        <div class="stat-label">Under Budget</div>
        <div class="stat-val green"><?= $categoriesUnderBudget ?>/<?= count($categories) ?></div>
        <div class="stat-change">Categories on track</div>
    </div>
</div>

<!-- Expense Records -->
<div class="card" style="background-color: #0F1C32;">
    <div class="section-hd">
        <h3 style="color:white">Expense Records</h3>
        <?= Html::a('+ Add Expense', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Amount (UGX)</th>
                    <th>Date</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $model): ?>
                    <tr>
                        <td><?= Html::encode($model->description) ?></td>
                        <td>
                            <span class="td-cat">
                                <?= $model->category->icon ?? '🧾' ?> 
                                <?= Html::encode($model->category->name ?? 'Uncategorized') ?>
                            </span>
                        </td>
                        <td style="text-align: right; font-family: monospace;">
                            <span class="tx-amt neg">- <?= CurrencyHelper::formatUGX($model->amount, false) ?></span>
                        </td>
                        <td><?= Yii::$app->formatter->asDate($model->date, 'php:M j, Y') ?></td>
                        <td><?= Html::encode($model->note) ?></td>
                        <td class="td-actions">
                            <?= Html::a('✏️', ['update', 'id' => $model->id], ['class' => 'btn btn-ghost btn-sm']) ?>
                            <?= Html::a('🗑️', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-red btn-sm',
                                'data' => [
                                    'confirm' => 'Delete this expense?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>