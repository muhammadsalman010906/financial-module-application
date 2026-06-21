<?php
session_start();
include 'db.php';

$message = "";
$error = "";

if(isset($_POST['register'])){

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password != $confirm_password){
        $error = "Passwords do not match!";
    } else {

        $check = $conn->prepare("SELECT * FROM users WHERE email=? OR username=?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            $error = "Username or Email already exists!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users(username,email,phone,password) VALUES(?,?,?,?)");
            $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);

            if($stmt->execute()){
                $message = "Registration Successful!";
            } else {
                $error = "Registration Failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Finance System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins', sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#0f172a,#1e293b,#312e81);
        }

        .register-box{
            width:420px;
            background:#243247;
            padding:40px;
            border-radius:25px;
            box-shadow:0 20px 40px rgba(0,0,0,0.4);
        }

        .register-box h1{
            color:white;
            text-align:center;
            font-size:38px;
            font-weight:700;
            margin-bottom:10px;
        }

        .register-box p{
            text-align:center;
            color:#cbd5e1;
            margin-bottom:30px;
        }

        .form-control{
            background:#4b5563;
            border:none;
            height:50px;
            border-radius:12px;
            color:white;
            margin-bottom:18px;
        }

        .form-control::placeholder{
            color:#d1d5db;
        }

        .form-control:focus{
            background:#4b5563;
            color:white;
            box-shadow:none;
            border:1px solid #0ea5e9;
        }

        .btn-register{
            width:100%;
            height:50px;
            border:none;
            border-radius:12px;
            background:#0ea5e9;
            color:white;
            font-weight:600;
            font-size:18px;
            transition:0.3s;
        }

        .btn-register:hover{
            background:#0284c7;
        }

        .bottom-text{
            text-align:center;
            margin-top:20px;
            color:white;
        }

        .bottom-text a{
            color:#38bdf8;
            text-decoration:none;
            font-weight:600;
        }

        .alert{
            border-radius:10px;
            text-align:center;
        }
    </style>
</head>
<body>

<div class="register-box">

    <h1>Create Account</h1>
    <p>Finance System Registration</p>

    <?php if($message != ""){ ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php } ?>

    <?php if($error != ""){ ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">

        <input type="text" name="username" class="form-control" placeholder="Username" required>

        <input type="email" name="email" class="form-control" placeholder="Email Address" required>

        <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>

        <input type="password" name="password" class="form-control" placeholder="Password" required>

        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>

        <button type="submit" name="register" class="btn-register">
            Register
        </button>

    </form>

    <div class="bottom-text">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>
```

# Important Database Update

Add the `phone` column inside your `users` table if it does not already exist.

```sql
ALTER TABLE users
ADD phone VARCHAR(20);
```
