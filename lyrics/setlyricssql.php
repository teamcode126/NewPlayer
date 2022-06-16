<?php
session_start();
$song_name=$_GET['songname'];


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


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
   $sql = "INSERT INTO song_lyrics (song_name, song_lyrics)
                VALUES ('$song_name', '$infomation')"; 
       
        if ($conn->query($sql) === TRUE) {
                    echo 'You really want set this string as a Lyrics? 
                            <a href="../index.php" title=""><button>Set</button></a>';
        } else {
            echo 'Error: ' . $sql . '<br>' . $conn->error.'<br><a href="../index.php" title=""><button>Return home</button></a>';
        }

    } else {
        echo 'Sorry, there was an error setting your lyrics.<a href="../index.php" title=""><button>Return home</button></a>';
    }
// echo $_SESSION['changealbum'];
$conn->close();


?>



