<?php
session_start();
$id=$_GET['id'];
// echo $id;
// echo stripos($id,"artist")."<br/>";
// echo stripos($id,"album")."<br/>";


$target_dir = "uploads/";
 $servername = "localhost";
	$username = "id6335536_clarence";
	$password = "helloworld";
	$dbname = "id6335536_clarencedb";
      
    // 创建连接
    $conn = new mysqli($servername, $username, $password, $dbname);
     
    // 检测连接
    if ($conn->connect_error) {
        die("Did not access your database, please contact with developer " . $conn->connect_error);
    } 
$infomation=$_POST['Message'];
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imagename='';
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".<br/>";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Check if file already exists, but have to set the image as background image
if (file_exists($target_file)) {
    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " is already exists <br>" ;

        $_SESSION['imagename'] =basename( $_FILES["fileToUpload"]["name"]);

        // $albumid = mysqli_real_escape_string($albumid);
        $imagename='image/uploads/'.$_SESSION['imagename'];
        if (stripos($id,"artist")=="2") {
             $id=substr($id,8);
              $sql = "INSERT INTO artist_image (id, artist_name, image_name, artist_message)
                VALUES ('1', '$id', '$imagename', '$infomation')"; 
        }else{
            $id=substr($id,7);
            $sql = "INSERT INTO album_image (id, album_name, image_name, album_message)
                VALUES ('1', '$id', '$imagename', '$infomation')";    
        }
        
        if ($conn->query($sql) === TRUE) {
                    echo 'You really want set "'. basename( $_FILES["fileToUpload"]["name"]). '" as an Album or Artist Image? 
                            <a href="../index.php" title=""><button>Set</button></a>';
            // echo "新记录插入成功";
        } else {
            echo 'Error: ' . $sql . '<br>' . $conn->error.'<br><a href="../index.php" title=""><button>Return home</button></a>';
        }
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 8000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    // echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {


        $_SESSION['imagename'] =basename( $_FILES["fileToUpload"]["name"]);

        // $albumid = mysqli_real_escape_string($albumid);
        $imagename='image/uploads/'.$_SESSION['imagename'];
        if (stripos($id,"artist")=="2") {
             $id=substr($id,8);
             $sql = "INSERT INTO artist_image (id, artist_name, image_name, artist_message)
                VALUES ('1', '$id', '$imagename', '$infomation')"; 
        }else{
            $id=substr($id,7);
            $sql = "INSERT INTO album_image (id, album_name, image_name, album_message)
                VALUES ('1', '$id', '$imagename', '$infomation')";    
        }
        
        if ($conn->query($sql) === TRUE) {
                    echo 'You really want set "'. basename( $_FILES["fileToUpload"]["name"]). '" as an Album or Artist Image? 
                            <a href="../index.php" title=""><button>Set</button></a>';
            // echo "新记录插入成功";
        } else {
            echo 'Error: ' . $sql . '<br>' . $conn->error.'<br><a href="../index.php" title=""><button>Return home</button></a>';
        }

    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
// echo $_SESSION['changealbum'];
$conn->close();


?>



