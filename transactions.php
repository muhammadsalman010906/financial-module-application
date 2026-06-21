<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// ================= ADD =================
if(isset($_POST['add'])){

    $account_id = $_POST['account_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("
        INSERT INTO transactions 
        (account_id, transaction_type, amount, description, created_at)
        VALUES (?,?,?,?,NOW())
    ");

    $stmt->bind_param("isds", $account_id, $type, $amount, $description);
    $stmt->execute();
}

// ================= DATA =================
$accounts = $conn->query("SELECT * FROM accounts");

$transactions = $conn->query("
    SELECT t.*, a.account_name
    FROM transactions t
    LEFT JOIN accounts a ON a.id = t.account_id
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

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
            transform:translateX(6px);
        }

        .main{
            margin-left:270px;
            padding:25px;
            animation:fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn{
            from{opacity:0; transform:translateY(10px);}
            to{opacity:1; transform:translateY(0);}
        }

        h1{
            margin-bottom:15px;
        }

        /* QUICK STATS */
        .stats{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:15px;
            margin-bottom:20px;
        }

        .stat{
            background:#111827;
            padding:15px;
            border-radius:12px;
            transition:0.3s;
        }

        .stat:hover{
            transform:translateY(-5px);
            background:#1f2937;
        }

        .stat h3{
            font-size:14px;
            color:#94a3b8;
        }

        .stat h2{
            margin-top:5px;
        }

        /* FORM */
        .form-box{
            background:#111827;
            padding:20px;
            border-radius:15px;
            margin-bottom:20px;
            transition:0.3s;
        }

        .form-box:hover{
            transform:scale(1.01);
        }

        select,input{
            padding:10px;
            margin-right:10px;
            border:none;
            border-radius:8px;
            background:#1f2937;
            color:white;
            width:20%;
            transition:0.3s;
        }

        input:focus,select:focus{
            outline:none;
            transform:scale(1.05);
        }

        button{
            padding:10px 15px;
            border:none;
            border-radius:8px;
            background:#22c55e;
            color:white;
            cursor:pointer;
            transition:0.3s;
        }

        button:hover{
            background:#16a34a;
            transform:scale(1.08);
        }

        /* TABLE */
        table{
            width:100%;
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

        tr{
            transition:0.2s;
        }

        tr:hover{
            background:#1e293b;
        }

        .income{color:#22c55e;font-weight:600;}
        .expense{color:#ef4444;font-weight:600;}

        /* FLOAT ANIMATION BADGE */
        .badge{
            display:inline-block;
            padding:3px 10px;
            border-radius:20px;
            font-size:12px;
        }

        .income-badge{background:rgba(34,197,94,0.2);color:#22c55e;}
        .expense-badge{background:rgba(239,68,68,0.2);color:#ef4444;}

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

    <h1>Transactions 💰</h1>

    <!-- QUICK STATS -->
    <div class="stats">

        <div class="stat">
            <h3>Total Transactions</h3>
            <h2>
                <?php echo $conn->query("SELECT COUNT(*) as c FROM transactions")->fetch_assoc()['c']; ?>
            </h2>
        </div>

        <div class="stat">
            <h3>Total Income</h3>
            <h2>
                <?php echo $conn->query("SELECT COALESCE(SUM(amount),0) as s FROM transactions WHERE transaction_type='income'")->fetch_assoc()['s']; ?>
            </h2>
        </div>

        <div class="stat">
            <h3>Total Expense</h3>
            <h2>
                <?php echo $conn->query("SELECT COALESCE(SUM(amount),0) as s FROM transactions WHERE transaction_type='expense'")->fetch_assoc()['s']; ?>
            </h2>
        </div>

    </div>

    <!-- FORM -->
    <div class="form-box">

        <form method="POST">

            <select name="account_id" required>
                <option value="">Account</option>
                <?php while($a=$accounts->fetch_assoc()){ ?>
                    <option value="<?php echo $a['id']; ?>">
                        <?php echo $a['account_name']; ?>
                    </option>
                <?php } ?>
            </select>

            <select name="type">
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>

            <input type="number" name="amount" placeholder="Amount" required>

            <input type="text" name="description" placeholder="Description">

            <button name="add">+ Add Transaction</button>

        </form>

    </div>

    <!-- TABLE -->
    <table>

        <tr>
            <th>Account</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Date</th>
        </tr>

        <?php while($t=$transactions->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $t['account_name']; ?></td>

            <td>
                <span class="badge <?php echo $t['transaction_type']=='income'?'income-badge':'expense-badge'; ?>">
                    <?php echo $t['transaction_type']; ?>
                </span>
            </td>

            <td><?php echo $t['amount']; ?></td>
            <td><?php echo $t['description']; ?></td>
            <td><?php echo $t['created_at']; ?></td>
        </tr>
        <?php } ?>

    </table>

</div>

</body>
</html>