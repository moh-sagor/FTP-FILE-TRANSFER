<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Transfer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main-container">
      <header>
            <div class="logo">
                <a href="index.php">File Transfer</a>
            </div>
            <button class="menu-toggle" aria-label="Toggle navigation" onclick="toggleMenu()">
                <i class="fas fa-bars" style="display: flex; margin-left: auto; align-items: right; justify-content: right; margin-top:-45px;"></i>
            </button>

            <nav id="navMenu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </nav>
        </header>

        <div class="container" >
            <div class="tabs">
                <div class="tab active" data-tab="send">Send</div>
                <div class="tab" data-tab="receive">Receive</div>
            </div>
            <div class="content">
                <div class="tab-content active" id="send">
                    <h1>Send File</h1>
                    <input type="file" id="file-input" multiple>
                    <div id="file-info"></div>
                    <button id="send-btn">Send</button>
                    <div class="progress-container">
                        <div class="progress-bar"></div>
                    </div>
                    <div class="code-container">
                        <span id="generated-code"></span>
                        
                    </div>
                </div>
                <div class="tab-content" id="receive">
                    <h1>Receive File</h1>
                    <input type="text" id="code-input" placeholder="Enter code">
                    <button id="receive-btn">Receive</button>
                </div>
            </div>
        </div>
        <footer>
            <p>&copy; 2024 File Transfer. All rights reserved.</p>
        </footer>
    </div>
    <script src="script.js"></script>
</body>
</html>