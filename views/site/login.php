<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Login - FinFlow';
?>

<div class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <div class="login-logo-title">FinFlow</div>
            <div class="login-logo-sub">Personal Finance Manager</div>
        </div>
        
        <div class="login-title">Welcome back</div>
        <div class="login-sub">Sign in to your account</div>
        
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'login-form'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'control-label'],
                'inputOptions' => ['class' => 'form-control'],
                'errorOptions' => ['class' => 'help-block'],
            ],
        ]); ?>
        
        <div class="form-group">
            <?= $form->field($model, 'email')->textInput([
                'autofocus' => true,
                'placeholder' => 'johndoe@example.com',
                'type' => 'email',
            ]) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => '*********',
            ]) ?>
        </div>
        
        <div class="form-group">
            <?= Html::submitButton('✦ Sign In', ['class' => 'btn btn-info login-btn', 'name' => 'login-button']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
        
        <div class="login-links">
            <?= Html::a('Create account', ['site/signup'], ['class' => 'login-link']) ?>
            <?= Html::a('Forgot password?', ['site/request-password-reset'], ['class' => 'login-link']) ?>
        </div>
    </div>
</div>

<style>
.login-page {
    position: fixed;
    inset: 0;
    background: #0A0F1E;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 500;
}

.login-box {
    width: 400px;
    max-width: 90vw;
    background-color: #0F1C32;
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.login-logo {
    text-align: center;
    margin-bottom: 28px;
}

.login-logo-title {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 900;
    background: linear-gradient(135deg, var(--accent2), var(--gold2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.login-logo-sub {
    color: var(--text3);
    font-size: 13px;
    margin-top: 4px;
    letter-spacing: 0.5px;
}

.login-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
    color: var(--text);
}

.login-sub {
    color: var(--text3);
    font-size: 13px;
    margin-bottom: 24px;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.login-form .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 0;
}

.login-form .form-group label {
    font-size: 12px;
    color: var(--text3);
    font-weight: 500;
    letter-spacing: 0.3px;
}

.login-form .form-group input {
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

.login-form .form-group input:focus {
    border-color: var(--accent);
}

.login-btn {
    padding: 13px;
    font-size: 15px;
    width: 100%;
    border-radius: 12px;
    justify-content: center;
    margin-top: 8px;
    background: linear-gradient(135deg, var(--accent), #2563eb);
    color: white;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.login-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(59,130,246,0.4);
}

.login-links {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    font-size: 12px;
}

.login-link {
    color: var(--accent2);
    cursor: pointer;
    text-decoration: none;
    font-weight: 500;
}

.login-link:hover {
    text-decoration: underline;
}

.help-block {
    color: var(--red2);
    font-size: 11px;
    margin-top: 4px;
}
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
</style>