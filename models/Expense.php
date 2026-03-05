<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Expense extends ActiveRecord
{
    public static function tableName()
    {
        return 'expenses';
    }

    public function rules()
    {
        return [
            [['description', 'amount', 'date'], 'required'],
            [['amount'], 'number', 'min' => 0],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['description'], 'string', 'max' => 200],
            [['note'], 'string'],
            [['is_recurring'], 'boolean'],
            [['recurring_period'], 'in', 'range' => ['daily', 'weekly', 'monthly', 'yearly']],
            [['category_id'], 'exist', 'targetClass' => ExpenseCategory::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'amount' => 'Amount',
            'date' => 'Date',
            'note' => 'Note',
            'category_id' => 'Category',
            'is_recurring' => 'Is Recurring',
            'recurring_period' => 'Recurring Period',
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($insert) {
            // Update budget actual amount
            $this->updateBudgetActual();
        }
    }

    private function updateBudgetActual()
    {
        if (!$this->category_id) return;

        $month = date('Y-m-01', strtotime($this->date));
        
        $budget = Budget::find()
            ->where([
                'user_id' => $this->user_id,
                'category_id' => $this->category_id,
                'month' => $month
            ])
            ->one();

        if ($budget) {
            $totalExpenses = Expense::find()
                ->where([
                    'user_id' => $this->user_id,
                    'category_id' => $this->category_id
                ])
                ->andWhere(['>=', 'date', $month])
                ->andWhere(['<', 'date', date('Y-m-01', strtotime('+1 month', strtotime($month)))])
                ->sum('amount');

            $budget->actual_amount = $totalExpenses ?: 0;
            $budget->save();
        }
    }
}