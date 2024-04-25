<?php

// Function to scrape M3U8 URL from a webpage
function scrapeM3U8Url($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    
    // Regular expression to find the M3U8 URL
    preg_match('/"(https:\/\/.*\.m3u8.*?)"/', $output, $matches);
    
    if(isset($matches[1])) {
        return $matches[1];
    } else {
        return null;
    }
}

// Get the URL parameter
if(isset($_GET['url'])) {
    $url = $_GET['url'];

    // Get the M3U8 URL
    $m3u8Url = scrapeM3U8Url($url);

    // Redirect if M3U8 URL is found
    if($m3u8Url) {
        // Display the HLS player
        echo "<html>";
        echo "<head>";
        echo "<title>HLS Player</title>";
        echo '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>';
        echo '<style>
                body, html {
                  margin: 0;
                  padding: 0;
                  height: 100%;
                }
                #video-container {
                  position: relative;
                  width: 100%;
                  height: 100%;
                  background-color: #000;
                }
                #video {
                  width: 100%;
                  height: 100%;
                }
                #fullscreen-button {
                  position: absolute;
                  top: 10px;
                  right: 10px;
                  cursor: pointer;
                  color: #fff;
                  font-size: 20px;
                  z-index: 9999;
                }
                </style>';
        echo "</head>";
        echo "<body>";
        echo '<div id="video-container">
                <video id="video" controls autoplay></video>
                <span id="fullscreen-button">&#x26F6;</span>
              </div>';
        echo "<script>";
        echo "var video = document.getElementById('video');";
        echo "var fullscreenButton = document.getElementById('fullscreen-button');";
        echo "fullscreenButton.addEventListener('click', function() {
                if (video.requestFullscreen) {
                  video.requestFullscreen();
                } else if (video.webkitRequestFullscreen) { /* Safari */
                  video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) { /* IE11 */
                  video.msRequestFullscreen();
                }
              });";
        echo "if(Hls.isSupported()) {";
        echo "var hls = new Hls();";
        echo "hls.loadSource('" . $m3u8Url . "');";
        echo "hls.attachMedia(video);";
        echo "} else if (video.canPlayType('application/vnd.apple.mpegurl')) {";
        echo "video.src = '" . $m3u8Url . "';";
        echo "}";
        echo "</script>";
        echo "</body>";
        echo "</html>";
    } else {
        echo "M3U8 URL not found!";
    }
} else {
    echo "URL parameter is missing!";
}

?>
