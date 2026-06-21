<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// ================= FILTER =================
$filter = $_GET['filter'] ?? 'all';

$where = "";

if($filter == "income"){
    $where = "WHERE transaction_type='income'";
}
elseif($filter == "expense"){
    $where = "WHERE transaction_type='expense'";
}

// ================= DATA =================
$totalIncome = $conn->query("
    SELECT COALESCE(SUM(amount),0) as total 
    FROM transactions 
    WHERE transaction_type='income'
")->fetch_assoc()['total'];

$totalExpense = $conn->query("
    SELECT COALESCE(SUM(amount),0) as total 
    FROM transactions 
    WHERE transaction_type='expense'
")->fetch_assoc()['total'];

$totalTransactions = $conn->query("
    SELECT COUNT(*) as total FROM transactions
")->fetch_assoc()['total'];

$net = $totalIncome - $totalExpense;

$records = $conn->query("
    SELECT t.*, a.account_name 
    FROM transactions t
    LEFT JOIN accounts a ON a.id = t.account_id
    $where
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Poppins;
        }

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
        }

        .main{
            margin-left:270px;
            padding:25px;
            animation:fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn{
            from{opacity:0; transform:translateY(10px);}
            to{opacity:1; transform:translateY(0);}
        }

        h1{
            margin-bottom:15px;
        }

        /* FILTER BUTTONS */
        .filters{
            margin-bottom:20px;
        }

        .btn{
            padding:8px 12px;
            border-radius:8px;
            text-decoration:none;
            color:white;
            margin-right:10px;
            transition:0.3s;
            display:inline-block;
        }

        .all{background:#3b82f6;}
        .income{background:#22c55e;}
        .expense{background:#ef4444;}

        .btn:hover{
            transform:scale(1.08);
            opacity:0.9;
        }

        /* CARDS */
        .grid{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:15px;
            margin-top:15px;
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
            font-size:13px;
            color:#94a3b8;
        }

        .card h2{
            margin-top:10px;
            font-size:24px;
        }

        .income-b{border-left:4px solid #22c55e;}
        .expense-b{border-left:4px solid #ef4444;}
        .net-b{border-left:4px solid #a855f7;}
        .total-b{border-left:4px solid #3b82f6;}

        /* CHART */
        .chart-box{
            margin-top:20px;
            background:#111827;
            padding:15px;
            border-radius:15px;
            display:flex;
            justify-content:center;
        }

        /* TABLE */
        table{
            width:100%;
            margin-top:20px;
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

        tr:hover{
            background:#1e293b;
        }

    </style>
</head>

<body>

<div class="sidebar">
    <h2>FINANCE ERP</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="accounts.php">Accounts</a>
    <a href="transactions.php">Transactions</a>
    <a href="reports.php">Reports</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <h1>Financial Reports 📊</h1>

    <!-- FILTERS -->
    <div class="filters">
        <a class="btn all" href="reports.php">All</a>
        <a class="btn income" href="reports.php?filter=income">Income</a>
        <a class="btn expense" href="reports.php?filter=expense">Expense</a>
    </div>

    <!-- STATS -->
    <div class="grid">

        <div class="card total-b">
            <h3>Total Transactions</h3>
            <h2><?php echo $totalTransactions; ?></h2>
        </div>

        <div class="card income-b">
            <h3>Total Income</h3>
            <h2><?php echo $totalIncome; ?></h2>
        </div>

        <div class="card expense-b">
            <h3>Total Expense</h3>
            <h2><?php echo $totalExpense; ?></h2>
        </div>

        <div class="card net-b">
            <h3>Net Balance</h3>
            <h2><?php echo $net; ?></h2>
        </div>

    </div>

    <!-- CHART -->
    <div class="chart-box">
        <div style="width:320px;">
            <canvas id="reportChart"></canvas>
        </div>
    </div>

    <!-- TABLE -->
    <table>

        <tr>
            <th>Account</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>

        <?php while($r=$records->fetch_assoc()){ ?>
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
new Chart(document.getElementById('reportChart'), {
    type: 'bar',
    data: {
        labels: ['Income','Expense','Net'],
        datasets: [{
            label: 'Financial Overview',
            data: [
                <?php echo $totalIncome; ?>,
                <?php echo $totalExpense; ?>,
                <?php echo $net; ?>
            ],
            backgroundColor: ['#22c55e','#ef4444','#a855f7']
        }]
    }
});
</script>

</body>
</html>