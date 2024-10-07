<?php
// Database connection
$host = "localhost:3307";
$dbname = "login_db";
$username = "root";
$password = "";

// $servername = "localhost";
// $username = "your_username";
// $password = "your_password";
// $dbname = "DbGallery";

$conn = new mysqli(hostname: $host, 
					 username: $username,
					 password: $password, 
					 database: $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS tblPicture (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is actual image or fake image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // If everything is ok, try to upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $caption = $_POST['caption'];
            $sql = "INSERT INTO tblPicture (url, caption) VALUES ('$target_file', '$caption')";
            if ($conn->query($sql) === TRUE) {
                echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gallery</title>
</head>
<body>
    <h1>My Gallery</h1>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        Select image to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="text" name="caption" placeholder="Enter caption">
        <input type="submit" value="Upload Image" name="submit">
    </form>

    <h2>Gallery</h2>
    <?php
    // Display gallery
	$conn = new mysqli(hostname: $host, 
					 username: $username,
					 password: $password, 
					 database: $dbname);

    //$conn = new mysqli($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM tblPicture";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<img src='" . $row["url"] . "' alt='" . $row["caption"] . "' style='width:200px;'>";
            echo "<p>" . $row["caption"] . "</p>";
            echo "</div>";
        }
    } else {
        echo "No images in the gallery";
    }
    $conn->close();
    ?>
</body>
</html>
