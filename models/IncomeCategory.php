<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class IncomeCategory extends ActiveRecord
{
    public static function tableName()
    {
        return 'income_categories';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['icon'], 'string', 'max' => 10],
            [['color'], 'string', 'max' => 20],
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
            'is_active' => 'Active',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getIncomes()
    {
        return $this->hasMany(Income::class, ['category_id' => 'id']);
    }
}