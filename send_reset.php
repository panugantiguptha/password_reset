<?php

require_once "config.php";

/*allow only POST requests*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}


/*get email from form*/
$email = trim($_POST["email"] ?? "");


/*validate email*/
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


/*check whether the email exists*/
$sql = "
    SELECT id
    FROM users
    WHERE email = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch();


/*if email does not exist*/
if (!$user) {
    die("Email address not found.");
}

/*generate a secure random token random_bytes(16) = 16 random bytes bin2hex() = 32 hexadecimal characters*/
$token = bin2hex(random_bytes(16));


/*delete any old reset token  for this email*/
$sql = "
    DELETE FROM password_resets
    WHERE email = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);


/*store the new token DATE_ADD(NOW(), INTERVAL 1 HOUR) makes the token expire after 1 hour.*/
$sql = "
    INSERT INTO password_resets
    (email, token, expires_at)
    VALUES (
        ?,
        ?,
        DATE_ADD(NOW(), INTERVAL 1 HOUR)
    )
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $email,
    $token
]);


/*create password reset link*/
$reset_link =
    "http://localhost/password_reset/reset_password.php?token="
    . urlencode($token);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Link Generated</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

    <div class="container">

        <h2>Reset Link Generated</h2>

        <p>
            Your password reset link is:
        </p>

        <div class="reset-link">

            <a href="<?php echo htmlspecialchars($reset_link); ?>">

                <?php echo htmlspecialchars($reset_link); ?>

            </a>

        </div>

        <p class="expiry">
            This link will expire in 1 hour.
        </p>

        <a
            class="back-link"
            href="request_reset.php"
        >
            Back
        </a>

    </div>

</body>

</html>