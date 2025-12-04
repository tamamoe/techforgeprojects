//PLEASE DO NOT ADD OR DELETE ANYTHING THIS IS ONLY FOR THE SIGNUP PAGE

document.addEventListener('DOMContentLoaded', function() { // want to make sure the DOM is fully loaded irst

    const signupForm = document.getElementById('signup-form');

    signupForm.addEventListener('submit', function(event) { //Gets values from user
        const email = document.getElementById("signupEmail").value;
        const password = document.getElementById("signupPassword").value;
        const emailConfirm = document.getElementById("signupEmailConfirm").value;
        const passwordConfirm = document.getElementById("signupPasswordConfirm").value;

            let isValid = true;

        if (email !== emailConfirm) { // checks if emails match
        console.error("Emails do not match, please try again.");
        isValid = false;
        }

        if (password.length < 8) {
         console.error("Password must be at least 8 characters long.");
         isValid = false;
        }

        if (password !== passwordConfirm) { // checks if pass match
        console.error("Passwords do not match, please try again.");
        isValid = false; 

        if (!isValid) {
        event.preventDefault();
        }

        }
    });
});