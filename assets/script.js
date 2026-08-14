$(document).ready(function () {


    /*password strength checker*/

    $("#password").on("keyup", function () {

        let password = $(this).val();

        let strength = 0;


        /* at least 8 characters */

        if (password.length >= 8) {

            strength++;

        }


        /*contains uppercase letter*/

        if (/[A-Z]/.test(password)) {

            strength++;

        }


        /* contains lowercase letter*/

        if (/[a-z]/.test(password)) {

            strength++;

        }


        /*contains number*/

        if (/[0-9]/.test(password)) {

            strength++;

        }


        /*contains special character*/

        if (/[^A-Za-z0-9]/.test(password)) {

            strength++;

        }


        /* update strength bar*/

        let width = strength * 20;

        $("#strengthBar").css(
            "width",
            width + "%"
        );


        /*update strength text*/

        if (password.length === 0) {

            $("#strengthText")
                .text("Password strength");

        }
        else if (strength <= 2) {

            $("#strengthText")
                .text("Weak password");

        }
        else if (strength <= 4) {

            $("#strengthText")
                .text("Medium password");

        }
        else {

            $("#strengthText")
                .text("Strong password");

        }

    });


    /* password matching check*/

    $("#confirm_password").on("keyup", function () {

        let password =
            $("#password").val();

        let confirmPassword =
            $("#confirm_password").val();


        if (confirmPassword === "") {

            $("#matchMessage")
                .text("");

        }
        else if (password === confirmPassword) {

            $("#matchMessage")
                .text("Passwords match.");

        }
        else {

            $("#matchMessage")
                .text("Passwords do not match.");

        }

    });


    /*check before submitting*/

    $("#resetForm").on("submit", function (event) {

        let password =
            $("#password").val();

        let confirmPassword =
            $("#confirm_password").val();


        /*check matching */

        if (password !== confirmPassword) {

            event.preventDefault();

            alert("Passwords do not match.");

            return;

        }


        /*check minimum length*/

        if (password.length < 8) {

            event.preventDefault();

            alert(
                "Password must contain at least 8 characters."
            );

        }

    });

});