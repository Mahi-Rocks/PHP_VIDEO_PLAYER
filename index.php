<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Video Player</title>
    <!-- Link external stylesheet -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Video Player</h2>
    <!-- ---- File selection form ---- -->
    <form action="" method="POST" novalidate>
        <label for="videoSelect">Choose a Video:</label>
        <select name="name" id="videoSelect" required aria-required="true" aria-label="Select video to play">
            <!-- ---- Default option ---- -->
            <option value="" disabled <?php echo empty($_POST['name']) ? 'selected' : ''; ?>>-- Select Video --</option>
            <?php
            // ---- Allow specific video formats only ----
            $allowedExt = ['mp4', 'mkv', 'webm', 'avi']; // Allowed extensions
            $selectedName = $_POST['name'] ?? '';
            // ---- Scan videos folder for videos ----
            foreach (scandir("videos") as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExt)) {
                    $safeName = htmlspecialchars($file); // Prevent XSS
                    $isSelected = ($file === $selectedName) ? 'selected' : '';
                    echo "<option value="$safeName" $isSelected>$safeName</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Play Video</button>
    </form>
    <div aria-live="polite">
    <?php
    // ---- Handle video selection and playback ----
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["name"])) {
        // ---- Get safe filename ----
        $selectedName = basename($_POST["name"]);
        $filePath = 'videos/' . $selectedName;

        // ---- Security check ----
        if (file_exists($filePath) && strpos(realpath($filePath), realpath('videos')) === 0) {
            // ---- Detect video MIME type ----
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            // ---- Permit only safe MIME types ----
            $allowedTypes = ['video/mp4', 'video/x-matroska', 'video/webm', 'video/x-msvideo', 'application/octet-stream'];
            if (!in_array($mimeType, $allowedTypes)) {
                echo '<p class="error">Error: Unsupported video type.</p>';
            } else {
                $sizeMB = round(filesize($filePath) / (1024 * 1024), 2);
                echo "<h3>Playing: " . htmlspecialchars($selectedName) . "</h3>";
                echo "<video controls autoplay preload=\"metadata\" tabindex=\"0\">
                        <source src=\"" . htmlspecialchars($filePath) . "\" type=\"" . htmlspecialchars($mimeType) . "\">
                        Your browser does not support the video tag.
                      </video>";
                echo "<div class='info'>Size: {$sizeMB} MB</div>";
            }
        } else {
            echo '<p class="error">Video not found: ' . htmlspecialchars($selectedName) . '</p>';
        }
    }
    ?>
    </div>
</div>
</body>
</html>