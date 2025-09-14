<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Video Player</title>
    <style>
        /* ---- Center everything using Flexbox ---- */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #edf2fb 0%, #e0e7ef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* ---- Main container styling ---- */
        .container {
            background: #fff;
            padding: 38px 38px 32px 38px;
            max-width: 750px;
            min-width: 320px;
            width: 100%;
            border-radius: 17px;
            box-shadow: 0 6px 38px 0 rgba(30,60,120,0.14), 0 1.5px 1.5px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        h2, h3 {
            text-align: center;
            color: #24456c;
            margin-top: 0;
            margin-bottom: 20px;
        }
        /* ---- Form and inputs ---- */
        form {
            margin-top: 18px;
            margin-bottom: 12px;
            text-align: center;
            width: 100%;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 11px;
            font-size: 1.07rem;
            color: #374765;
        }
        select {
            width: 80%;
            padding: 13px 45px 13px 13px;
            font-size: 17px;
            margin-bottom: 21px;
            border-radius: 7px;
            border: 1.5px solid #b4c2d6;
            background: #f6faff url("data:image/svg+xml,%3csvg fill='gray' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e") no-repeat right 18px center;
            background-size: 1.3em;
            appearance: none;
            color: #274765;
            text-align: center;
            transition: border-color 0.18s;
            box-shadow: 0 1px 3px 0 rgba(36,69,108,0.04);
        }
        select:focus {
            border-color: #5ca6fa;
            outline: none;
            background-color: #e8f0fe;
        }
        button {
            padding: 13px 38px;
            font-size: 17px;
            color: white;
            background-image: linear-gradient(90deg,#2095fa,#246ad1 60%);
            border: none;
            border-radius: 9px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(33,90,168,0.07);
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            margin-top: 4px;
            transition: background 0.22s;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        button:hover, button:focus {
            background-image: linear-gradient(90deg,#177ee5 0,#1a60af 70%);
            outline: none;
        }
        /* ---- Video output ---- */
        video {
            margin-top: 28px;
            width: 100%;
            max-width: 640px;
            border-radius: 14px;
            box-shadow: 0 2px 14px rgba(90,110,150,.16);
            max-height: 480px;
            background: #131c24;
            display: block;
            margin-left: auto;
            margin-right: auto;
            animation: fadeIn 0.45s cubic-bezier(.7,-0.05,.38,.98) 1;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(38px);}
            100% { opacity: 1; transform: translateY(0);}
        }
        .info {
            margin-top: 16px;
            font-size: 15px;
            color: #536d88;
            text-align: center;
        }
        p.error {
            color: #de4444;
            font-weight: 600;
            text-align: center;
            margin-top: 18px;
            letter-spacing: 0.01em;
        }
        /* ---- Responsive tweaks ---- */
        @media (max-width: 700px) {
            .container { max-width: 98vw; padding: 16px 3vw 22px 3vw; }
            video { max-width: 98vw; }
            select { width: 98%; }
        }
        @media (max-width: 440px) {
            h2, h3 { font-size: 1.06em; }
            select, button { font-size: 15px; }
            .container { min-width: 95vw; }
        }
    </style>
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
            // ---- Scan converted folder for videos ----
            foreach (scandir("converted") as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExt)) {
                    $safeName = htmlspecialchars($file); // Prevent XSS by escaping filename
                    $isSelected = ($file === $selectedName) ? 'selected' : '';
                    echo "<option value=\"$safeName\" $isSelected>$safeName</option>";
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
        $filePath = 'converted/' . $selectedName;
        // ---- Security check: file must exist and be inside "converted" ----
        if (file_exists($filePath) && strpos(realpath($filePath), realpath('converted')) === 0) {
            // ---- Detect video MIME type ----
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            // ---- Permit only safe MIME types ----
            $allowedTypes = ['video/mp4', 'video/x-matroska', 'video/webm', 'video/x-msvideo', 'application/octet-stream'];
            if (!in_array($mimeType, $allowedTypes)) {
                // ---- Invalid video type error ----
                echo '<p class="error">Error: Unsupported video type.</p>';
            } else {
                // ---- Display video details and player ----
                $sizeMB = round(filesize($filePath) / (1024 * 1024), 2);
                // $lastModified = date("F d, Y H:i", filemtime($filePath));
                echo "<h3>Playing: " . htmlspecialchars($selectedName) . "</h3>";
                echo "<video controls autoplay preload=\"metadata\" tabindex=\"0\">
                        <source src=\"" . htmlspecialchars($filePath) . "\" type=\"" . htmlspecialchars($mimeType) . "\">
                        Your browser does not support the video tag.
                      </video>";
                echo "<div class='info'>Size: {$sizeMB} MB</div>";
            }
        } else {
            // ---- Video not found error ----
            echo '<p class="error">Video not found: ' . htmlspecialchars($selectedName) . '</p>';
        }
    }
    ?>
    </div>
</div>
</body>
</html>
