<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class SavingsGoal extends ActiveRecord
{
    public static function tableName()
    {
        return 'savings_goals'; // Matches your database table name
    }

    public function rules()
    {
        return [
            [['name', 'target_amount'], 'required'],
            [['user_id'], 'integer'],
            [['target_amount', 'current_amount', 'monthly_contribution'], 'number', 'min' => 0],
            [['deadline'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 10],
            [['color'], 'string', 'max' => 20],
            [['is_completed'], 'boolean'],
            [['completed_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Goal Name',
            'target_amount' => 'Target Amount',
            'current_amount' => 'Current Amount',
            'deadline' => 'Target Date',
            'icon' => 'Icon',
            'color' => 'Color',
            'monthly_contribution' => 'Monthly Contribution',
            'is_completed' => 'Completed',
            'completed_at' => 'Completed At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getContributions()
    {
        return $this->hasMany(GoalContribution::class, ['goal_id' => 'id'])->orderBy(['date' => SORT_DESC]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->user_id = Yii::$app->user->id;
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            
            // Auto-complete goal if current_amount reaches or exceeds target
            if (!$this->is_completed && $this->current_amount >= $this->target_amount) {
                $this->is_completed = true;
                $this->completed_at = date('Y-m-d H:i:s');
                $this->current_amount = $this->target_amount; // Cap at target
            }
            
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Create notification if goal is completed
        if (isset($changedAttributes['is_completed']) && $this->is_completed && !$changedAttributes['is_completed']) {
            Notification::createAlert(
                $this->user_id,
                Notification::TYPE_SUCCESS,
                'Goal Completed! 🎉',
                "Congratulations! You've reached your savings goal: {$this->name}",
                ['goal_id' => $this->id]
            );
        }
    }

    public function getProgressPercentage()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return round(($this->current_amount / $this->target_amount) * 100, 1);
    }

    public function getRemainingAmount()
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getDaysLeft()
    {
        if (!$this->deadline) {
            return null;
        }
        $today = new \DateTime();
        $deadline = new \DateTime($this->deadline);
        $interval = $today->diff($deadline);
        return $interval->days * ($interval->invert ? -1 : 1);
    }

    public function getMonthlyNeeded()
    {
        $daysLeft = $this->getDaysLeft();
        $remaining = $this->getRemainingAmount();
        
        if ($daysLeft && $daysLeft > 0 && $remaining > 0) {
            $monthsLeft = ceil($daysLeft / 30);
            return $remaining / $monthsLeft;
        }
        return 0;
    }
}