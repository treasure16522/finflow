<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Create Savings Goal';
$icons = ['🚨', '✈️', '🏠', '🎓', '🚗', '💍', '🎯', '🏦', '💻', '📱', '🎮', '📚', '🏥', '🛒', '⚽'];
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-title">
        <span>🎯</span> Create New Savings Goal
    </div>
    
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-grid'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'control-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'help-block'],
        ],
    ]); ?>
    
    <div class="form-group full">
        <?= $form->field($model, 'name')->textInput([
            'placeholder' => 'e.g. Emergency Fund, Vacation to Zanzibar, New Car'
        ]) ?>
    </div>
    
    <div class="form-group full">
        <label>Icon</label>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 5px;">
            <?php foreach ($icons as $icon): ?>
                <label class="icon-radio" style="cursor: pointer; padding: 10px 15px; background: var(--bg3); border: 1px solid var(--border); border-radius: 8px; transition: all 0.2s;">
                    <input type="radio" name="SavingsGoal[icon]" value="<?= $icon ?>" style="display: none;" <?= $icon == '🎯' ? 'checked' : '' ?>>
                    <span style="font-size: 24px;"><?= $icon ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'target_amount')->textInput([
            'type' => 'number',
            'step' => '1000',
            'placeholder' => 'e.g. 1000000'
        ]) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Amount in UGX</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'current_amount')->textInput([
            'type' => 'number',
            'step' => '1000',
            'value' => 0,
            'placeholder' => '0'
        ]) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Amount you've already saved</div>
    </div>
    
    <div class="form-group">
        <label>Target Date</label>
        <input type="month" class="form-control" name="SavingsGoal[deadline]" 
               value="<?= $model->deadline ? date('Y-m', strtotime($model->deadline)) : date('Y-m', strtotime('+1 year')) ?>">
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">When do you want to achieve this goal?</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'monthly_contribution')->textInput([
            'type' => 'number',
            'step' => '1000',
            'placeholder' => 'e.g. 100000'
        ]) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">How much can you save each month?</div>
    </div>
    
    <div class="form-group full" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('✨ Create Goal', ['class' => 'btn btn-green']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group.full {
    grid-column: 1 / -1;
}

.help-block {
    color: var(--red2);
    font-size: 11px;
    margin-top: 4px;
}

.icon-radio {
    cursor: pointer;
    padding: 10px 15px;
    background: var(--bg3);
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: all 0.2s;
}

.icon-radio:hover {
    background: var(--card2);
    border-color: var(--accent);
}

.icon-radio.selected {
    background: rgba(59,130,246,0.2);
    border-color: var(--accent);
}

.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 30px;
}

.card-title {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
}
</style>

<script>
// Style the icon radio buttons
document.addEventListener('DOMContentLoaded', function() {
    const radioLabels = document.querySelectorAll('.icon-radio');
    
    radioLabels.forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        
        // Check initial state
        if (radio.checked) {
            label.style.background = 'rgba(59,130,246,0.2)';
            label.style.borderColor = 'var(--accent)';
        }
        
        // Add click handler
        label.addEventListener('click', function() {
            radioLabels.forEach(l => {
                l.style.background = 'var(--bg3)';
                l.style.borderColor = 'var(--border)';
            });
            this.style.background = 'rgba(59,130,246,0.2)';
            this.style.borderColor = 'var(--accent)';
        });
    });
});

// Handle month input
document.querySelector('input[type=month]')?.addEventListener('change', function() {
    if (this.value) {
        this.value = this.value + '-01';
    }
});
</script>