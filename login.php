<?php
session_start();
include 'db.php';

$error = "";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($user = $result->fetch_assoc()){

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ERP Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: linear-gradient(-45deg,#0f172a,#1e293b,#0ea5e9,#1e3a8a);
            background-size:400% 400%;
            animation: gradientMove 10s ease infinite;
            overflow:hidden;
        }

        @keyframes gradientMove{
            0%{background-position:0% 50%;}
            50%{background-position:100% 50%;}
            100%{background-position:0% 50%;}
        }

        .login-box{
            width:380px;
            padding:40px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(15px);
            border-radius:20px;
            box-shadow:0 0 40px rgba(0,0,0,0.3);
            color:white;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float{
            0%{transform:translateY(0px);}
            50%{transform:translateY(-10px);}
            100%{transform:translateY(0px);}
        }

        .welcome{
            text-align:center;
            margin-bottom:20px;
        }

        .welcome h1{
            font-size:22px;
            font-weight:600;
        }

        .welcome p{
            font-size:13px;
            opacity:0.8;
        }

        input{
            width:100%;
            padding:12px;
            margin:10px 0;
            border:none;
            border-radius:10px;
            outline:none;
            background:rgba(255,255,255,0.15);
            color:white;
            transition:0.3s;
        }

        input:focus{
            background:rgba(255,255,255,0.25);
            transform:scale(1.02);
        }

        button{
            width:100%;
            padding:12px;
            margin-top:10px;
            border:none;
            border-radius:10px;
            background:#0ea5e9;
            color:white;
            font-weight:600;
            cursor:pointer;
            transition:0.3s;
        }

        button:hover{
            background:#0284c7;
            transform:scale(1.05);
        }

        .error{
            background:rgba(255,0,0,0.2);
            padding:8px;
            border-radius:8px;
            font-size:12px;
            margin-bottom:10px;
            text-align:center;
        }

        .glow-circle{
            position:absolute;
            width:300px;
            height:300px;
            background:#0ea5e9;
            filter:blur(120px);
            border-radius:50%;
            top:10%;
            left:10%;
            opacity:0.3;
        }

        .glow-circle2{
            position:absolute;
            width:300px;
            height:300px;
            background:#8b5cf6;
            filter:blur(120px);
            border-radius:50%;
            bottom:10%;
            right:10%;
            opacity:0.3;
        }

    </style>
</head>

<body>

<div class="glow-circle"></div>
<div class="glow-circle2"></div>

<div class="login-box">

    <div class="welcome">
        <h1>Welcome Back 👋</h1>
        <p>Finance System Login</p>
    </div>

    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username or Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button name="login">Login</button>

    </form>

</div>

</body>
</html>