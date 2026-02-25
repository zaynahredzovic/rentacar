<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/rentacar/public/css/style.css">
    <title>Rent a car - Signup</title>
</head>
<body>
    
    <div class="container">
        <h2>Create Account</h2>

        <form id="signupForm">
            <input type="text" name="fullName" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit"> Sign Up</button>
        </form>

        <p id="responseMsg"></p>
        <p>Already have an account? <a href="/rentacar/">Log In</a></p>

    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/signup.js"></script>
</body>
</html>