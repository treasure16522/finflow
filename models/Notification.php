<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Notification extends ActiveRecord
{
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';

    public static function tableName()
    {
        return 'notifications';
    }

    public function rules()
    {
        return [
            [['type', 'title', 'message'], 'required'],
            [['type'], 'in', 'range' => [self::TYPE_DANGER, self::TYPE_WARNING, self::TYPE_INFO, self::TYPE_SUCCESS]],
            [['title'], 'string', 'max' => 100],
            [['message'], 'string'],
            [['is_read'], 'boolean'],
            [['data'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'title' => 'Title',
            'message' => 'Message',
            'is_read' => 'Read',
            'data' => 'Data',
            'created_at' => 'Received At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->user_id = Yii::$app->user->id;
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }

    public static function createAlert($userId, $type, $title, $message, $data = null)
    {
        $notification = new self();
        $notification->user_id = $userId;
        $notification->type = $type;
        $notification->title = $title;
        $notification->message = $message;
        $notification->data = $data;
        return $notification->save();
    }

    public function getTimeAgo()
    {
        $time = strtotime($this->created_at);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }

    public function getIcon()
    {
        switch ($this->type) {
            case self::TYPE_DANGER:
                return '🚨';
            case self::TYPE_WARNING:
                return '⚠️';
            case self::TYPE_INFO:
                return 'ℹ️';
            case self::TYPE_SUCCESS:
                return '✅';
            default:
                return '🔔';
        }
    }

    public function getAlertClass()
    {
        switch ($this->type) {
            case self::TYPE_DANGER:
                return 'alert-danger';
            case self::TYPE_WARNING:
                return 'alert-warn';
            case self::TYPE_INFO:
                return 'alert-info';
            case self::TYPE_SUCCESS:
                return 'alert-success';
            default:
                return '';
        }
    }
}