<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\SavingsGoal;
use app\models\GoalContribution;
use yii\data\ActiveDataProvider;

class SavingsController extends Controller
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
                    'delete-contribution' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        $activeGoalsProvider = new ActiveDataProvider([
            'query' => SavingsGoal::find()
                ->where(['user_id' => $userId, 'is_completed' => false])
                ->orderBy(['deadline' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        $completedGoalsProvider = new ActiveDataProvider([
            'query' => SavingsGoal::find()
                ->where(['user_id' => $userId, 'is_completed' => true])
                ->orderBy(['completed_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        // Get statistics
        $totalSaved = SavingsGoal::find()
            ->where(['user_id' => $userId])
            ->sum('current_amount') ?: 0;
            
        $activeGoalsCount = SavingsGoal::find()
            ->where(['user_id' => $userId, 'is_completed' => false])
            ->count();
            
        $completedGoalsCount = SavingsGoal::find()
            ->where(['user_id' => $userId, 'is_completed' => true])
            ->count();
            
        // Next milestone
        $nextMilestone = SavingsGoal::find()
            ->where(['user_id' => $userId, 'is_completed' => false])
            ->andWhere(['>', 'deadline', date('Y-m-d')])
            ->orderBy(['deadline' => SORT_ASC])
            ->one();

        return $this->render('index', [
            'activeGoalsProvider' => $activeGoalsProvider,
            'completedGoalsProvider' => $completedGoalsProvider,
            'totalSaved' => $totalSaved,
            'activeGoalsCount' => $activeGoalsCount,
            'completedGoalsCount' => $completedGoalsCount,
            'nextMilestone' => $nextMilestone,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to view this goal.');
        }
        
        $contributionsProvider = new ActiveDataProvider([
            'query' => GoalContribution::find()
                ->where(['goal_id' => $model->id])
                ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'contributionsProvider' => $contributionsProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new SavingsGoal();
        $model->current_amount = 0;
        $model->is_completed = false;

        if ($model->load(Yii::$app->request->post())) {
            // Handle date input (comes as YYYY-MM from form)
            $deadline = Yii::$app->request->post('SavingsGoal')['deadline'] ?? null;
            if ($deadline) {
                $model->deadline = $deadline . '-01'; // Add day to make valid date
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Savings goal created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to update this goal.');
        }

        if ($model->load(Yii::$app->request->post())) {
            // Handle date input
            $deadline = Yii::$app->request->post('SavingsGoal')['deadline'] ?? null;
            if ($deadline) {
                $model->deadline = $deadline . '-01';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Savings goal updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to delete this goal.');
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Savings goal deleted successfully.');
        
        return $this->redirect(['index']);
    }

    public function actionAddContribution($goalId)
    {
        $goal = $this->findModel($goalId);
        
        if ($goal->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to add to this goal.');
        }
        
        if ($goal->is_completed) {
            Yii::$app->session->setFlash('error', 'Cannot add contributions to a completed goal.');
            return $this->redirect(['view', 'id' => $goalId]);
        }

        $model = new GoalContribution();
        $model->goal_id = $goalId;
        $model->date = date('Y-m-d');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Contribution added successfully.');
            
            // Check if goal is now completed
            $goal->refresh();
            if ($goal->is_completed) {
                Yii::$app->session->setFlash('success', '🎉 Congratulations! You\'ve reached your goal!');
            }
            
            return $this->redirect(['view', 'id' => $goalId]);
        }

        return $this->render('add-contribution', [
            'model' => $model,
            'goal' => $goal,
        ]);
    }

    public function actionDeleteContribution($id)
    {
        $model = GoalContribution::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('Contribution not found.');
        }
        
        $goal = $model->goal;
        
        if ($goal->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to delete this contribution.');
        }
        
        $goalId = $goal->id;
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Contribution deleted successfully.');
        return $this->redirect(['view', 'id' => $goalId]);
    }

    public function actionAjaxCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new SavingsGoal();
        $model->current_amount = 0;
        $model->is_completed = false;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'Savings goal created successfully!',
                'data' => $model->attributes
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to create goal.',
            'errors' => $model->errors
        ];
    }

    protected function findModel($id)
    {
        if (($model = SavingsGoal::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}