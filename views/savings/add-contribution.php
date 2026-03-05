<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\CurrencyHelper;

$this->title = 'Add Contribution - ' . $goal->name;
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <div class="card-title">
        <span>➕</span> Add Contribution to "<?= Html::encode($goal->name) ?>"
    </div>
    
    <div style="background: var(--bg3); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="color: var(--text3);">Target Amount:</span>
            <span style="font-weight: 600; color: var(--gold2);"><?= CurrencyHelper::formatUGX($goal->target_amount) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="color: var(--text3);">Current Savings:</span>
            <span style="font-weight: 600; color: var(--green2);"><?= CurrencyHelper::formatUGX($goal->current_amount) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text3);">Remaining:</span>
            <span style="font-weight: 600; color: var(--text);"><?= CurrencyHelper::formatUGX($goal->getRemainingAmount()) ?></span>
        </div>
    </div>
    
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-grid'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group full'],
            'labelOptions' => ['class' => 'control-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'help-block'],
        ],
    ]); ?>
    
    <div class="form-group">
        <?= $form->field($model, 'amount')->textInput([
            'type' => 'number',
            'step' => '1000',
            'min' => '1000',
            'placeholder' => 'e.g. 50000',
            'autofocus' => true
        ]) ?>
        <div style="font-size: 11px; color: var(--text3); margin-top: 4px;">Amount in UGX</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'date')->textInput([
            'type' => 'date',
            'value' => date('Y-m-d')
        ]) ?>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'note')->textInput([
            'placeholder' => 'e.g. Monthly salary, Bonus, Gift'
        ]) ?>
    </div>
    
    <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
        <?= Html::a('Cancel', ['view', 'id' => $goal->id], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('➕ Add Contribution', ['class' => 'btn btn-green']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<style>
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
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
}

.form-group {
    margin-bottom: 15px;
}

.help-block {
    color: var(--red2);
    font-size: 11px;
    margin-top: 4px;
}
</style>