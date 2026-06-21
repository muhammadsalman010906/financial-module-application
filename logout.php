<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body{
            margin:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#0b1220;
            color:white;
            font-family:Poppins;
        }

        .box{
            text-align:center;
            animation:fade 1s ease-in-out;
        }

        @keyframes fade{
            from{opacity:0; transform:scale(0.9);}
            to{opacity:1; transform:scale(1);}
        }

        .loader{
            margin:20px auto;
            width:40px;
            height:40px;
            border:4px solid #1e293b;
            border-top:4px solid #38bdf8;
            border-radius:50%;
            animation:spin 1s linear infinite;
        }

        @keyframes spin{
            to{transform:rotate(360deg);}
        }
    </style>
</head>

<body>

<div class="box">
    <h2>Logging you out...</h2>
    <div class="loader"></div>
</div>

<script>
setTimeout(()=>{
    window.location.href="login.php";
},1500);
</script>

</body>
</html>