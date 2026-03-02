// Get form elements for validation
const fullNameInput = document.getElementById('fullName');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const responseMsg = document.getElementById('responseMsg');

// Error message elements
const nameError = document.querySelector('.nameError');
const emailError = document.querySelector('.emailError');
const passwordError = document.querySelector('.passwordError');

// Validation status
let nameStatus, emailStatus, passwordStatus;

// Handle form submission
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous messages
    responseMsg.className = '';
    responseMsg.textContent = '';
    
    // Clear field-specific errors
    if(nameError) nameError.innerHTML = '';
    if(emailError) emailError.innerHTML = '';
    if(passwordError) passwordError.innerHTML = '';

    // Get form data
    const userData = {
        fullName: fullNameInput.value,
        email: emailInput.value,
        password: passwordInput.value
    };

    // Validations
    // Name validation
    if(!userData.fullName || userData.fullName.trim() === '') {
        if(nameError) nameError.innerHTML = 'Full name is required';
        nameStatus = false;
    } else {
        nameStatus = true;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!userData.email) {
        if(emailError) emailError.innerHTML = 'Email is required';
        emailStatus = false;
    } else if(!emailRegex.test(userData.email)) {
        if(emailError) emailError.innerHTML = 'Please enter a valid email';
        emailStatus = false;
    } else {
        emailStatus = true;
    }

    // Password validation
    if(!userData.password) {
        if(passwordError) passwordError.innerHTML = 'Password is required';
        passwordStatus = false;
    } else if(userData.password.length < 6) {
        if(passwordError) passwordError.innerHTML = 'Password must be at least 6 characters';
        passwordStatus = false;
    } else {
        passwordStatus = true;
    }

    // If all validations pass, use submitForm
    if(nameStatus && emailStatus && passwordStatus) {
        
        // Success callback
        const onSuccess = function(response, form) {
            if(response.status === 'success'){
                responseMsg.className = 'success';
                responseMsg.textContent = response.message;
                
                // Clear form
                form.reset();

                // Redirect to login
                setTimeout(() => {
                    window.location.href = '/rentacar/';
                }, 2000);
            } else {
                responseMsg.className = 'error';
                responseMsg.textContent = response.message;
            }
        };
        
        // Call submitForm with the data
        submitForm('signupForm', '/rentacar/api/signup', 'POST', userData, onSuccess);
    }
});