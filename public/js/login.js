const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const responseMsg = document.getElementById('responseMsg');
const loginBtn = document.getElementById('loginBtn');

loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const originalText = loginBtn.textContent;

    //login state
    loginBtn.textContent = "Logging in...";
    loginBtn.disabled = true;

    //clear msgs 
    responseMsg.className = '';
    responseMsg.textContent = '';

    //Get form data
    const formData = {
        email: emailInput.value,
        password: passwordInput.value,
    }

    //basic validation
    if(!formData.email || !formData.password){
        responseMsg.className = 'error';
        responseMsg.textContent = 'Please fill in all fields'
        loginBtn.textContent = originalText;
        loginBtn.disabled = false;
        return;
    }

    //custom success callback
    const onSuccess = function(response, form) {
        if(response.status === 'success'){
            responseMsg.className = 'success';
            responseMsg.textContent = response.message;

            //clear form
            form.reset();

            //redirect to dashboard
            setTimeout(() => {
                window.location.href = 'rentacar/dashboard';
            }, 1000);
        }else{
            responseMsg.className = 'error';
            responseMsg.textContent = response.message;
        }
    }

    submitForm('loginForm', '/rentacar/api/login', 'POST', formData, onSuccess);
})