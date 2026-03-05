<?php
use yii\helpers\Html;
use app\helpers\CurrencyHelper;

// Set page margins for PDF
$this->registerCss("
    body { 
        font-family: Helvetica, Arial, sans-serif; 
        margin: 20px; 
        color: #333333;
        line-height: 1.4;
    }
    h1 { 
        color: #3b82f6; 
        font-size: 24px; 
        margin-bottom: 5px; 
        font-weight: bold;
    }
    h2 { 
        color: #1e293b; 
        font-size: 18px; 
        border-bottom: 2px solid #3b82f6; 
        padding-bottom: 5px; 
        margin-top: 25px; 
        margin-bottom: 15px;
        font-weight: bold;
    }
    .header { 
        text-align: center; 
        margin-bottom: 20px; 
        padding-bottom: 15px; 
        border-bottom: 2px solid #3b82f6; 
    }
    .summary { 
        background-color: #f8fafc; 
        padding: 15px; 
        border-radius: 5px; 
        margin-bottom: 20px; 
        border: 1px solid #cbd5e1;
    }
    .summary-grid { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 10px; 
    }
    .summary-item { 
        padding: 12px; 
        background: white; 
        border-radius: 5px; 
        border: 1px solid #cbd5e1;
    }
    .summary-label { 
        color: #64748b; 
        font-size: 12px; 
        margin-bottom: 4px;
    }
    .summary-value { 
        color: #0f172a; 
        font-size: 18px; 
        font-weight: bold; 
    }
    .footer { 
        margin-top: 30px; 
        text-align: center; 
        color: #64748b; 
        font-size: 10px; 
        border-top: 1px solid #cbd5e1;
        padding-top: 15px;
    }
");
?>

<div class="header">
    <h1>FinFlow Financial Report</h1>
    <p style="font-size: 14px; color: #475569; margin: 5px 0;"><strong><?= date('F Y', strtotime("$year-$month-01")) ?></strong></p>
    <p style="font-size: 12px; color: #64748b; margin: 3px 0;">Generated for: <?= Html::encode($user->getFullName()) ?> (<?= Html::encode($user->email) ?>)</p>
    <p style="font-size: 12px; color: #64748b; margin: 3px 0;">Generated on: <?= date('F j, Y') ?></p>
</div>

<!-- Summary Section -->
<h2>Executive Summary</h2>
<div class="summary">
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-label">Total Income</div>
            <div class="summary-value positive">UGX <?= number_format($totalIncome) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Expenses</div>
            <div class="summary-value negative">UGX <?= number_format($totalExpenses) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Net Savings</div>
            <div class="summary-value" style="color: <?= $netSavings >= 0 ? '#059669' : '#dc2626' ?>;">
                UGX <?= number_format($netSavings) ?>
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Savings Rate</div>
            <div class="summary-value"><?= $savingsRate ?>%</div>
        </div>
    </div>
</div>

<!-- Category Breakdown -->
<?php if (!empty($categoryBreakdown)): ?>
<h2>Spending by Category</h2>
<div style="margin: 15px 0;">
    <!-- TABLE WITH INLINE STYLES FOR GUARANTEED BORDERS -->
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000000; font-size: 11px;">
        <thead>
            <tr style="background-color: #1e40af; color: white;">
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 60%;">Category</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: right; width: 20%;">Amount (UGX)</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: right; width: 20%;">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $index = 0; // Initialize index counter
            foreach ($categoryBreakdown as $category): 
            ?>
            <tr style="background-color: <?= $index++ % 2 == 0 ? '#ffffff' : '#f1f5f9' ?>;">
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($category['name']) ?></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right; font-family: 'Courier New', monospace;">UGX <?= number_format($category['amount']) ?></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right;"><?= $category['percentage'] ?>%</td>
            </tr>
            <?php endforeach; ?>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td style="border: 1px solid #000000; padding: 8px;"><strong>Total</strong></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right; font-family: 'Courier New', monospace;"><strong>UGX <?= number_format($totalExpenses) ?></strong></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right;"><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Income Section -->
<?php if (!empty($incomes)): ?>
<h2>Income Details</h2>
<div style="margin: 15px 0;">
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000000; font-size: 11px;">
        <thead>
            <tr style="background-color: #1e40af; color: white;">
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 15%;">Date</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 25%;">Source</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 20%;">Category</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: right; width: 20%;">Amount (UGX)</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 20%;">Note</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $incomeTotal = 0;
            $index = 0; // Initialize index counter
            foreach ($incomes as $income): 
                $incomeTotal += $income->amount;
            ?>
            <tr style="background-color: <?= $index++ % 2 == 0 ? '#ffffff' : '#f1f5f9' ?>;">
                <td style="border: 1px solid #000000; padding: 8px;"><?= Yii::$app->formatter->asDate($income->date, 'php:M j, Y') ?></td>
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($income->source) ?></td>
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($income->category->name ?? 'Uncategorized') ?></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right; font-family: 'Courier New', monospace; color: #059669;"><?= number_format($income->amount) ?></td>
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($income->note) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="3" style="border: 1px solid #000000; padding: 8px;"><strong>Total Income</strong></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right; font-family: 'Courier New', monospace; color: #059669;"><strong>UGX <?= number_format($incomeTotal) ?></strong></td>
                <td style="border: 1px solid #000000; padding: 8px;"></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Expenses Section -->
<?php if (!empty($expenses)): ?>
<h2>Expense Details</h2>
<div style="margin: 15px 0;">
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000000; font-size: 11px;">
        <thead>
            <tr style="background-color: #1e40af; color: white;">
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 15%;">Date</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 25%;">Description</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 20%;">Category</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: right; width: 20%;">Amount (UGX)</th>
                <th style="border: 1px solid #000000; padding: 8px; text-align: left; width: 20%;">Note</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $expenseTotal = 0;
            $index = 0; // Initialize index counter
            foreach ($expenses as $expense): 
                $expenseTotal += $expense->amount;
            ?>
            <tr style="background-color: <?= $index++ % 2 == 0 ? '#ffffff' : '#f1f5f9' ?>;">
                <td style="border: 1px solid #000000; padding: 8px;"><?= Yii::$app->formatter->asDate($expense->date, 'php:M j, Y') ?></td>
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($expense->description) ?></td>
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($expense->category->name ?? 'Uncategorized') ?></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right; font-family: 'Courier New', monospace; color: #dc2626;"><?= number_format($expense->amount) ?></td>
                <td style="border: 1px solid #000000; padding: 8px;"><?= Html::encode($expense->note) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="3" style="border: 1px solid #000000; padding: 8px;"><strong>Total Expenses</strong></td>
                <td style="border: 1px solid #000000; padding: 8px; text-align: right; font-family: 'Courier New', monospace; color: #dc2626;"><strong>UGX <?= number_format($expenseTotal) ?></strong></td>
                <td style="border: 1px solid #000000; padding: 8px;"></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="footer">
    <p>FinFlow - Personal Finance Manager | This report was generated automatically</p>
</div>