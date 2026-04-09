// validate.js
// This file checks to see if the form is ready to submit to the database

document.addEventListener('DOMContentLoaded', function () {

    // Gets the form element from the page using its ID
    const form = document.getElementById('playerForm') || document.getElementById('registerForm');

    if (!form) return;

    // Listens for when the user clicks the submit button
    form.addEventListener('submit', function (e) {

        let isValid = true;

        function checkField(id) {
            const el = document.getElementById(id);
            if (el && !el.value.trim()) isValid = false;
        }

        // Checks to see if each field is empty
        checkField('first_name');
        checkField('last_name');
        checkField('position');
        checkField('phone');
        checkField('email');
        checkField('team_name');
        checkField('username');
        checkField('password');
        checkField('confirm_password');

        // Checks to see that captcha is completed (only on pages that have it)
        if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() === '') isValid = false;

        // If anything is empty stop the form from submitting and alert the user
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }

    });

});
