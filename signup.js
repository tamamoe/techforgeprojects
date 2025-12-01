//PLEASE DO NOT ADD OR DELETE ANYTHING THIS IS ONLY FOR THE SIGNUP PAGE

document.addEventListener('DOMContentLoaded', function() { // want to make sure the DOM is fully loaded irst

    const signupForm = document.getElementById('signup-form');

    signupForm.addEventListener('submit', function(event) { //Gets values from user
        const email = document.getElementById("signupEmail").value;
        const password = document.getElementById("signupPassword").value;
        const emailConfirm = document.getElementById("signupEmailConfirm").value;
        const passwordConfirm = document.getElementById("signupPasswordConfirm").value;

        if (email !== emailConfirm) { // checks if emails match
            alert("Emails do not match, please try again.");
            event.preventDefault();
        }

        if (password !== passwordConfirm) { // checks if pass match
            alert("Passwords do not matchm, please try again.");
            event.preventDefault();
        }
    });
});