<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class GoalContribution extends ActiveRecord
{
    public static function tableName()
    {
        return 'goal_contributions';
    }

    public function rules()
    {
        return [
            [['goal_id', 'amount', 'date'], 'required'],
            [['goal_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['date'], 'safe'],
            [['note'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goal_id' => 'Goal ID',
            'amount' => 'Amount',
            'date' => 'Date',
            'note' => 'Note',
            'created_at' => 'Created At',
        ];
    }

    public function getGoal()
    {
        return $this->hasOne(SavingsGoal::class, ['id' => 'goal_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($insert) {
            // Update goal current amount
            $goal = $this->goal;
            $goal->current_amount += $this->amount;
            $goal->save();
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        
        // Update goal current amount
        $goal = $this->goal;
        $goal->current_amount -= $this->amount;
        if ($goal->current_amount < 0) {
            $goal->current_amount = 0;
        }
        $goal->save();
    }
}