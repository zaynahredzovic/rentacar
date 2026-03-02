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
            <input type="text" id="fullName" name="fullName" placeholder="Full Name" required>
            <div class="nameError error-message"></div>
            
            <input type="email" id="email" name="email" placeholder="Email" required>
            <div class="emailError error-message"></div>
            
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div class="passwordError error-message"></div>
            
            <button type="submit">Sign Up</button>
        </form>

        <p id="responseMsg"></p>
        <p>Already have an account? <a href="/rentacar/">Log In</a></p>
    </div>

    <!-- Move scripts to the bottom, just before closing body tag -->
    <script src="/rentacar/public/js/jquery.min.js"></script>
    <script src="/rentacar/public/js/form.js?v=<?php echo time(); ?>"></script>
    <script src="/rentacar/public/js/signup.js?v=<?php echo time(); ?>"></script>
</body>
</html>