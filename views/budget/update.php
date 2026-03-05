<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Budget';
?>

<div class="card" style="max-width: 500px; margin: 0 auto; background-color: #0F1C32;">
    <div class="card-title" style="color:white;">
        <span>📊</span> Update Budget
    </div>
    
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-grid'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'control-label', 'style' => 'color: var(--text2);'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'help-block', 'style' => 'color: var(--red2); font-size: 11px;'],
        ],
    ]); ?>
    
    <div class="form-group full">
        <?= $form->field($model, 'category_id')->dropDownList(
            \yii\helpers\ArrayHelper::map($categories, 'id', function($category) {
                return ($category->icon ?? '📊') . ' ' . $category->name;
            }),
            ['prompt' => 'Select Category', 'class' => 'form-control', 'disabled' => true]
        ) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Category cannot be changed</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'planned_amount')->textInput([
            'type' => 'number', 
            'step' => '1000',
            'min' => '0',
            'placeholder' => 'e.g. 1000000'
        ]) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Amount in UGX</div>
    </div>
    
    <div class="form-group">
        <label style="color: var(--text2);">Month</label>
        <input type="month" class="form-control" id="budget-month" name="Budget[month]" 
               value="<?= date('Y-m', strtotime($model->month)) ?>" required>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Select the month for this budget</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'alert_threshold')->textInput([
            'type' => 'number',
            'min' => 1,
            'max' => 100,
            'value' => $model->alert_threshold ?: 80
        ]) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Alert when this % of budget is used (1-100)</div>
    </div>
    
    <div class="form-group full" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
        <?= Html::a('Cancel', ['index', 'month' => $model->month], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('Update Budget', ['class' => 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<script>
// Ensure month is sent in YYYY-MM-DD format (first day of month)
document.querySelector('form').addEventListener('submit', function(e) {
    const monthInput = document.getElementById('budget-month');
    if (monthInput && monthInput.value) {
        let month = monthInput.value;
        
        // Check if it's in MM-YYYY format (some browsers)
        if (/^\d{1,2}-\d{4}$/.test(month)) {
            // Convert MM-YYYY to YYYY-MM
            const parts = month.split('-');
            month = parts[1] + '-' + parts[0].padStart(2, '0');
        }
        
        // Add the first day of the month
        monthInput.value = month + '-01';
    }
});
</script>