// this .js file checks to see if the form is ready to go to submit to the database
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('playerForm');

    form.addEventListener('submit', function (e) {

        let isValid = true;

        // Checks to see if each field is empty
        if (!document.getElementById('first_name').value.trim()) isValid = false;
        if (!document.getElementById('last_name').value.trim()) isValid = false;
        if (!document.getElementById('position').value) isValid = false;
        if (!document.getElementById('phone').value.trim()) isValid = false;
        if (!document.getElementById('email').value.trim()) isValid = false;
        if (!document.getElementById('team_name').value.trim()) isValid = false;

        // Checks to see that captcha is completed (only on add.php)
        if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() === '') isValid = false;

        // If anything is empty stop the form from submitting
        if (!isValid) {
            e.preventDefault();
            alert('Fill in all forms and complete the captcha.');
        }

    });

});