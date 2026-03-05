<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Expense;
use app\models\ExpenseCategory;
use app\models\Budget;
use yii\data\ActiveDataProvider;

class ExpenseController extends Controller
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
        $currentMonth = date('Y-m-01');
        
        $query = Expense::find()
            ->with('category')
            ->where(['user_id' => $userId]);

        // Apply filters
        $categoryId = Yii::$app->request->get('category_id');
        $dateRange = Yii::$app->request->get('date_range');
        
        if ($categoryId) {
            $query->andWhere(['category_id' => $categoryId]);
        }
        
        if ($dateRange == 'this_month') {
            $query->andWhere(['>=', 'date', $currentMonth]);
        } elseif ($dateRange == 'last_month') {
            $lastMonth = date('Y-m-01', strtotime('-1 month'));
            $query->andWhere(['>=', 'date', $lastMonth])
                  ->andWhere(['<', 'date', $currentMonth]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        // Get statistics
        $totalSpent = Expense::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $currentMonth])
            ->sum('amount') ?: 0;
            
        $transactionCount = Expense::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $currentMonth])
            ->count();
            
        // Daily average
        $daysInMonth = date('t');
        $dailyAverage = $daysInMonth > 0 ? $totalSpent / $daysInMonth : 0;
        
        // Categories under budget
        $budgets = Budget::find()
            ->where(['user_id' => $userId, 'month' => $currentMonth])
            ->all();
        $categoriesUnderBudget = 0;
        foreach ($budgets as $budget) {
            if ($budget->getStatus() == 'success') {
                $categoriesUnderBudget++;
            }
        }

        // Get categories for filter dropdown
        $categories = ExpenseCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'totalSpent' => $totalSpent,
            'dailyAverage' => $dailyAverage,
            'transactionCount' => $transactionCount,
            'categoriesUnderBudget' => $categoriesUnderBudget,
            'categories' => $categories,
        ]);
    }

    public function actionCreate()
    {
        $model = new Expense();
        $userId = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Expense recorded successfully.');
            return $this->redirect(['index']);
        }

        $categories = ExpenseCategory::find()
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
            Yii::$app->session->setFlash('success', 'Expense updated successfully.');
            return $this->redirect(['index']);
        }

        $categories = ExpenseCategory::find()
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
        Yii::$app->session->setFlash('success', 'Expense deleted successfully.');
        
        return $this->redirect(['index']);
    }

    public function actionAjaxCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new Expense();
        $model->user_id = Yii::$app->user->id;
        $model->date = date('Y-m-d');
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'Expense added successfully!',
                'data' => $model->attributes
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to add expense.',
            'errors' => $model->errors
        ];
    }

    public function actionQuickAdd()
    {
        $model = new Expense();
        $model->user_id = Yii::$app->user->id;
        $model->date = date('Y-m-d');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Expense added successfully!');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to add expense.');
        }
        
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}