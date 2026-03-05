<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Set Budget';
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <div class="card-title">
        <span>📊</span> Set New Budget
    </div>
    
    <?php $form = ActiveForm::begin([
        'id' => 'budget-form',
        'method' => 'post',
        'options' => ['class' => 'form-grid'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'control-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'help-block'],
        ],
    ]); ?>
    
    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map($categories, 'id', function($category) {
            return ($category->icon ?? '📊') . ' ' . $category->name;
        }),
        ['prompt' => '-- Select Category --', 'class' => 'form-control']
    ) ?>
    
    <div class="form-group">
        <?= $form->field($model, 'planned_amount')->textInput([
            'type' => 'number',
            'step' => '1000',
            'min' => '0',
            'placeholder' => 'e.g. 1000000',
            'value' => $model->planned_amount ?: ''
        ]) ?>
        <div class="field-hint">Amount in UGX</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'month')->textInput([
            'type' => 'month',
            'value' => date('Y-m', strtotime($model->month ?: date('Y-m-01'))),
            'class' => 'form-control'
        ]) ?>
        <div class="field-hint">Select the month for this budget</div>
    </div>
    
    <div class="form-group">
        <?= $form->field($model, 'alert_threshold')->textInput([
            'type' => 'number',
            'min' => 1,
            'max' => 100,
            'value' => $model->alert_threshold ?: 80
        ]) ?>
        <div class="field-hint">Alert when this % of budget is used (1-100)</div>
    </div>
    
    <div class="form-group full" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-ghost']) ?>
        <?= Html::submitButton('Create Budget', ['class' => 'btn btn-primary']) ?>
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
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 5px;
}

.form-group label {
    font-size: 12px;
    color: var(--text2);
    font-weight: 500;
    letter-spacing: 0.3px;
    margin-bottom: 5px;
    display: block;
}

.form-control {
    background: var(--bg3);
    border: 1px solid var(--border);
    color: var(--text);
    padding: 12px 14px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s;
    width: 100%;
}

.form-control:focus {
    border-color: var(--accent);
}

select.form-control option {
    background: var(--bg2);
    color: var(--text);
}

.field-hint {
    font-size: 11px;
    color: var(--text3);
    margin-top: 4px;
}

.help-block {
    color: var(--red2);
    font-size: 11px;
    margin-top: 4px;
}

.btn {
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent), #2563eb);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(59,130,246,0.4);
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('budget-form');
    const monthInput = document.querySelector('input[name="Budget[month]"]');
    
    if (form && monthInput) {
        form.addEventListener('submit', function(e) {
            // Get the month value (YYYY-MM)
            let monthValue = monthInput.value;
            
            if (monthValue) {
                // Ensure it's in YYYY-MM format
                if (monthValue.length === 7) {
                    // Convert from YYYY-MM to YYYY-MM-01 for the database
                    monthInput.value = monthValue + '-01';
                }
            }
        });
    }
    
    // Pre-select category if passed in URL
    <?php if (Yii::$app->request->get('category_id')): ?>
    const categorySelect = document.querySelector('select[name="Budget[category_id]"]');
    if (categorySelect) {
        categorySelect.value = '<?= Yii::$app->request->get('category_id') ?>';
    }
    <?php endif; ?>
});
</script>