# 🎬 Simple PHP Video Player

This is a single-file, self-contained PHP application that allows you to
play video files from a designated directory on your web server.\
It features a simple, clean, and responsive user interface for easy
video selection and playback.

------------------------------------------------------------------------

## ✨ Features

-   📂 **Video File Listing**: Automatically scans and lists all
    supported video files (`.mp4`, `.mkv`, `.webm`, `.avi`) from the
    `converted` directory.
-   🔒 **Secure File Handling**: Uses PHP's `basename` and `realpath`
    functions to prevent directory traversal attacks and ensures that
    only files within the `converted` folder can be accessed.
-   ✅ **MIME Type Validation**: Verifies the file's MIME type to
    prevent the serving of unsupported or malicious file types.
-   📱 **Responsive Design**: The interface is built with a responsive
    layout, ensuring a good user experience on both desktop and mobile
    devices.

------------------------------------------------------------------------

## 🛠 Requirements

-   A web server (like Apache, Nginx, or a local server like XAMPP)
-   PHP installed and configured on the web server

------------------------------------------------------------------------

## ⚙️ Installation

1.  **Clone the repository**: Clone or download this project to your web
    server's document root directory.

    ``` bash
    git clone https://github.com/Mahi-Rocks/php-video-player.git
    cd php-video-player
    ```

2.  **Create the video directory**: In the same directory as
    `index.php`, create a new folder named `converted`.

    ``` bash
    mkdir converted
    ```

3.  **Add your videos**: Place your video files (in supported formats)
    inside the newly created `converted` folder.

------------------------------------------------------------------------

## ▶️ Usage

1.  Navigate to the `index.php` file in your web browser.\
    For example, if you're using a local server, it might be:

        http://localhost/php-video-player/index.php

2.  Use the dropdown menu to select a video file you want to play.

3.  Click the **Play Video** button. The selected video will load and
    begin playback.

------------------------------------------------------------------------

## 📌 Example Project Structure

    php-video-player/
    │── index.php
    │── converted/
    │    ├── sample.mp4
    │    ├── movie.mkv
