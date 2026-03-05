<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Income;
use app\models\Expense;
use app\models\Budget;
use app\models\SavingsGoal;
use app\models\Notification;
use app\models\ExpenseCategory;

class DashboardController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $currentMonth = date('Y-m-01');
        
        // Calculate totals
        $totalBalance = $this->getTotalBalance($userId);
        $monthlyIncome = $this->getMonthlyIncome($userId, $currentMonth);
        $monthlyExpenses = $this->getMonthlyExpenses($userId, $currentMonth);
        $savingsRate = $monthlyIncome > 0 ? round(($monthlyIncome - $monthlyExpenses) / $monthlyIncome * 100) : 0;

        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions($userId, 10);

        // Get budget overview
        $budgetOverview = $this->getBudgetOverview($userId, $currentMonth);

        // Get chart data
        $chartData = $this->getMonthlyChartData($userId);
        $categoryData = $this->getCategoryBreakdown($userId, $currentMonth);

        // Get unread notifications count
        $unreadNotifications = Notification::find()
            ->where(['user_id' => $userId, 'is_read' => false])
            ->count();

        // Pass data to view
        return $this->render('index', [
            'totalBalance' => $totalBalance,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpenses' => $monthlyExpenses,
            'savingsRate' => $savingsRate,
            'recentTransactions' => $recentTransactions,
            'budgetOverview' => $budgetOverview,
            'chartData' => $chartData,
            'categoryData' => $categoryData,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    private function getTotalBalance($userId)
    {
        $totalIncome = Income::find()->where(['user_id' => $userId])->sum('amount') ?: 0;
        $totalExpenses = Expense::find()->where(['user_id' => $userId])->sum('amount') ?: 0;
        return $totalIncome - $totalExpenses;
    }

    private function getMonthlyIncome($userId, $month)
    {
        return Income::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $month])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime('+1 month', strtotime($month)))])
            ->sum('amount') ?: 0;
    }

    private function getMonthlyExpenses($userId, $month)
    {
        return Expense::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $month])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime('+1 month', strtotime($month)))])
            ->sum('amount') ?: 0;
    }

    private function getRecentTransactions($userId, $limit)
    {
        $incomes = Income::find()
            ->with('category')
            ->where(['user_id' => $userId])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC])
            ->limit($limit)
            ->all();

        $expenses = Expense::find()
            ->with('category')
            ->where(['user_id' => $userId])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC])
            ->limit($limit)
            ->all();

        $transactions = array_merge($incomes, $expenses);
        usort($transactions, function($a, $b) {
            return strtotime($b->date) - strtotime($a->date);
        });

        return array_slice($transactions, 0, $limit);
    }

    private function getBudgetOverview($userId, $month)
    {
        $budgets = Budget::find()
            ->with('category')
            ->where(['user_id' => $userId, 'month' => $month])
            ->all();

        $overview = [];
        foreach ($budgets as $budget) {
            $overview[] = [
                'id' => $budget->id,
                'category' => $budget->category->name ?? 'Unknown',
                'icon' => $budget->category->icon ?? '📊',
                'planned' => $budget->planned_amount,
                'actual' => $budget->actual_amount,
                'percentage' => $budget->getSpentPercentage(),
                'status' => $budget->getStatus(),
                'status_text' => $budget->getStatusText(),
                'remaining' => $budget->getRemainingAmount(),
            ];
        }

        return $overview;
    }

    private function getMonthlyChartData($userId)
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M', strtotime($month));
            $months[] = $monthName;
            
            $incomeData[] = $this->getMonthlyIncome($userId, $month);
            $expenseData[] = $this->getMonthlyExpenses($userId, $month);
        }

        return [
            'months' => $months,
            'income' => $incomeData,
            'expenses' => $expenseData,
        ];
    }

    private function getCategoryBreakdown($userId, $month)
    {
        $expenses = Expense::find()
            ->with('category')
            ->select(['category_id', 'SUM(amount) as total'])
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', $month])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime('+1 month', strtotime($month)))])
            ->groupBy('category_id')
            ->asArray()
            ->all();

        $total = array_sum(array_column($expenses, 'total'));
        
        $categories = [];
        foreach ($expenses as $expense) {
            if ($expense['category_id']) {
                $category = ExpenseCategory::findOne($expense['category_id']);
                $percentage = $total > 0 ? round(($expense['total'] / $total) * 100) : 0;
                
                $categories[] = [
                    'name' => $category->name ?? 'Unknown',
                    'icon' => $category->icon ?? '📦',
                    'color' => $category->color ?? 'var(--accent)',
                    'amount' => $expense['total'],
                    'percentage' => $percentage,
                ];
            }
        }

        return $categories;
    }
}