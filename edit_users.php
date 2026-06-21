<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("User ID Missing");
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){

    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    mysqli_query($conn, "UPDATE users SET
    username='$username',
    email='$email',
    role='$role'
    WHERE id='$id'");

    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>

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
            padding:30px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            max-width:600px;
        }

        input,
        select{
            width:100%;
            padding:12px;
            margin-top:10px;
            margin-bottom:20px;
            border:1px solid #ccc;
            border-radius:8px;
        }

        button{
            background:#0d6efd;
            color:white;
            border:none;
            padding:12px 20px;
            border-radius:8px;
            cursor:pointer;
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

        <h2>Edit User</h2>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

            <label>Role</label>
            <select name="role">
                <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>User</option>
            </select>

            <button type="submit" name="update">Update User</button>

        </form>

    </div>

</div>

</body>
</html>