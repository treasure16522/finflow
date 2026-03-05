<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\CurrencyHelper;

$this->title = 'Savings & Goals';
?>

<!-- Stats Cards -->
<div class="stat-grid">
    <div class="stat-card green">
        <div class="stat-icon">🏦</div>
        <div class="stat-label">Total Saved</div>
        <div class="stat-val green"><?= CurrencyHelper::formatUGX($totalSaved) ?></div>
        <div class="stat-change up">Across all goals</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">🎯</div>
        <div class="stat-label">Active Goals</div>
        <div class="stat-val blue"><?= $activeGoalsCount ?></div>
        <div class="stat-change">In progress</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">🏆</div>
        <div class="stat-label">Completed</div>
        <div class="stat-val gold"><?= $completedGoalsCount ?></div>
        <div class="stat-change">Goals achieved</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">📅</div>
        <div class="stat-label">Next Milestone</div>
        <div class="stat-val purple">
            <?php if ($nextMilestone): ?>
                <?= date('M', strtotime($nextMilestone->deadline)) ?>
            <?php else: ?>
                —
            <?php endif; ?>
        </div>
        <div class="stat-change">
            <?php if ($nextMilestone): ?>
                <?= Html::encode($nextMilestone->name) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Section Header with Add Button -->
<div class="section-hd">
    <h3>Active Savings Goals</h3>
    <?= Html::a('+ New Goal', ['create'], ['class' => 'btn btn-green']) ?>
</div>

<!-- Goals Grid -->
<div class="goal-grid">
    <?php 
    $activeGoals = $activeGoalsProvider->getModels();
    if (!empty($activeGoals)): 
        foreach ($activeGoals as $goal): 
    ?>
        <div class="goal-card">
            <div class="goal-icon-big"><?= $goal->icon ?? '🎯' ?></div>
            <div class="goal-name"><?= Html::encode($goal->name) ?></div>
            <div class="goal-deadline">
                🗓 Target: <?= $goal->deadline ? date('F Y', strtotime($goal->deadline)) : 'No deadline' ?>
                <?php if ($goal->getDaysLeft() !== null && $goal->getDaysLeft() > 0): ?>
                    <span style="color: var(--text3); margin-left: 5px;">(<?= $goal->getDaysLeft() ?> days left)</span>
                <?php endif; ?>
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
                <div class="progress-bar-fill" style="width:<?= $goal->getProgressPercentage() ?>%; 
                    background:linear-gradient(to right, var(--accent), var(--accent2))"></div>
            </div>
            <div class="goal-rem">
                💵 <?= CurrencyHelper::formatUGX($goal->getRemainingAmount(), false) ?> remaining · <?= $goal->getProgressPercentage() ?>% complete
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 8px; margin-top: 15px; justify-content: space-between;">
                <?= Html::a('➕ Add Funds', ['add-contribution', 'goalId' => $goal->id], [
                    'class' => 'btn btn-primary btn-sm',
                    'style' => 'flex: 2;'
                ]) ?>
                <?= Html::a('👁️ View', ['view', 'id' => $goal->id], [
                    'class' => 'btn btn-ghost btn-sm',
                    'style' => 'flex: 1;'
                ]) ?>
            </div>
            <div style="display: flex; gap: 8px; margin-top: 5px;">
                <?= Html::a('✏️ Edit', ['update', 'id' => $goal->id], [
                    'class' => 'btn btn-ghost btn-sm',
                    'style' => 'flex: 1;'
                ]) ?>
                <?= Html::a('🗑️ Delete', ['delete', 'id' => $goal->id], [
                    'class' => 'btn btn-red btn-sm',
                    'style' => 'flex: 1;',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this goal? All contributions will also be deleted.',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
    <?php 
        endforeach;
    else: 
    ?>
        <!-- Empty state for no goals -->
        <div class="goal-card" style="grid-column: span 3; display: flex; align-items: center; justify-content: center; min-height: 250px;">
            <div style="text-align: center; color: var(--text3);">
                <div style="font-size: 48px; margin-bottom: 15px;">🎯</div>
                <div style="font-size: 16px; margin-bottom: 10px;">No active goals yet</div>
                <div style="font-size: 13px; margin-bottom: 20px; color: var(--text2);">Start saving towards your dreams!</div>
                <?= Html::a('+ Create Your First Goal', ['create'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Add New Goal Card (always visible) -->
    <div class="goal-card" style="border:1px dashed var(--border); background:transparent; display:flex; align-items:center; justify-content:center; cursor:pointer; min-height:250px" onclick="window.location.href='<?= Url::to(['create']) ?>'">
        <div style="text-align:center; color:var(--text3)">
            <div style="font-size:48px; margin-bottom:10px">+</div>
            <div style="font-size:16px; font-weight:500; margin-bottom:5px">Add New Goal</div>
            <div style="font-size:12px; color:var(--text2)">Set a new savings target</div>
        </div>
    </div>
</div>

<!-- Completed Goals Section -->
<?php 
$completedGoals = $completedGoalsProvider->getModels();
if (!empty($completedGoals)): 
?>
    <div style="margin-top: 40px;">
        <div class="section-hd">
            <h3>🏆 Completed Goals</h3>
        </div>
        
        <div class="goal-grid">
            <?php foreach ($completedGoals as $goal): ?>
                <div class="goal-card" style="opacity: 0.8; border-color: var(--green);">
                    <div class="goal-icon-big">🏆</div>
                    <div class="goal-name"><?= Html::encode($goal->name) ?></div>
                    <div class="goal-deadline">
                        ✅ Completed: <?= $goal->completed_at ? date('M j, Y', strtotime($goal->completed_at)) : 'Done!' ?>
                    </div>
                    <div class="goal-amounts">
                        <div>
                            <div class="goal-saved" style="color: var(--gold2);"><?= CurrencyHelper::formatUGX($goal->target_amount, false) ?></div>
                            <div style="font-size:11px;color:var(--text3)">Target achieved</div>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 15px;">
                        <span class="pill pill-gold" style="font-size: 12px; padding: 5px 15px;">🎉 Goal Complete!</span>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 15px; justify-content: center;">
                        <?= Html::a('👁️ View', ['view', 'id' => $goal->id], [
                            'class' => 'btn btn-ghost btn-sm'
                        ]) ?>
                        <?= Html::a('🗑️ Delete', ['delete', 'id' => $goal->id], [
                            'class' => 'btn btn-red btn-sm',
                            'data' => [
                                'confirm' => 'Delete this completed goal?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Pagination for active goals -->
<?php if ($activeGoalsProvider->pagination && $activeGoalsProvider->pagination->totalCount > $activeGoalsProvider->pagination->pageSize): ?>
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        <?= LinkPager::widget([
            'pagination' => $activeGoalsProvider->pagination,
            'options' => ['class' => 'pagination'],
            'linkOptions' => ['class' => 'page-link'],
            'activePageCssClass' => 'active',
            'prevPageLabel' => '‹',
            'nextPageLabel' => '›',
        ]) ?>
    </div>
<?php endif; ?>

<!-- Quick Savings Tip -->
<div class="card" style="margin-top: 30px; background: linear-gradient(135deg, var(--bg3), var(--card));">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div style="font-size: 40px;">💡</div>
        <div>
            <div style="font-weight: 600; margin-bottom: 5px; color:white;">Savings Tip</div>
            <div style="font-size: 13px; color: var(--text2);">
                Try the 50/30/20 rule: 50% for needs, 30% for wants, and 20% for savings. 
                Automate your monthly contributions to reach your goals faster!
            </div>
        </div>
    </div>
</div>

<style>
/* Goal grid and card styles */
.goal-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.goal-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    transition: transform 0.2s, border-color 0.2s;
    cursor: default;
    display: flex;
    flex-direction: column;
}

.goal-card:hover {
    transform: translateY(-3px);
    border-color: rgba(99,179,237,0.25);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.goal-icon-big {
    font-size: 40px;
    margin-bottom: 12px;
}

.goal-name {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 4px;
    color: var(--text);
}

.goal-deadline {
    font-size: 12px;
    color: var(--text3);
    margin-bottom: 14px;
}

.goal-amounts {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
}

.goal-saved {
    font-size: 20px;
    font-weight: 700;
    color: var(--green2);
}

.goal-target {
    font-size: 16px;
    color: var(--text3);
}

.goal-rem {
    font-size: 12px;
    color: var(--text3);
    margin-top: 8px;
}

/* Progress bar */
.progress-bar-bg {
    background: var(--bg3);
    border-radius: 50px;
    height: 8px;
    overflow: hidden;
    margin: 5px 0;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 50px;
    transition: width 0.6s cubic-bezier(.4,0,.2,1);
}

/* Button styles */
.btn {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-family: inherit;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent), #2563eb);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}

.btn-green {
    background: linear-gradient(135deg, var(--green), #059669);
    color: white;
}

.btn-green:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}

.btn-ghost {
    background: var(--card2);
    color: var(--text2);
    border: 1px solid var(--border);
}

.btn-ghost:hover {
    background: var(--bg3);
    color: var(--text);
}

.btn-red {
    background: rgba(239,68,68,0.15);
    color: var(--red2);
    border: 1px solid rgba(239,68,68,0.2);
}

.btn-red:hover {
    background: rgba(239,68,68,0.25);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 11px;
}

/* Section header */
.section-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.section-hd h3 {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--text);
}

/* Pagination */
.pagination {
    display: flex;
    gap: 5px;
    list-style: none;
    padding: 0;
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

/* Pill styles */
.pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.pill-green {
    background: rgba(16,185,129,0.15);
    color: var(--green2);
}

.pill-gold {
    background: rgba(245,158,11,0.15);
    color: var(--gold2);
}

.pill-blue {
    background: rgba(59,130,246,0.15);
    color: var(--accent2);
}

/* Empty state */
.empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text3);
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.7;
}
</style>