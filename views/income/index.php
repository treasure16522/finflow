<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\CurrencyHelper;

$this->title = 'Income';
?>

<!-- Stats Cards -->
<div class="stat-grid">
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-label">This Month</div>
        <div class="stat-val green"><?= CurrencyHelper::formatUGX($currentMonthTotal) ?></div>
        <div class="stat-change up">↑ <?= $lastMonthTotal > 0 ? round(($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal * 100, 1) : 0 ?>% increase</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">📅</div>
        <div class="stat-label">Last Month</div>
        <div class="stat-val blue"><?= CurrencyHelper::formatUGX($lastMonthTotal) ?></div>
        <div class="stat-change"><?= date('F Y', strtotime('-1 month')) ?></div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">🏆</div>
        <div class="stat-label">YTD Income</div>
        <div class="stat-val gold"><?= CurrencyHelper::formatUGX($ytdTotal) ?></div>
        <div class="stat-change">Year to date</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">📌</div>
        <div class="stat-label">Sources</div>
        <div class="stat-val purple"><?= $sourcesCount ?></div>
        <div class="stat-change">Active income streams</div>
    </div>
</div>

<!-- Income Records -->
<div class="card"style="background-color:#0F1C32;">
    <div class="section-hd">
        <h3 style="color:white;">Income Records</h3>
        <?= Html::a('+ Add Income', ['create'], ['class' => 'btn btn-green']) ?>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Source</th>
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
                        <td><?= Html::encode($model->source) ?></td>
                        <td>
                            <span class="td-cat">
                                <?= $model->category->icon ?? '💰' ?> 
                                <?= Html::encode($model->category->name ?? 'Uncategorized') ?>
                            </span>
                        </td>
                        <td style="text-align: right; font-family: monospace;">
                            <span class="tx-amt pos">+ <?= CurrencyHelper::formatUGX($model->amount, false) ?></span>
                        </td>
                        <td><?= Yii::$app->formatter->asDate($model->date, 'php:M j, Y') ?></td>
                        <td><?= Html::encode($model->note) ?></td>
                        <td class="td-actions">
                            <?= Html::a('✏️', ['update', 'id' => $model->id], ['class' => 'btn btn-ghost btn-sm']) ?>
                            <?= Html::a('🗑️', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-red btn-sm',
                                'data' => [
                                    'confirm' => 'Delete this income record?',
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