<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?> — FinFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0f1e;
            --bg2: #0d1526;
            --bg3: #111d35;
            --card: #0F1C32;
            --card2: #132040;
            --border: rgba(99,179,237,0.12);
            --accent: #3b82f6;
            --accent2: #60a5fa;
            --gold: #f59e0b;
            --gold2: #fbbf24;
            --green: #10b981;
            --green2: #34d399;
            --red: #ef4444;
            --red2: #f87171;
            --purple: #8b5cf6;
            --teal: #14b8a6;
            --text: #e2e8f0;
            --text2: #94a3b8;
            --text3: #64748b;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .logo {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
        }
        .logo-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--accent2), var(--gold2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo-sub { font-size: 11px; color: var(--text3); letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }

        .nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section { margin-bottom: 6px; }
        .nav-label { font-size: 10px; color: var(--text3); letter-spacing: 1.5px; text-transform: uppercase; padding: 8px 12px 4px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            color: white;
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 2px;
            position: relative;
            text-decoration: none;
        }
        .nav-item:hover { background: var(--card2); color: var(--text); }
        .nav-item.active { 
            background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(139,92,246,0.1)); 
            color: var(--accent2); 
            font-weight: 500; 
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-icon { font-size: 18px; width: 22px; text-align: center; }
        .nav-badge {
            margin-left: auto;
            background: var(--red);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }

        .user-section {
            padding: 16px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: white;
        }
        .user-name { font-size: 14px; font-weight: 500; 
            color: #FFFFFF;}
        .user-role { font-size: 11px; color: var(--text3); }
        .user-logout {
            margin-left: auto;
            cursor: pointer;
            color: var(--text3);
            font-size: 18px;
            transition: color 0.2s;
            text-decoration: none;
        }
        .user-logout:hover { color: var(--red); }

        /* Main */
        .main {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color:white;
        }

        /* Topbar */
        .topbar {
            height: 64px;
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
        }
        .topbar-actions {
            margin-left: auto;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .notif-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--card);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            position: relative;
            transition: background 0.2s;
            text-decoration: none;
            color: var(--text);
        }
        .notif-btn:hover { background: var(--card2); }
        .notif-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--red);
            border: 2px solid var(--bg2);
        }
        .month-select {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 14px;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }

        /* Content */
        .content {
            padding: 28px;
            flex: 1;
            animation: fadeIn 0.3s ease;
            background-color: #0A0F1E;
        }
        @keyframes fadeIn {
            from { opacity:0; transform: translateY(8px); }
            to { opacity:1; transform: translateY(0); }
        }

        /* Stat Cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
            background-color: #0A0F1E;
        }
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
            cursor: default;
        }
        .stat-card:hover { transform: translateY(-2px); border-color: rgba(99,179,237,0.25); }
        .stat-card::after {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.06;
        }
        .stat-card.blue::after { background: var(--accent); }
        .stat-card.green::after { background: var(--green); }
        .stat-card.red::after { background: var(--red); }
        .stat-card.gold::after { background: var(--gold); }
        .stat-card.purple::after { background: var(--purple); }
        .stat-label { font-size: 12px; color: var(--text3); letter-spacing: 0.5px; text-transform: uppercase; font-weight: 500; }
        .stat-val { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; margin: 6px 0 4px; }
        .stat-val.blue { color: var(--accent2); }
        .stat-val.green { color: var(--green2); }
        .stat-val.red { color: var(--red2); }
        .stat-val.gold { color: var(--gold2); }
        .stat-val.purple { color: var(--purple); }
        .stat-change { font-size: 12px; color: var(--text3); display: flex; align-items: center; gap: 4px; }
        .stat-change.up { color: var(--green2); }
        .stat-change.down { color: var(--red2); }
        .stat-icon { font-size: 22px; margin-bottom: 10px; }

        /* Grid Layouts */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; background-color: #0A0F1E;}
        .grid-3 { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 24px; background-color: #0A0F1E;}
        .grid-eq { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px; background-color: #0A0F1E;}

        /* Cards */
        .card {
            background-color: var(--card) #0F1C32;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color:white; 
        }
        .card-title span { font-size: 18px; }
        .card-title .badge {
            margin-left: auto;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 500;
            padding: 3px 10px;
            border-radius: 20px;
            background: rgba(59,130,246,0.15);
            color: var(--accent2);
            cursor: pointer;
        }

        /* Bar Chart */
        .bar-chart {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            height: 140px;
            padding-bottom: 4px;
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
            width: 14px;
            transition: opacity 0.2s;
            cursor: pointer;
        }
        .bar:hover { opacity: 0.75; }
        .bar.income { background: linear-gradient(to top, var(--accent), var(--accent2)); }
        .bar.expense { background: linear-gradient(to top, #ef4444, #f87171); }
        .bar-label { font-size: 10px; color: var(--text3); }
        .chart-legend {
            display: flex;
            gap: 16px;
            margin-top: 14px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text2);
        }
        .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

        /* Pie Chart */
        .pie-wrap {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .pie {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(
                var(--accent) 0deg 115deg,
                var(--green) 115deg 187deg,
                var(--gold) 187deg 241deg,
                var(--purple) 241deg 295deg,
                var(--teal) 295deg 360deg
            );
            position: relative;
            flex-shrink: 0;
        }
        .pie-inner {
            position: absolute;
            inset: 22px;
            background: var(--card);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--text3);
            font-weight: 600;
            flex-direction: column;
            gap: 1px;
        }
        .pie-legend {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .pie-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }
        .pie-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            flex-shrink: 0;
        }
        .pie-name { color: var(--text2); flex: 1; }
        .pie-pct { color: var(--text); font-weight: 600; }

        /* Transactions */
        .tx-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .tx-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.15s;
            cursor: pointer;
        }
        .tx-item:hover { background: var(--card2); }
        .tx-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .tx-name { font-size: 14px; font-weight: 500; color: white;}
        .tx-cat { font-size: 12px; color: var(--text3); }
        .tx-date {
            font-size: 12px;
            color: var(--text3);
            margin-left: auto;
            margin-right: 16px;
        }
        .tx-amt { font-size: 15px; font-weight: 600; }
        .tx-amt.pos { color: var(--green2); }
        .tx-amt.neg { color: var(--red2); }

        /* Progress Bars */
        .progress-wrap {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .progress-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .progress-name { font-weight: 500; color:white; }
        .progress-vals { color: var(--text3); }
        .progress-bar-bg {
            background: var(--bg3);
            border-radius: 50px;
            height: 8px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 50px;
            transition: width 0.6s cubic-bezier(.4,0,.2,1);
        }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: white;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            color: white;
        }
        td {
            padding: 13px 12px;
            font-size: 13px;
            border-bottom: 1px solid rgba(99,179,237,0.06);
            vertical-align: middle;
            color: white;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(59,130,246,0.04); }
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
        .td-actions { display: flex; gap: 6px; }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #2563eb);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(59,130,246,0.4); }
        .btn-ghost {
            background: var(--card2);
            color: var(--text2);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { background: var(--bg3); color: var(--text); }
        .btn-green {
            background: linear-gradient(135deg, var(--green), #059669);
            color: white;
        }
        .btn-green:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(16,185,129,0.4); }
        .btn-red {
            background: rgba(239,68,68,0.15);
            color: var(--red2);
            border: 1px solid rgba(239,68,68,0.2);
        }
        .btn-red:hover { background: rgba(239,68,68,0.25); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* Alerts */
        .alert-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .alert-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 12px;
            border-left: 3px solid;
            background: var(--bg3);
        }
        .alert-icon { font-size: 20px; margin-top: 1px; }
        .alert-title { font-size: 13px; font-weight: 600; margin-bottom: 3px; color:white; }
        .alert-body { font-size: 12px; color: var(--text3); }
        .alert-time {
            margin-left: auto;
            font-size: 11px;
            color: var(--text3);
            white-space: nowrap;
        }
        .alert-warn { border-color: var(--gold); }
        .alert-danger { border-color: var(--red); }
        .alert-info { border-color: var(--accent); }
        .alert-success { border-color: var(--green); }

        /* Goal Cards */
        .goal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }
        .goal-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: transform 0.2s, border-color 0.2s;
            cursor: default;
        }
        .goal-card:hover { transform: translateY(-3px); border-color: rgba(99,179,237,0.25); }
        .goal-icon-big { font-size: 32px; margin-bottom: 12px; }
        .goal-name {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .goal-deadline {
            font-size: 12px;
            color: var(--text3);
            margin-bottom: 14px;
        }
        .goal-amounts {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .goal-saved {
            font-size: 18px;
            font-weight: 700;
            color: var(--green2);
        }
        .goal-target {
            font-size: 14px;
            color: var(--text3);
        }
        .goal-rem {
            font-size: 12px;
            color: var(--text3);
            margin-top: 8px;
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group.full { grid-column: 1 / -1; }
        label {
            font-size: 12px;
            color: var(--text3);
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        input, select, textarea {
            background: var(--bg3);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 14px;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); }

        /* Section Header */
        .section-hd {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .section-hd h3 {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 600;
            color:white;
        }

        /* Badge/Pill */
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .pill-green { background: rgba(16,185,129,0.15); color: var(--green2); }
        .pill-red { background: rgba(239,68,68,0.15); color: var(--red2); }
        .pill-blue { background: rgba(59,130,246,0.15); color: var(--accent2); }
        .pill-gold { background: rgba(245,158,11,0.15); color: var(--gold2); }

        /* Empty State */
        .empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text3);
        }
        .empty-icon { font-size: 40px; margin-bottom: 10px; }
        .empty-text { font-size: 14px; }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 16px 0;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
            .grid-2, .grid-3, .grid-eq { grid-template-columns: 1fr; }
        }
    </style>
    <?php $this->head() ?>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <div class="logo-title">FinFlow</div>
        <div class="logo-sub">Personal Finance</div>
    </div>
    <nav class="nav">
        <div class="nav-section">
            <div class="nav-label">Overview</div>
            <a href="<?= Url::to(['/dashboard/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-label">Finance</div>
            <a href="<?= Url::to(['/income/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'income' ? 'active' : '' ?>">
                <span class="nav-icon">💰</span> Income
            </a>
            <a href="<?= Url::to(['/expense/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'expense' ? 'active' : '' ?>">
                <span class="nav-icon">🧾</span> Expenses
            </a>
            <a href="<?= Url::to(['/budget/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'budget' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span> Budget Planning
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-label">Insights</div>
            <a href="<?= Url::to(['/report/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'report' ? 'active' : '' ?>">
                <span class="nav-icon">📈</span> Reports
            </a>
            <a href="<?= Url::to(['/savings/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'savings' ? 'active' : '' ?>">
                <span class="nav-icon">🏦</span> Savings & Goals
            </a>
            <a href="<?= Url::to(['/alert/index']) ?>" class="nav-item <?= Yii::$app->controller->id == 'alert' ? 'active' : '' ?>">
                <span class="nav-icon">⚠️</span> Alerts
                <span class="nav-badge">3</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-label">Account</div>
            <a href="<?= Url::to(['/site/profile']) ?>" class="nav-item <?= Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'profile' ? 'active' : '' ?>">
                <span class="nav-icon">👤</span> Profile
            </a>
        </div>
    </nav>
    <div class="user-section">
        <div class="user-avatar">A</div>
        <div>
            <div class="user-name">Bwesigye Treasure</div>
            <div class="user-role">Premium User</div>
        </div>
        <?= Html::beginForm(['/site/logout'], 'post', ['style' => 'display: inline;']) ?>
    <?= Html::submitButton('⏻', [
        'class' => 'user-logout', 
        'title' => 'Logout',
        'style' => 'background: none; border: none; cursor: pointer;'
    ]) ?>
<?= Html::endForm() ?>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <div class="page-title"><?= Html::encode($this->title) ?></div>
        <div class="topbar-actions">
            <a href="<?= Url::to(['/alert/index']) ?>" class="notif-btn">
                🔔
                <span class="notif-dot"></span>
            </a>
        </div>
    </div>

    <div class="content">
        <?= $content ?>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <span class="toast-icon" id="toastIcon">✅</span>
    <span id="toastMsg">Saved successfully!</span>
</div>

<script>
function showToast(icon, msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toastIcon').textContent = icon;
    document.getElementById('toastMsg').textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2800);
}
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>