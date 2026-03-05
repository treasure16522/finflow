<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class UserSettings extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_settings';
    }

    public function rules()
    {
        return [
            [['budget_alert_threshold'], 'integer', 'min' => 1, 'max' => 100],
            [['email_notifications', 'push_notifications', 'weekly_digest', 'monthly_summary'], 'boolean'],
            [['theme'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'budget_alert_threshold' => 'Budget Alert Threshold (%)',
            'email_notifications' => 'Email Notifications',
            'push_notifications' => 'Push Notifications',
            'weekly_digest' => 'Weekly Digest',
            'monthly_summary' => 'Monthly Summary',
            'theme' => 'Theme',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }
}