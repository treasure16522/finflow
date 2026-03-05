<?php
namespace app\models;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    public function sendEmail()
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        // In a real application, you would send an email here
        // For demo purposes, we'll just return true
        Yii::$app->session->setFlash('success', 'Password reset link sent to your email.');
        
        return true;
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email Address',
        ];
    }
}