<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .icon { font-size: 70px; }
        h2 { color: #28a745; margin: 20px 0 10px; }
        p  { color: #666; }
        a  {
            display: inline-block;
            margin-top: 25px;
            background: #6c63ff;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">🎉</div>
        <h2>Payment Successful!</h2>
        <p>Your <strong>Pro Plan</strong> subscription is now active.</p>
        <p style="margin-top:10px; color:#aaa; font-size:13px;">
            You'll receive a confirmation email shortly.
        </p>
        <a href="dashboard.php">Go to Dashboard →</a>
    </div>
</body>
</html>