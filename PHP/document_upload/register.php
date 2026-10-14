<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Sign Up</h2>
    <form action="register_user.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="login" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>

        <label>Full Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br>

        <label>Phone:</label><br>
        <input type="text" name="phone"><br>

        <label>Company:</label><br>
        <input type="text" name="company"><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
