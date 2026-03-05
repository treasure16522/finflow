<?php
namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'phone', 'password'], 'required'],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^[0-9+\-\s()]+$/', 'message' => 'Please enter a valid phone number.'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $user = new User();
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->username = $this->email; // Use email as username for simplicity
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->currency = 'USD';
        $user->role = 'user';
        $user->status = 1;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save();
    }

    public function attributeLabels()
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'password' => 'Password',
        ];
    }
}