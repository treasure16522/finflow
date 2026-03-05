<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Notification;
use app\models\UserSettings;
use yii\data\ActiveDataProvider;

class AlertController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-all' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        $dataProvider = new ActiveDataProvider([
            'query' => Notification::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        // Get counts by type
        $criticalCount = Notification::find()
            ->where(['user_id' => $userId, 'type' => Notification::TYPE_DANGER, 'is_read' => false])
            ->count();
            
        $warningCount = Notification::find()
            ->where(['user_id' => $userId, 'type' => Notification::TYPE_WARNING, 'is_read' => false])
            ->count();
            
        $infoCount = Notification::find()
            ->where(['user_id' => $userId, 'type' => Notification::TYPE_INFO, 'is_read' => false])
            ->count();
            
        $resolvedCount = Notification::find()
            ->where(['user_id' => $userId, 'is_read' => true])
            ->count();

        // Get user settings
        $settings = UserSettings::findOne(['user_id' => $userId]);
        if (!$settings) {
            $settings = new UserSettings();
            $settings->user_id = $userId;
            $settings->save();
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'criticalCount' => $criticalCount,
            'warningCount' => $warningCount,
            'infoCount' => $infoCount,
            'resolvedCount' => $resolvedCount,
            'settings' => $settings,
        ]);
    }

    public function actionMarkRead($id)
    {
        $model = Notification::findOne($id);
        
        if ($model && $model->user_id == Yii::$app->user->id) {
            $model->is_read = true;
            $model->save();
        }
        
        return $this->redirect(['index']);
    }

    public function actionMarkAllRead()
    {
        Notification::updateAll(
            ['is_read' => true],
            ['user_id' => Yii::$app->user->id, 'is_read' => false]
        );
        
        Yii::$app->session->setFlash('success', 'All notifications marked as read.');
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $model = Notification::findOne($id);
        
        if ($model && $model->user_id == Yii::$app->user->id) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Notification deleted.');
        }
        
        return $this->redirect(['index']);
    }

    public function actionDeleteAll()
    {
        Notification::deleteAll(['user_id' => Yii::$app->user->id]);
        Yii::$app->session->setFlash('success', 'All notifications cleared.');
        return $this->redirect(['index']);
    }

    public function actionSaveSettings()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $settings = UserSettings::findOne(['user_id' => $userId]);
        
        if (!$settings) {
            $settings = new UserSettings();
            $settings->user_id = $userId;
        }
        
        $data = Yii::$app->request->post();
        
        $settings->budget_alert_threshold = $data['budget_alert_threshold'] ?? 80;
        $settings->email_notifications = $data['email_notifications'] ?? true;
        $settings->push_notifications = $data['push_notifications'] ?? true;
        $settings->weekly_digest = $data['weekly_digest'] ?? false;
        $settings->monthly_summary = $data['monthly_summary'] ?? true;
        
        if ($settings->save()) {
            return ['success' => true, 'message' => 'Settings saved successfully!'];
        }
        
        return ['success' => false, 'message' => 'Failed to save settings.'];
    }
}