<?php

require_once "config.php";


/*make sure request is POST*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    die("Invalid request.");

}


/*get form values*/

$token = $_POST["token"] ?? "";

$password = $_POST["password"] ?? "";

$confirm_password = $_POST["confirm_password"] ?? "";


/*check token*/

if (empty($token)) {

    die("Invalid reset token.");

}


/*check passwords are not empty*/

if (
    empty($password) ||
    empty($confirm_password)
) {

    die("Password fields are required.");

}


/*check passwords match*/

if ($password !== $confirm_password) {

    die("Passwords do not match.");

}


/*check minimum password length*/

if (strlen($password) < 8) {

    die("Password must contain at least 8 characters.");

}


/*validate token again*/

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


$email = $reset["email"];


/*hash password using BCRYPT*/

$hashed_password = password_hash(
    $password,
    PASSWORD_BCRYPT
);


/*update user password*/

$sql = "
    UPDATE users
    SET password = ?
    WHERE email = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $hashed_password,
    $email
]);


/*delete token after successful reset*/

$sql = "
    DELETE FROM password_resets
    WHERE token = ?
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$token]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Password Reset Successful</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

    <div class="container success">

        <h2>Password Reset Successful</h2>

        <p>
            Your password has been changed successfully.
        </p>

        <a
            class="back-link"
            href="request_reset.php"
        >
            Back to Forgot Password
        </a>

    </div>

</body>

</html>