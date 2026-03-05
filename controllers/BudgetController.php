<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Budget;
use app\models\ExpenseCategory;
use app\models\Expense;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class BudgetController extends Controller
{
    /**
     * {@inheritdoc}
     */
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
                    'ajax-create' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Budget records for the selected month.
     * @return mixed
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        // Get month parameter from request
        $monthParam = Yii::$app->request->get('month');
        
        // If month parameter is provided, ensure it's in full date format
        if ($monthParam) {
            // If it's in YYYY-MM format, convert to YYYY-MM-01
            if (strlen($monthParam) == 7) {
                $selectedMonth = $monthParam . '-01';
            } else {
                $selectedMonth = $monthParam;
            }
        } else {
            // Default to current month
            $selectedMonth = date('Y-m-01');
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => Budget::find()
                ->with('category')
                ->where(['user_id' => $userId, 'month' => $selectedMonth])
                ->orderBy(['category_id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        // Get statistics
        $totalBudget = Budget::find()
            ->where(['user_id' => $userId, 'month' => $selectedMonth])
            ->sum('planned_amount') ?: 0;
            
        $totalSpent = Budget::find()
            ->where(['user_id' => $userId, 'month' => $selectedMonth])
            ->sum('actual_amount') ?: 0;
            
        $remaining = $totalBudget - $totalSpent;
        
        // Count overspent budgets (actual > planned)
        $overspentCount = Budget::find()
            ->where(['user_id' => $userId, 'month' => $selectedMonth])
            ->andWhere('actual_amount > planned_amount')
            ->count();

        // Get categories without budgets for this month
        $categoriesWithBudgets = Budget::find()
            ->select('category_id')
            ->where(['user_id' => $userId, 'month' => $selectedMonth])
            ->column();
            
        $availableCategories = ExpenseCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->andWhere(['not in', 'id', $categoriesWithBudgets])
            ->all();

        // Format the month for display in the view
        $displayMonth = date('F Y', strtotime($selectedMonth));

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remaining' => $remaining,
            'overspentCount' => $overspentCount,
            'selectedMonth' => $selectedMonth,
            'displayMonth' => $displayMonth,
            'availableCategories' => $availableCategories,
        ]);
    }

    /**
     * Creates a new Budget record.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Budget();
        $userId = Yii::$app->user->id;
        
        // Set default values
        $model->month = date('Y-m-01');
        $model->alert_threshold = 80;
        $model->actual_amount = 0;

        if ($model->load(Yii::$app->request->post())) {
            // Validate the model first
            if ($model->validate()) {
                // Ensure month is in full date format (YYYY-MM-01)
                if (strlen($model->month) == 7) {
                    $model->month = $model->month . '-01';
                }
                
                // Check if budget already exists for this category and month
                $existing = Budget::find()
                    ->where([
                        'user_id' => $userId,
                        'category_id' => $model->category_id,
                        'month' => $model->month
                    ])
                    ->exists();
                    
                if ($existing) {
                    Yii::$app->session->setFlash('error', 'A budget for this category already exists for the selected month.');
                } else {
                    // Set user_id before saving
                    $model->user_id = $userId;
                    
                    if ($model->save()) {
                        // Calculate actual amount from existing expenses
                        $this->updateBudgetActual($model);
                        
                        Yii::$app->session->setFlash('success', 'Budget created successfully.');
                        
                        // Return with month in YYYY-MM format for URL
                        $returnMonth = date('Y-m', strtotime($model->month));
                        return $this->redirect(['index', 'month' => $returnMonth]);
                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to save budget. Please check the form.');
                    }
                }
            } else {
                Yii::$app->session->setFlash('error', 'Please fix the validation errors.');
            }
        }

        // Get available categories for the dropdown
        $categories = ExpenseCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    /**
     * Updates an existing Budget record.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $userId = Yii::$app->user->id;
        
        if ($model->user_id !== $userId) {
            throw new NotFoundHttpException('You don\'t have permission to update this budget.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Ensure month is in full date format
            if (strlen($model->month) == 7) {
                $model->month = $model->month . '-01';
            }
            
            if ($model->save()) {
                $this->updateBudgetActual($model);
                Yii::$app->session->setFlash('success', 'Budget updated successfully.');
                return $this->redirect(['index', 'month' => $model->month]);
            }
        }

        $categories = ExpenseCategory::find()
            ->where(['user_id' => $userId])
            ->orWhere(['user_id' => null])
            ->andWhere(['is_active' => true])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    /**
     * Deletes an existing Budget record.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('You don\'t have permission to delete this budget.');
        }

        $month = $model->month;
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Budget deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to delete budget.');
        }
        
        return $this->redirect(['index', 'month' => $month]);
    }

    /**
     * Recalculates actual amounts for all budgets in a month based on expenses.
     * @param string $month
     * @return mixed
     */
    public function actionRecalculate($month = null)
    {
        $userId = Yii::$app->user->id;
        
        if ($month) {
            if (strlen($month) == 7) {
                $month = $month . '-01';
            }
        } else {
            $month = date('Y-m-01');
        }
        
        $budgets = Budget::find()
            ->where(['user_id' => $userId, 'month' => $month])
            ->all();
            
        $count = 0;
        foreach ($budgets as $budget) {
            if ($this->updateBudgetActual($budget)) {
                $count++;
            }
        }
        
        Yii::$app->session->setFlash('success', "$count budget(s) recalculated successfully.");
        return $this->redirect(['index', 'month' => date('Y-m', strtotime($month))]);
    }

    /**
     * AJAX action for creating budget from modal.
     * @return array
     */
    public function actionAjaxCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new Budget();
        $model->user_id = Yii::$app->user->id;
        $model->month = date('Y-m-01');
        $model->alert_threshold = 80;
        $model->actual_amount = 0;
        
        if ($model->load(Yii::$app->request->post())) {
            // Ensure month format
            if (strlen($model->month) == 7) {
                $model->month = $model->month . '-01';
            }
            
            // Check for existing
            $existing = Budget::find()
                ->where([
                    'user_id' => $model->user_id,
                    'category_id' => $model->category_id,
                    'month' => $model->month
                ])
                ->exists();
                
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'A budget for this category already exists for this month.'
                ];
            }
            
            if ($model->save()) {
                $this->updateBudgetActual($model);
                return [
                    'success' => true,
                    'message' => 'Budget created successfully!',
                    'data' => $model->attributes
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create budget.',
                    'errors' => $model->errors
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'No data received.'
        ];
    }

    /**
     * Updates the actual_amount of a budget based on expenses for that month.
     * @param Budget $budget
     * @return boolean
     */
    private function updateBudgetActual($budget)
    {
        $nextMonth = date('Y-m-01', strtotime('+1 month', strtotime($budget->month)));
        
        $totalSpent = Expense::find()
            ->where([
                'user_id' => $budget->user_id,
                'category_id' => $budget->category_id
            ])
            ->andWhere(['>=', 'date', $budget->month])
            ->andWhere(['<', 'date', $nextMonth])
            ->sum('amount');
            
        $budget->actual_amount = $totalSpent ?: 0;
        return $budget->save();
    }

    /**
     * Finds the Budget model based on its primary key value.
     * @param integer $id
     * @return Budget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Budget::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}