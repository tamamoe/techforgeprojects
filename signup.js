//PLEASE DO NOT ADD OR DELETE ANYTHING THIS IS ONLY FOR THE SIGNUP PAGE

document.addEventListener('DOMContentLoaded', function() { // want to make sure the DOM is fully loaded irst

    const signupForm = document.getElementById('signup-form');
    const errorBox = document.getElementById('js-signup-error'); 

    if (signupForm) {
        signupForm.addEventListener('submit', function(event) { 
            const email = document.getElementById("signupEmail").value;
            const password = document.getElementById("signupPassword").value;
            const emailConfirm = document.getElementById("signupEmailConfirm").value;
            const passwordConfirm = document.getElementById("signupPasswordConfirm").value;
            
            let isValid = true;
            let errorMessage = ""; 
            
            if (email !== emailConfirm) { 
                errorMessage = "Emails do not match try again.";
                isValid = false;
            } else if (password.length < 8) {
                errorMessage = "Password must be at least 8 characters long.";
                isValid = false;
            } else if (password !== passwordConfirm) { 
                errorMessage = "Passwords do not match try again.";
                isValid = false; 
            }

            if (!isValid) {
                event.preventDefault(); 
                errorBox.innerText = errorMessage; 
                errorBox.style.display = "block"; 
            } else {
                errorBox.style.display = "none"; 
        });
    }
});