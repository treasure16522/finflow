<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Income;
use app\models\IncomeCategory;
use yii\data\ActiveDataProvider;

class IncomeController extends Controller
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
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        $dataProvider = new ActiveDataProvider([
            'query' => Income::find()
                ->with('category')
                ->where(['user_id' => $userId])
                ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        // Get statistics
        $currentMonth = date('Y-m-01');
        $lastMonth = date('Y-m-01', strtotime('-1 month'));
        
        $currentMonthTotal = Income::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $currentMonth])
            ->sum('amount') ?: 0;
            
        $lastMonthTotal = Income::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $lastMonth])
            ->andWhere(['<', 'date', $currentMonth])
            ->sum('amount') ?: 0;
            
        $ytdTotal = Income::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', date('Y-01-01')])
            ->sum('amount') ?: 0;
            
        $sourcesCount = Income::find()
            ->where(['user_id' => $userId])
            ->select('category_id')
            ->distinct()
            ->count();

        // Get categories for dropdown
        $categories = IncomeCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentMonthTotal' => $currentMonthTotal,
            'lastMonthTotal' => $lastMonthTotal,
            'ytdTotal' => $ytdTotal,
            'sourcesCount' => $sourcesCount,
            'categories' => $categories,
        ]);
    }

    public function actionCreate()
    {
        $model = new Income();
        $userId = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Income record added successfully.');
            return $this->redirect(['index']);
        }

        $categories = IncomeCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $userId = Yii::$app->user->id;
        
        if ($model->user_id !== $userId) {
            throw new NotFoundHttpException('You don\'t have permission to update this record.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Income record updated successfully.');
            return $this->redirect(['index']);
        }

        $categories = IncomeCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to delete this record.');
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Income record deleted successfully.');
        
        return $this->redirect(['index']);
    }

    public function actionAjaxCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new Income();
        $model->user_id = Yii::$app->user->id;
        $model->date = date('Y-m-d');
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'Income added successfully!',
                'data' => $model->attributes
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to add income.',
            'errors' => $model->errors
        ];
    }

    protected function findModel($id)
    {
        if (($model = Income::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}