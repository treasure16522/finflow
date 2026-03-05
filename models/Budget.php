<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Budget extends ActiveRecord
{
    public static function tableName()
    {
        return 'budgets';
    }

    public function rules()
    {
        return [
            [['category_id', 'month', 'planned_amount'], 'required'],
            [['planned_amount', 'actual_amount'], 'number', 'min' => 0],
            [['month'], 'date', 'format' => 'php:Y-m-d'],
            [['alert_threshold'], 'integer', 'min' => 1, 'max' => 100],
            [['category_id'], 'exist', 'targetClass' => ExpenseCategory::class, 'targetAttribute' => 'id'],
            [['user_id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
            'month' => 'Month',
            'planned_amount' => 'Planned Amount (UGX)',
            'actual_amount' => 'Actual Amount (UGX)',
            'alert_threshold' => 'Alert Threshold (%)',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(ExpenseCategory::class, ['id' => 'category_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->user_id = Yii::$app->user->id;
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    public function getSpentPercentage()
    {
        if ($this->planned_amount <= 0) {
            return 0;
        }
        return round(($this->actual_amount / $this->planned_amount) * 100, 1);
    }

    public function getRemainingAmount()
    {
        return $this->planned_amount - $this->actual_amount;
    }

    public function getStatus()
    {
        $percentage = $this->getSpentPercentage();
        if ($percentage >= 100) {
            return 'danger';
        } elseif ($percentage >= $this->alert_threshold) {
            return 'warning';
        }
        return 'success';
    }

    public function getStatusText()
    {
        $percentage = $this->getSpentPercentage();
        if ($percentage >= 100) {
            return 'Over Budget';
        } elseif ($percentage >= $this->alert_threshold) {
            return 'Near Limit';
        }
        return 'On Track';
    }
}