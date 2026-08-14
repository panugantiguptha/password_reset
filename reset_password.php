<?php

require_once "config.php";


/*get token from URL*/

$token = $_GET["token"] ?? "";


/*check token exists*/

if (empty($token)) {

    die("Invalid reset link.");

}


/*check token and expiration*/

$sql = "
    SELECT email
    FROM password_resets
    WHERE token = ?
    AND expires_at > NOW()
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$token]);

$reset = $stmt->fetch();


/*token invalid or expired*/

if (!$reset) {

    die("This reset link is invalid or expired.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

    <div class="container">

        <h2>Create New Password</h2>

        <p>
            Enter your new password below.
        </p>

        <form
            action="update_password.php"
            method="POST"
            id="resetForm"
        >

            <!-- Hidden token -->

            <input
                type="hidden"
                name="token"
                value="<?php echo htmlspecialchars($token); ?>"
            >


            <!-- New Password -->

            <label for="password">
                New Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter new password"
                required
            >


            <!-- Password strength meter -->

            <div class="strength-container">

                <div id="strengthBar"></div>

            </div>

            <p
                id="strengthText"
                class="strength-text"
            >
                Password strength
            </p>


            <!-- Confirm Password -->

            <label for="confirm_password">
                Confirm Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm your password"
                required
            >

            <p id="matchMessage"></p>


            <button type="submit">
                Reset Password
            </button>

        </form>

    </div>


    <!-- jQuery -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js">
    </script>


    <!-- Our JavaScript -->

    <script src="assets/script.js"></script>

</body>

</html>