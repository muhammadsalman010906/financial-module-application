<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// ================= ADD ACCOUNT =================
if(isset($_POST['add'])){

    $name = $_POST['name'];
    $type = $_POST['type'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("
        INSERT INTO accounts (account_name, account_type, email, phone)
        VALUES (?,?,?,?)
    ");

    $stmt->bind_param("ssss", $name, $type, $email, $phone);
    $stmt->execute();
}

// ================= DELETE =================
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM accounts WHERE id=$id");
}

// ================= DATA =================
$totalAccounts = $conn->query("SELECT COUNT(*) as c FROM accounts")->fetch_assoc()['c'];

$clients = $conn->query("SELECT COUNT(*) as c FROM accounts WHERE account_type='client'")->fetch_assoc()['c'];

$vendors = $conn->query("SELECT COUNT(*) as c FROM accounts WHERE account_type='vendor'")->fetch_assoc()['c'];

$accounts = $conn->query("SELECT * FROM accounts ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accounts</title>

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
            transform:translateX(5px);
        }

        .main{
            margin-left:270px;
            padding:25px;
            animation:fade 0.5s ease-in;
        }

        @keyframes fade{
            from{opacity:0; transform:translateY(10px);}
            to{opacity:1; transform:translateY(0);}
        }

        h1{
            margin-bottom:15px;
        }

        /* STATS */
        .grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:15px;
            margin-bottom:20px;
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
            font-size:24px;
            margin-top:10px;
        }

        .blue{border-left:4px solid #3b82f6;}
        .green{border-left:4px solid #22c55e;}
        .purple{border-left:4px solid #a855f7;}

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

        input,select{
            width:23%;
            padding:10px;
            margin-right:10px;
            border:none;
            border-radius:8px;
            background:#1f2937;
            color:white;
            transition:0.3s;
        }

        input:focus,select:focus{
            transform:scale(1.05);
            outline:none;
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

        tr:hover{
            background:#1e293b;
        }

        .delete{
            background:#ef4444;
            padding:6px 10px;
            border-radius:6px;
            color:white;
            text-decoration:none;
        }

        .delete:hover{
            opacity:0.8;
        }

        .badge{
            padding:4px 10px;
            border-radius:20px;
            font-size:12px;
        }

        .client{background:rgba(59,130,246,0.2);color:#3b82f6;}
        .vendor{background:rgba(34,197,94,0.2);color:#22c55e;}

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

    <h1>Accounts Management 🏢</h1>

    <!-- STATS -->
    <div class="grid">

        <div class="card blue">
            <h3>Total Accounts</h3>
            <h2><?php echo $totalAccounts; ?></h2>
        </div>

        <div class="card green">
            <h3>Clients</h3>
            <h2><?php echo $clients; ?></h2>
        </div>

        <div class="card purple">
            <h3>Vendors</h3>
            <h2><?php echo $vendors; ?></h2>
        </div>

    </div>

    <!-- FORM -->
    <div class="form-box">

        <form method="POST">

            <input type="text" name="name" placeholder="Account Name" required>

            <select name="type">
                <option value="client">Client</option>
                <option value="vendor">Vendor</option>
            </select>

            <input type="email" name="email" placeholder="Email">

            <input type="text" name="phone" placeholder="Phone">

            <button name="add">+ Add Account</button>

        </form>

    </div>

    <!-- TABLE -->
    <table>

        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>

        <?php while($a=$accounts->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $a['account_name']; ?></td>

            <td>
                <span class="badge <?php echo $a['account_type']; ?>">
                    <?php echo $a['account_type']; ?>
                </span>
            </td>

            <td><?php echo $a['email']; ?></td>
            <td><?php echo $a['phone']; ?></td>

            <td>
                <a class="delete" href="accounts.php?delete=<?php echo $a['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>

    </table>

</div>

</body>
</html>