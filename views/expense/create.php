<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\CurrencyHelper;

$this->title = 'Add Expense';
?>

<div class="card" style="max-width: 600px; margin: 0 auto; background-color:#0F1C32;">
    <div class="card-title" style="color:white;">
        <span>🧾</span> Add New Expense
    </div>
    
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-grid'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'control-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'help-block', 'style' => 'color: var(--red2); font-size: 11px;'],
        ],
    ]); ?>
    
    <?= $form->field($model, 'description')->textInput([
        'placeholder' => 'e.g. Grocery shopping, Rent, Fuel'
    ]) ?>
    
    <?= $form->field($model, 'amount')->textInput([
        'type' => 'number', 
        'step' => '1000',
        'placeholder' => 'e.g. 50000'
    ]) ?>
    <div style="font-size: 11px; color: var(--text3); margin-top: -10px; margin-bottom: 10px;">
        Amount in UGX (e.g., 50000 = 50,000 UGX)
    </div>
    
    <?= $form->field($model, 'category_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($categories, 'id', function($category) {
            return ($category->icon ?? '🧾') . ' ' . $category->name;
        }),
        ['prompt' => '-- Select Category --']
    ) ?>
    
    <div class="form-group">
        <?= $form->field($model, 'date')->textInput(['type' => 'date', 'value' => date('Y-m-d')]) ?>
    </div>
    
    <?= $form->field($model, 'note')->textarea(['rows' => 3, 'placeholder' => 'Optional notes about this expense']) ?>
    
    <?= $form->field($model, 'is_recurring')->checkbox() ?>
    
    <div class="form-group full" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('Save Expense', ['class' => 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<style>
.help-block {
    color: var(--red2);
    font-size: 11px;
    margin-top: 4px;
}
.form-control:focus {
    border-color: var(--accent);
    outline: none;
}
</style>