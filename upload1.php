<?php

// Retrieve the cookie value
if (!isset($_COOKIE["fileSelected"])) {
    header("Location: index.php");
    exit();
}
// Check if a file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile']) && ($_FILES['csvFile']['type'] == 'text/csv')) {
    $file = $_FILES['csvFile'];
    $filePath = 'uploads/' . basename($file['name']);
    // Move the uploaded file to the "uploads" directory
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Read the headers from the CSV file
        if (($handle = fopen($filePath, 'r'))) {
            $headers = fgetcsv($handle, 1000, ',');
            $firstRow = fgetcsv($handle);
            fclose($handle);
        } else {
            die("Error reading the CSV file.");
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveHeaders'])) {
    $uploadPath = $_POST['filePath'];
    $newHeaders = $_POST['headers'];
    // Open the original CSV file for reading from the temporary location
    if (($handle = fopen($uploadPath, 'r')) !== FALSE) {

        // Read the rest of the data (excluding original headers)
        $data = [];
        fgetcsv($handle, 1000, ','); // Skip the original headers
        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);

        // Create a temporary file in memory
        $tempFile = fopen('php://memory', 'w');

        // Write the new headers to the temporary file
        fputcsv($tempFile, $newHeaders);

        // Write the rest of the data to the temporary file
        foreach ($data as $row) {
            fputcsv($tempFile, $row);
        }

        // Set the file pointer to the beginning of the file
        rewind($tempFile);

        // Set HTTP headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="updated_headers.csv"');

        // Output the file content to the browser
        fpassthru($tempFile);
        // Close the temporary file
        fclose($tempFile);
        // Remove the temporary uploaded file
        unlink($uploadPath);
        exit;

    } else {
        die("Error opening the original CSV file.");
    }

} else {
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>

<body>
    <div class="upload-section">
        <?php if (isset($headers)): ?>
            <form action="" method="post">
                <div class="header-mapping-container">

                    <a href="./index.php">Select Next File</a>

                    <div class="header">
                        <div class="left-box">
                            <p class="header-info col-name">Column Name</p>
                        </div>
                        <div class="right-box">
                            <p class="header-info">Map to field</p>
                        </div>
                    </div>

                    <?php foreach ($headers as $index => $header): ?>
                        <div class="contents">
                            <div class="left">
                                <?php echo htmlspecialchars($header); ?>
                                <!-- <input class="header-input" value="<?php echo htmlspecialchars($header); ?>" readonly> -->
                                <p class="sample">Sample: <?php echo htmlspecialchars($firstRow[$index]) ?></p>
                            </div>

                            <div class="right">
                                <select name="headers[]">
                                    <option value="<?php echo htmlspecialchars($header); ?>">
                                        <?php echo htmlspecialchars($header); ?>
                                    </option>
                                    <option value="Item Name">Item Name</option>
                                    <option value="Selling Price">Selling Price</option>
                                    <option value="Cost of Goods">Cost of Goods</option>
                                    <option value="Tax">Tax</option>
                                    <option value="Payment">Payment</option>
                                    <option value="Processing Fee">Processing Fee</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" name="filePath" value="<?php echo htmlspecialchars($filePath); ?>">
                    <button type="submit" name="saveHeaders" class="saveHeaders" id="download">Download File</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <script>
        let download = document.getElementById("download")
        download.onclick = (e) => {
            setTimeout(() => {
                window.location.href = "./index.php"
            }, 2000);
        }
    </script>
</body>

</html>