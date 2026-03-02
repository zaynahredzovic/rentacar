<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/rentacar/public/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">
    <title>Rent a car - login</title>
</head>
<body>
    
    <div class="container">
        <h2>Login</h2>

        <form id="loginForm">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit" id="loginBtn">Login</button>
        </form>

        <p id="responseMsg"></p>
        <p>Already have an account? <a href="/rentacar/signup">Sign Up</a></p>
    </div>

    <script src="/rentacar/public/js/jquery.min.js"></script>
    <script src="/rentacar/public/js/form.js?v=<?php echo time(); ?>"></script>
    <script src="/rentacar/public/js/login.js?v=<?php echo time(); ?>"></script>
</body>
</html>