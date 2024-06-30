<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $to = "menciagh4@gmail.com";
    $subject = "Contact Form Submission from $first_name $last_name";
    $body = "Name: $first_name $last_name\nEmail: $email\nMessage:\n$message";

    // Simulate email sending for demonstration
    if (mail($to, $subject, $body)) {
        $response = "Message sent successfully!";
    } else {
        $response = "Failed to send message.";
    }

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Submission Status</title>
        <link rel='stylesheet' href='./assets/styles/styles.css'>
        <link rel='icon' href='./assets/images/logo.ico' type='image/x-icon'>
    </head>
    <body>
        <header>
            <div id='header' class='container'>
                <div class='logo-title-container'>
                    <img src='./assets/images/logo.png' alt='SkyCast Logo' class='logo'>
                    <h1>SkyCast Weather</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href='homePage.php'>Home</a></li>
                        <li><a href='aboutUs.html'>About Us</a></li>
                        <li><a href='contact.html' class='active'>Contact Us</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class='container response-page'>
            <h2>Submission Status</h2>
            <p class='response-message'>$response</p>
            <button onclick=\"window.location.href='homePage.php'\">Return to Home Page</button>
        </main>
        <footer>
            <div id='footer' class='container'>
                <p>&copy; 2024 SkyCast Weather</p>
            </div>
        </footer>
    </body>
    </html>";
} else {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Invalid Request</title>
        <link rel='stylesheet' href='./assets/styles/styles.css'>
        <link rel='icon' href='./assets/images/logo.ico' type='image/x-icon'>
    </head>
    <body>
        <header>
            <div id='header' class='container'>
                <div class='logo-title-container'>
                    <img src='./assets/images/logo.png' alt='SkyCast Logo' class='logo'>
                    <h1>SkyCast Weather</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href='homePage.php'>Home</a></li>
                        <li><a href='aboutUs.html'>About Us</a></li>
                        <li><a href='contact.html' class='active'>Contact Us</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class='container response-page'>
            <h2>Invalid Request</h2>
            <p class='response-message'>This request is invalid. Please submit the form through the contact page.</p>
            <button onclick=\"window.location.href='homePage.php'\">Return to Home Page</button>
        </main>
        <footer>
            <div id='footer' class='container'>
                <p>&copy; 2024 SkyCast Weather</p>
            </div>
        </footer>
    </body>
    </html>";
}
?>
