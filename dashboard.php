<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// ================= DATA =================
$income = $conn->query("
    SELECT COALESCE(SUM(amount),0) AS total 
    FROM transactions 
    WHERE transaction_type='income'
")->fetch_assoc()['total'];

$expense = $conn->query("
    SELECT COALESCE(SUM(amount),0) AS total 
    FROM transactions 
    WHERE transaction_type='expense'
")->fetch_assoc()['total'];

$accounts = $conn->query("SELECT COUNT(*) AS total FROM accounts")->fetch_assoc()['total'];
$net = $income - $expense;

$recent = $conn->query("
    SELECT t.*, a.account_name 
    FROM transactions t
    LEFT JOIN accounts a ON a.id = t.account_id
    ORDER BY t.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>ERP Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}

        body{
            background:#0b1220;
            color:white;
        }

        .sidebar{
            position:fixed;
            width:250px;
            height:100vh;
            background:#0f172a;
            padding:20px;
        }

        .sidebar h2{
            color:#38bdf8;
            margin-bottom:30px;
        }

        .sidebar a{
            display:block;
            padding:12px;
            margin:5px 0;
            color:#cbd5e1;
            text-decoration:none;
            border-radius:10px;
            transition:0.3s;
        }

        .sidebar a:hover{
            background:#1e293b;
            transform:translateX(5px);
            color:white;
        }

        .main{
            margin-left:270px;
            padding:25px;
        }

        .topbar h1{
            font-size:24px;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:15px;
            margin-top:20px;
        }

        .card{
            background:#111827;
            padding:20px;
            border-radius:15px;
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-5px);
            background:#1f2937;
        }

        .card h3{
            color:#94a3b8;
            font-size:14px;
        }

        .card h2{
            font-size:26px;
            margin-top:10px;
        }

        .income{border-left:4px solid #22c55e;}
        .expense{border-left:4px solid #ef4444;}
        .accounts{border-left:4px solid #3b82f6;}
        .net{border-left:4px solid #a855f7;}

        .chart-box{
            margin-top:25px;
            background:#111827;
            padding:15px;
            border-radius:15px;
            display:flex;
            justify-content:center;
        }

        table{
            width:100%;
            margin-top:25px;
            border-collapse:collapse;
            background:#111827;
            border-radius:10px;
            overflow:hidden;
        }

        th,td{
            padding:12px;
            border-bottom:1px solid #1f2937;
        }

        th{
            background:#1f2937;
        }

        .quick{
            margin-top:15px;
        }

        .btn{
            padding:10px 15px;
            border-radius:10px;
            text-decoration:none;
            color:white;
            margin-right:10px;
            display:inline-block;
        }

        .green{background:#22c55e;}
        .blue{background:#3b82f6;}
        .purple{background:#a855f7;}
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>FINANCE ERP</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="accounts.php">Accounts</a>
    <a href="transactions.php">Transactions</a>
    <a href="reports.php">Reports</a>
    <a href="users.php">Manage Users</a>
    <a href="audit_logs.php">Audit Logs</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick">
        <a class="btn green" href="transactions.php">+ Add Transaction</a>
        <a class="btn blue" href="accounts.php">+ Add Account</a>
        <a class="btn purple" href="reports.php">View Reports</a>
    </div>

    <!-- CARDS -->
    <div class="grid">

        <div class="card income">
            <h3>Total Income</h3>
            <h2><?php echo $income; ?></h2>
        </div>

        <div class="card expense">
            <h3>Total Expense</h3>
            <h2><?php echo $expense; ?></h2>
        </div>

        <div class="card accounts">
            <h3>Total Accounts</h3>
            <h2><?php echo $accounts; ?></h2>
        </div>

        <div class="card net">
            <h3>Net Balance</h3>
            <h2><?php echo $net; ?></h2>
        </div>

    </div>

    <!-- CHART -->
    <div class="chart-box">
        <div style="width:320px;">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <!-- RECENT -->
    <h3 style="margin-top:25px;">Recent Transactions</h3>

    <table>
        <tr>
            <th>Account</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>

        <?php while($r=$recent->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $r['account_name']; ?></td>
            <td><?php echo $r['transaction_type']; ?></td>
            <td><?php echo $r['amount']; ?></td>
            <td><?php echo $r['created_at']; ?></td>
        </tr>
        <?php } ?>
    </table>

</div>

<script>
new Chart(document.getElementById('chart'), {
    type: 'doughnut',
    data: {
        labels: ['Income','Expense'],
        datasets: [{
            data: [<?php echo $income; ?>, <?php echo $expense; ?>],
            backgroundColor: ['#22c55e','#ef4444']
        }]
    }
});
</script>

</body>
</html>