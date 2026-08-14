<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

    <div class="container">

        <h2>Forgot Password</h2>

        <p>
            Enter your registered email address.
        </p>

        <form action="send_reset.php" method="POST">

            <label for="email">
                Email Address
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <button type="submit">
                Send Reset Link
            </button>

        </form>

    </div>

</body>

</html>