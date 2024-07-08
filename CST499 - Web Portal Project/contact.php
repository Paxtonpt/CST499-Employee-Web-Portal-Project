<?php
include "topmenu.php";
?>

<h1>Contact Us</h1>


<form method="POST" action="contact.php">
    <?php
    $_token = md5(time());
    $_SESSION["_token"] = $_token;
    ?>
    <input type="hidden" name="_token" value="<?php echo $_token; ?>" />

    <p>
        <label>Name</label>
        <input type="text" name="name" required>
    </p>

    <p>
        <label>Email</label>
        <input type="email" name="email" required>
    </p>

    <p>
        <label>Message</label>
        <textarea name="message" required></textarea>
    </p>

    <p>
        <input type="submit" name="contact_us" value="Send message">
    </p>
</form>

<?php

// check if the form submits
if (isset($_POST["contact_us"]))
{
    $_token = $_POST["_token"];

    // check CSRF token
    if (isset($_SESSION["_token"]) && $_SESSION["_token"] == $_token)
    {
        // remove the token so it cannot be used again
        unset($_SESSION["_token"]);

        // connect with database
        $conn = mysqli_connect("localhost:8889", "root", "root", "yourdbname");

        // get and validate input fields from SQL injection
        $name = mysqli_real_escape_string($conn, $_POST["name"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $message = mysqli_real_escape_string($conn, $_POST["message"]);
        $is_read = 0;

        // sending email
        $headers = 'From: YourWebsite <admin@yourwebsite.com>' . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $to = "admin@gmail.com";
        $subject = "New Message";

        $body = "<h1>Message from:</h1>";
        $body .= "<p>Name: " . $name . "</p>";
        $body .= "<p>Email: " . $email . "</p>";
        $body .= "<p>Message: " . $message . "</p>";

        mail($to, $subject, $body, $headers);

        // saving in database
        $sql = "INSERT INTO inbox(name, email, message, is_read, created_at) VALUES ('" . $name . "', '" . $email . "', '" . $message . "', " . $is_read . ", NOW())";
        mysqli_query($conn, $sql);

        echo "<p>Your message has been received. Thank you.</p>";
    }
    else
    {
        echo "<p>Something went wrong.</p>";
    }

    header("refresh: 5; url=contact.php");
}
?>
