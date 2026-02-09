<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, sans-serif;
        }

        body{
            height:100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display:flex;
            flex-direction:column;
        }

        /* Navbar */
        .navbar{
            background: rgba(0,0,0,0.2);
            padding:15px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            color:white;
        }

        .navbar h3{
            font-weight:normal;
        }

        .logout-btn{
            text-decoration:none;
            background:white;
            color:#764ba2;
            padding:8px 15px;
            border-radius:20px;
            font-weight:bold;
            transition:0.3s;
        }

        .logout-btn:hover{
            background:#ff4d4d;
            color:white;
        }

        /* Center Card */
        .container{
            flex:1;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .card{
            background:white;
            padding:40px 60px;
            border-radius:15px;
            text-align:center;
            box-shadow:0 10px 25px rgba(0,0,0,0.2);
        }

        .card h2{
            color:#333;
            margin-bottom:10px;
        }

        .card p{
            color:#666;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <h3>My Dashboard</h3>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<!-- CENTER CONTENT -->
<div class="container">
    <div class="card">
        <h2>Welcome <?php echo $_SESSION['user']; ?> 🎉</h2>
        <p>You are successfully logged in.</p>
    </div>
</div>

</body>
</html>
