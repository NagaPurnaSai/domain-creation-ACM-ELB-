<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Management</title>
</head>
<body>
    <h1>Server Management</h1>
    <form action="folder_deploy.php" method="post" enctype="multipart/form-data">
        <label for="domain">Domain:</label><br>
        <input type="text" id="domain" name="domain" required><br>
        <label for="ip">IP Address:</label><br>
        <input type="text" id="ip" name="ip" required><br>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="pem">PEM Key:</label><br>
        <input type="file" id="pem" name="pem" required><br><br>
        <input type="submit" value="Deploy">
    </form>
</body>
</html>
