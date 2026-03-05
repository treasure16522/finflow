<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Income extends ActiveRecord
{
    public static function tableName()
    {
        return 'incomes';
    }

    public function rules()
    {
        return [
            [['source', 'amount', 'date'], 'required'],
            [['amount'], 'number', 'min' => 0],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['source'], 'string', 'max' => 100],
            [['note'], 'string'],
            [['is_recurring'], 'boolean'],
            [['recurring_period'], 'in', 'range' => ['daily', 'weekly', 'monthly', 'yearly']],
            [['category_id'], 'exist', 'targetClass' => IncomeCategory::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source' => 'Source',
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
        return $this->hasOne(IncomeCategory::class, ['id' => 'category_id']);
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
}