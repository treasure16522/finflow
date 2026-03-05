<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\CurrencyHelper;

$this->title = 'Reports & Analytics';
$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$monthIndex = (int)$month - 1;
$currentMonthName = $months[$monthIndex] ?? 'Unknown';
?>

<!-- Statistics Cards -->
<div class="stat-grid">
    <div class="stat-card green">
        <div class="stat-icon">📈</div>
        <div class="stat-label">Net Savings (<?= $currentMonthName ?>)</div>
        <div class="stat-val green"><?= CurrencyHelper::formatUGX($netSavings) ?></div>
        <div class="stat-change up">↑ <?= $savingsRate ?>% savings rate</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">📊</div>
        <div class="stat-label">Savings Rate</div>
        <div class="stat-val blue"><?= $savingsRate ?>%</div>
        <div class="stat-change">of income saved</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">💸</div>
        <div class="stat-label">Avg Daily Spend</div>
        <div class="stat-val red">
            <?php 
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, $year);
            $avgDaily = $daysInMonth > 0 ? $monthlyExpenses / $daysInMonth : 0;
            echo CurrencyHelper::formatUGX($avgDaily);
            ?>
        </div>
        <div class="stat-change"><?= $currentMonthName ?> <?= $year ?></div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">🏆</div>
        <div class="stat-label">Best Category</div>
        <div class="stat-val gold">
            <?= !empty($categoryData) ? $categoryData[0]['icon'] : '📊' ?>
        </div>
        <div class="stat-change">
            <?= !empty($categoryData) ? $categoryData[0]['name'] : 'N/A' ?>
        </div>
    </div>
</div>

<!-- Date Selector -->
<div class="card" style="margin-bottom: 16px;">
    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 18px;">📅</span>
            <select class="month-select" onchange="window.location.href = '<?= Url::to(['index']) ?>?year=' + this.value.split('-')[0] + '&month=' + this.value.split('-')[1]">
                <?php for ($i = 0; $i < 12; $i++): 
                    $y = date('Y', strtotime("-$i months"));
                    $m = date('m', strtotime("-$i months"));
                    $selected = ($y == $year && $m == $month) ? 'selected' : '';
                ?>
                    <option value="<?= $y ?>-<?= $m ?>" <?= $selected ?>>
                        <?= date('F Y', strtotime("-$i months")) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <?= Html::a('📥 CSV', ['export', 'type' => 'csv', 'year' => $year, 'month' => $month], [
                'class' => 'btn btn-info btn-sm'
            ]) ?>
            <?= Html::a('📄 PDF', ['export', 'type' => 'pdf', 'year' => $year, 'month' => $month], [
                'class' => 'btn btn-primary btn-sm'
            ]) ?>
        </div>
    </div>
</div>


<!-- Spending by Category Pie Chart -->
<div class="card" style="margin-bottom: 16px;">
    <div class="card-title"><span>🎯</span> Spending by Category - <?= $currentMonthName ?> <?= $year ?></div>
    <div class="pie-wrap">
        <?php 
        if (!empty($categoryData)): 
            $colors = [ 
                '#3b82f6', // blue
                '#10b981', // green
                '#f59e0b', // gold
                '#8b5cf6', // purple
                '#14b8a6', // teal
                '#ef4444', // red
                '#60a5fa'  // light blue
            ];
            $totalSpent = array_sum(array_column($categoryData, 'amount'));
            $startAngle = 0;
            $conicGradient = [];
            
            foreach ($categoryData as $index => $category): 
                $percentage = $category['percentage'];
                $endAngle = $startAngle + ($percentage * 3.6);
                $conicGradient[] = $colors[$index % count($colors)] . ' ' . $startAngle . 'deg ' . $endAngle . 'deg';
                $startAngle = $endAngle;
            endforeach;
        ?>
            <div class="pie" style="background: conic-gradient(<?= implode(', ', $conicGradient) ?>)">
                <div class="pie-inner">
                    <?= CurrencyHelper::formatUGX($totalSpent, false) ?><br>
                    <span style="font-size: 10px;">total</span>
                </div>
            </div>
            <div class="pie-legend">
                <?php foreach ($categoryData as $index => $category): ?>
                <div class="pie-item">
                    <div class="pie-dot" style="background: <?= $colors[$index % count($colors)] ?>;"></div>
                    <span class="pie-name"><?= Html::encode($category['name']) ?></span>
                    <span class="pie-pct"><?= $category['percentage'] ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty" style="width: 100%;">
                <div class="empty-icon">📊</div>
                <div class="empty-text">No expense data for <?= $currentMonthName ?> <?= $year ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 12-Month Trend Line Chart -->
<div class="card" style="margin-bottom: 16px;">
    <div class="card-title"><span>📈</span> 12-Month Overview - <?= $year ?></div>
    
    <div style="height: 200px; position: relative; padding: 10px 0;">
        <svg viewBox="0 0 1200 200" preserveAspectRatio="none" style="width: 100%; height: 100%;">
            <!-- Grid lines -->
            <?php for ($i = 0; $i <= 5; $i++): ?>
                <line x1="50" y1="<?= 30 + ($i * 30) ?>" x2="1150" y2="<?= 30 + ($i * 30) ?>" 
                      stroke="var(--border)" stroke-width="0.5" stroke-dasharray="5,5"/>
            <?php endfor; ?>
            
            <!-- Income line -->
            <?php 
            $maxValue = max(array_merge($yearlyChartData['income'], $yearlyChartData['expenses']));
            $maxValue = $maxValue ?: 1;
            $points = [];
            foreach ($yearlyChartData['income'] as $i => $value) {
                $x = 100 + ($i * 90);
                $y = 180 - (($value / $maxValue) * 150);
                $points[] = "$x,$y";
            }
            $pointsStr = implode(' ', $points);
            ?>
            <polyline points="<?= $pointsStr ?>" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linejoin="round"/>
            
            <!-- Expense line -->
            <?php 
            $points = [];
            foreach ($yearlyChartData['expenses'] as $i => $value) {
                $x = 100 + ($i * 90);
                $y = 180 - (($value / $maxValue) * 150);
                $points[] = "$x,$y";
            }
            $pointsStr = implode(' ', $points);
            ?>
            <polyline points="<?= $pointsStr ?>" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linejoin="round"/>
            
            <!-- Data points for income -->
            <?php foreach ($yearlyChartData['income'] as $i => $value): ?>
                <circle cx="<?= 100 + ($i * 90) ?>" cy="<?= 180 - (($value / $maxValue) * 150) ?>" r="3" fill="#10b981"/>
            <?php endforeach; ?>
            
            <!-- Data points for expenses -->
            <?php foreach ($yearlyChartData['expenses'] as $i => $value): ?>
                <circle cx="<?= 100 + ($i * 90) ?>" cy="<?= 180 - (($value / $maxValue) * 150) ?>" r="3" fill="#ef4444"/>
            <?php endforeach; ?>
            
            <!-- Month labels -->
            <?php foreach ($yearlyChartData['months'] as $i => $monthName): ?>
                <text x="<?= 100 + ($i * 90) ?>" y="195" fill="var(--text3)" font-size="10" text-anchor="middle">
                    <?= $monthName ?>
                </text>
            <?php endforeach; ?>
        </svg>
    </div>

<div class="grid-2" style="margin-top: 10px;">
    <!-- Category Breakdown Table -->
    <div class="card">
        <div class="card-title"><span>📊</span> Category Breakdown - <?= $currentMonthName ?> <?= $year ?></div>
        
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount (UGX)</th>
                        <th>% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categoryData)): ?>
                        <?php foreach ($categoryData as $category): ?>
                            <tr>
                                <td>
                                    <span class="td-cat">
                                        <?= $category['icon'] ?> <?= Html::encode($category['name']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right; font-family: monospace;"><?= CurrencyHelper::formatUGX($category['amount'], false) ?></td>
                                <td><?= $category['percentage'] ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="border-top: 1px solid var(--border);">
                            <td><strong>Total</strong></td>
                            <td style="text-align: right; font-family: monospace;"><strong><?= CurrencyHelper::formatUGX($monthlyExpenses, false) ?></strong></td>
                            <td>100%</td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="empty">
                                <div class="empty-icon">📊</div>
                                <div class="empty-text">No expense data for this month</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
<!-- Export Options -->
<div class="card" style="margin-top: 16px;">
    <div class="card-title"><span>💾</span> Export Reports</div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div>
            <div class="form-group">
                <label>Report Type</label>
                <select id="reportType" class="form-control">
                    <option value="monthly">Monthly Summary</option>
                    <option value="category">Category Breakdown</option>
                    <option value="income">Income Report</option>
                    <option value="expense">Expense Report</option>
                </select>
            </div>
        </div>
        <div>
            <div class="form-group">
                <label>Format</label>
                <select id="exportFormat" class="form-control">
                    <option value="csv">CSV (Spreadsheet)</option>
                    <option value="pdf" selected>PDF Document</option>
                </select>
            </div>
        </div>
        <div style="display: flex; align-items: flex-end;">
            <button class="btn btn-primary" style="width: 100%; justify-content: center; " onclick="exportReport()">
                📥 Download Report
            </button>
        </div>
    </div>
    
</div>

<style>
/* Chart styles */
.bar-chart {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    height: 180px;
    padding-bottom: 4px;
    margin: 20px 0;
}

.bar-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    height: 100%;
    justify-content: flex-end;
}

.bars {
    display: flex;
    gap: 4px;
    align-items: flex-end;
    width: 100%;
    justify-content: center;
}

.bar {
    border-radius: 5px 5px 0 0;
    width: 25px;
    transition: opacity 0.2s;
    cursor: pointer;
}

.bar:hover {
    opacity: 0.75;
}

.bar.income {
    background: linear-gradient(to top, #3b82f6, #60a5fa);
}

.bar.expense {
    background: linear-gradient(to top, #ef4444, #f87171);
}

.bar-label {
    font-size: 11px;
    color: var(--text3);
    font-weight: 500;
}

.chart-legend {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    justify-content: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text2);
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

/* Pie chart styles */
.pie-wrap {
    display: flex;
    align-items: center;
    gap: 30px;
    min-height: 180px;
    padding: 10px 0;
}

.pie {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.pie-inner {
    position: absolute;
    inset: 25px;
    background: var(--card);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--text2);
    font-weight: 600;
    flex-direction: column;
    gap: 2px;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.3);
}

.pie-legend {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px 15px;
}

.pie-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    padding: 4px 0;
}

.pie-dot {
    width: 12px;
    height: 12px;
    border-radius: 4px;
    flex-shrink: 0;
}

.pie-name {
    color: var(--text2);
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pie-pct {
    color: var(--text);
    font-weight: 600;
    min-width: 45px;
    text-align: right;
}

.empty {
    text-align: center;
    padding: 30px 20px;
    color: var(--text3);
    width: 100%;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.5;
}

.tx-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.tx-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px;
    border-radius: 10px;
    transition: background 0.15s;
}

.tx-item:hover {
    background: var(--card2);
}

.tx-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.tx-name {
    font-size: 13px;
    font-weight: 500;
}

.tx-cat {
    font-size: 11px;
    color: var(--text3);
}

.tx-date {
    font-size: 11px;
    color: var(--text3);
    margin-left: auto;
    margin-right: 10px;
}

.tx-amt {
    font-size: 14px;
    font-weight: 600;
    font-family: monospace;
}

.tx-amt.pos {
    color: var(--green2);
}

.tx-amt.neg {
    color: var(--red2);
}

.tx-amt.pos::before {
    content: '+';
    margin-right: 2px;
}

.tx-amt.neg::before {
    content: '-';
    margin-right: 2px;
}

/* Card and table styles */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
}

.card-title {
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text3);
    padding: 10px 12px;
    border-bottom: 1px solid var(--border);
}

td {
    padding: 13px 12px;
    font-size: 13px;
    border-bottom: 1px solid rgba(99,179,237,0.06);
    vertical-align: middle;
}

.td-cat {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--bg3);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

.divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 16px 0;
}
</style>

<script>
function exportReport() {
    const type = document.getElementById('reportType').value;
    const format = document.getElementById('exportFormat').value;
    const year = <?= $year ?>;
    const month = <?= $month ?>;
    
    if (format === 'excel') {
        showToast('📊', 'Excel export coming soon!');
        return;
    }
    
    window.location.href = '<?= Url::to(['export']) ?>?type=' + format + '&year=' + year + '&month=' + month + '&report=' + type;
}

function showToast(icon, msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toastIcon').textContent = icon;
    document.getElementById('toastMsg').textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2800);
}
</script>