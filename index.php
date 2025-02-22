<?php
setcookie("fileSelected", true, time() + 3600, "/");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Header Editor</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <h1>Choose file to map header</h1>
            <!-- Form to upload CSV file -->
            <form action="./upload1.php" method="post" enctype="multipart/form-data" id="file">
                <input type="file" name="csvFile" accept=".csv" required>
                <br>
                <button type="submit">Upload File</button>
            </form>
        </div>
    </div>
</body>

</html>