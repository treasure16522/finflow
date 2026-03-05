<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\CurrencyHelper;

$this->title = 'Update Income';
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-title">
        <span>✏️</span> Update Income
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
    
    <?= $form->field($model, 'source')->textInput(['placeholder' => 'e.g. Salary, Freelance, Business']) ?>
    
    <?= $form->field($model, 'amount')->textInput([
        'type' => 'number', 
        'step' => '1000',
        'placeholder' => 'e.g. 500000'
    ]) ?>
    
    <?= $form->field($model, 'category_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($categories, 'id', function($category) {
            return ($category->icon ?? '💰') . ' ' . $category->name;
        }),
        ['prompt' => '-- Select Category --']
    ) ?>
    
    <div class="form-group">
        <?= $form->field($model, 'date')->textInput(['type' => 'date']) ?>
    </div>
    
    <?= $form->field($model, 'note')->textarea(['rows' => 3, 'placeholder' => 'Optional notes about this income']) ?>
    
    <?= $form->field($model, 'is_recurring')->checkbox() ?>
    
    <div class="form-group full" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-info']) ?>
        <?= Html::submitButton('Update Income', ['class' => 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>