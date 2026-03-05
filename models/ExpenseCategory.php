<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ExpenseCategory extends ActiveRecord
{
    public static function tableName()
    {
        return 'expense_categories';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['icon'], 'string', 'max' => 10],
            [['color'], 'string', 'max' => 20],
            [['monthly_budget'], 'number', 'min' => 0],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'icon' => 'Icon',
            'color' => 'Color',
            'monthly_budget' => 'Monthly Budget',
            'is_active' => 'Active',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getExpenses()
    {
        return $this->hasMany(Expense::class, ['category_id' => 'id']);
    }

    public function getBudgets()
    {
        return $this->hasMany(Budget::class, ['category_id' => 'id']);
    }
}