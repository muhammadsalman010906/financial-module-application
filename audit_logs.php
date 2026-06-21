<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$logs = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            display:flex;
            background:#f4f6f9;
        }

        .sidebar{
            width:250px;
            height:100vh;
            background:#1e293b;
            padding:20px;
            position:fixed;
        }

        .sidebar h2{
            color:white;
            margin-bottom:30px;
            text-align:center;
        }

        .sidebar a{
            display:block;
            color:white;
            text-decoration:none;
            padding:12px;
            margin-bottom:10px;
            border-radius:8px;
            transition:0.3s;
        }

        .sidebar a:hover{
            background:#334155;
        }

        .main{
            margin-left:270px;
            padding:30px;
            width:100%;
        }

        .card{
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table th,
        table td{
            border:1px solid #ddd;
            padding:12px;
            text-align:left;
        }

        table th{
            background:#1e293b;
            color:white;
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
    <a href="users.php">Manage Users</a>
    <a href="audit_logs.php">Audit Logs</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <div class="card">

        <h2>Audit Logs</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Module</th>
                <th>Date</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($logs)) { ?>

            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['user']; ?></td>
                <td><?php echo $row['action']; ?></td>
                <td><?php echo $row['module']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>
