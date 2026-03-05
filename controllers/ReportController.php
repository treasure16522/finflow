<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\Income;
use app\models\Expense;
use app\models\ExpenseCategory;
use app\helpers\CurrencyHelper;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends Controller
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
        ];
    }
        public function actionIndex()
{
    $userId = Yii::$app->user->id;
    $year = Yii::$app->request->get('year', date('Y'));
    $month = Yii::$app->request->get('month', date('m'));
    
    // Calculate statistics
    $monthlyIncome = $this->getMonthlyIncome($userId, $year, $month);
    $monthlyExpenses = $this->getMonthlyExpenses($userId, $year, $month);
    $netSavings = $monthlyIncome - $monthlyExpenses;
    $savingsRate = $monthlyIncome > 0 ? round(($netSavings / $monthlyIncome) * 100, 1) : 0;
    
    // Get 6-month chart data for bar chart
    $sixMonthData = $this->getSixMonthChartData($userId);
    
    // Get 12-month chart data for line chart
    $yearlyChartData = $this->getYearlyChartData($userId, $year);
    
    // Get category breakdown for pie chart
    $categoryData = $this->getCategoryBreakdown($userId, $year, $month);
    
    // Get top expenses
    $topExpenses = Expense::find()
        ->with('category')
        ->where(['user_id' => $userId])
        ->andWhere(['>=', 'date', "$year-$month-01"])
        ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
        ->orderBy(['amount' => SORT_DESC])
        ->limit(5)
        ->all();
    
    return $this->render('index', [
        'year' => $year,
        'month' => $month,
        'monthlyIncome' => $monthlyIncome,
        'monthlyExpenses' => $monthlyExpenses,
        'netSavings' => $netSavings,
        'savingsRate' => $savingsRate,
        'chartData' => $sixMonthData,      // For 6-month bar chart
        'yearlyChartData' => $yearlyChartData, // For 12-month line chart
        'categoryData' => $categoryData,    // For pie chart
        'topExpenses' => $topExpenses,
    ]);
}
private function getSixMonthChartData($userId)
{
    $months = [];
    $incomeData = [];
    $expenseData = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m-01', strtotime("-$i months"));
        $monthName = date('M', strtotime($month));
        $months[] = $monthName;
        
        $incomeData[] = $this->getMonthlyIncomeTotal($userId, $month);
        $expenseData[] = $this->getMonthlyExpenseTotal($userId, $month);
    }

    return [
        'months' => $months,
        'income' => $incomeData,
        'expenses' => $expenseData,
    ];
}

private function getMonthlyIncomeTotal($userId, $month)
{
    return Income::find()
        ->where(['user_id' => $userId])
        ->andWhere(['>=', 'date', $month])
        ->andWhere(['<', 'date', date('Y-m-01', strtotime('+1 month', strtotime($month)))])
        ->sum('amount') ?: 0;
}

private function getMonthlyExpenseTotal($userId, $month)
{
    return Expense::find()
        ->where(['user_id' => $userId])
        ->andWhere(['>=', 'date', $month])
        ->andWhere(['<', 'date', date('Y-m-01', strtotime('+1 month', strtotime($month)))])
        ->sum('amount') ?: 0;
}

    public function actionExport($type, $year, $month)
    {
        $userId = Yii::$app->user->id;
        
        if ($type == 'csv') {
            return $this->exportCsv($userId, $year, $month);
        } elseif ($type == 'pdf') {
            return $this->exportPdf($userId, $year, $month);
        } elseif ($type == 'excel') {
            Yii::$app->session->setFlash('info', 'Excel export coming soon!');
            return $this->redirect(['index', 'year' => $year, 'month' => $month]);
        }
        
        Yii::$app->session->setFlash('error', 'Invalid export type');
        return $this->redirect(['index', 'year' => $year, 'month' => $month]);
    }

    private function getMonthlyIncome($userId, $year, $month)
    {
        return Income::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->sum('amount') ?: 0;
    }

    private function getMonthlyExpenses($userId, $year, $month)
    {
        return Expense::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->sum('amount') ?: 0;
    }

    private function getYearlyChartData($userId, $year)
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $monthName = date('M', strtotime("$year-$m-01"));
            $months[] = $monthName;
            
            $incomeData[] = $this->getMonthlyIncome($userId, $year, $m);
            $expenseData[] = $this->getMonthlyExpenses($userId, $year, $m);
        }
        
        return [
            'months' => $months,
            'income' => $incomeData,
            'expenses' => $expenseData,
        ];
    }

    private function getCategoryBreakdown($userId, $year, $month)
    {
        $expenses = Expense::find()
            ->with('category')
            ->select(['category_id', 'SUM(amount) as total'])
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->groupBy('category_id')
            ->asArray()
            ->all();
            
        $total = array_sum(array_column($expenses, 'total'));
        $breakdown = [];
        
        foreach ($expenses as $exp) {
            if ($exp['category_id']) {
                $category = ExpenseCategory::findOne($exp['category_id']);
                $percentage = $total > 0 ? round(($exp['total'] / $total) * 100, 1) : 0;
                
                $breakdown[] = [
                    'name' => $category->name ?? 'Unknown',
                    'icon' => $category->icon ?? '📦',
                    'amount' => $exp['total'],
                    'percentage' => $percentage,
                ];
            }
        }
        
        return $breakdown;
    }

    private function exportCsv($userId, $year, $month)
    {
        $filename = "finflow_report_{$year}_{$month}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['FinFlow Financial Report']);
        fputcsv($output, ["Period: " . date('F Y', strtotime("$year-$month-01"))]);
        fputcsv($output, []);
        
        // Summary
        $totalIncome = $this->getMonthlyIncome($userId, $year, $month);
        $totalExpenses = $this->getMonthlyExpenses($userId, $year, $month);
        $netSavings = $totalIncome - $totalExpenses;
        $savingsRate = $totalIncome > 0 ? round(($netSavings / $totalIncome) * 100, 1) : 0;
        
        fputcsv($output, ['SUMMARY']);
        fputcsv($output, ['Total Income', 'UGX ' . number_format($totalIncome)]);
        fputcsv($output, ['Total Expenses', 'UGX ' . number_format($totalExpenses)]);
        fputcsv($output, ['Net Savings', 'UGX ' . number_format($netSavings)]);
        fputcsv($output, ['Savings Rate', $savingsRate . '%']);
        fputcsv($output, []);
        
        // Income
        fputcsv($output, ['INCOME']);
        fputcsv($output, ['Date', 'Source', 'Category', 'Amount (UGX)', 'Note']);
        
        $incomes = Income::find()
            ->with('category')
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->orderBy(['date' => SORT_DESC])
            ->all();
            
        foreach ($incomes as $income) {
            fputcsv($output, [
                $income->date,
                $income->source,
                $income->category->name ?? 'Uncategorized',
                $income->amount,
                $income->note
            ]);
        }
        
        fputcsv($output, []);
        
        // Expenses
        fputcsv($output, ['EXPENSES']);
        fputcsv($output, ['Date', 'Description', 'Category', 'Amount (UGX)', 'Note']);
        
        $expenses = Expense::find()
            ->with('category')
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->orderBy(['date' => SORT_DESC])
            ->all();
            
        foreach ($expenses as $expense) {
            fputcsv($output, [
                $expense->date,
                $expense->description,
                $expense->category->name ?? 'Uncategorized',
                $expense->amount,
                $expense->note
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportPdf($userId, $year, $month)
    {
        // Get data for the report
        $totalIncome = $this->getMonthlyIncome($userId, $year, $month);
        $totalExpenses = $this->getMonthlyExpenses($userId, $year, $month);
        $netSavings = $totalIncome - $totalExpenses;
        $savingsRate = $totalIncome > 0 ? round(($netSavings / $totalIncome) * 100, 1) : 0;
        
        $incomes = Income::find()
            ->with('category')
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->orderBy(['date' => SORT_DESC])
            ->all();
            
        $expenses = Expense::find()
            ->with('category')
            ->where(['user_id' => $userId])
            ->andWhere(['>=', 'date', "$year-$month-01"])
            ->andWhere(['<', 'date', date('Y-m-01', strtotime("$year-$month-01 +1 month"))])
            ->orderBy(['date' => SORT_DESC])
            ->all();
            
        $categoryBreakdown = $this->getCategoryBreakdown($userId, $year, $month);
        
        // Generate HTML content for PDF
        $html = $this->renderPartial('pdf', [
            'year' => $year,
            'month' => $month,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netSavings' => $netSavings,
            'savingsRate' => $savingsRate,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'categoryBreakdown' => $categoryBreakdown,
            'user' => Yii::$app->user->identity,
        ]);
        
        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output the PDF
        $filename = "finflow_report_{$year}_{$month}.pdf";
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}