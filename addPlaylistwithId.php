<?php
session_start(); 
	

	$user_name=$_SESSION['username'];
    $songname =$_GET['song']; 
    $album =$_GET['album']; 
    $artist =$_GET['artist']; 
    if ($user_name=='') {
    	header("Location: index.php");
    }
   	else{
   	$servername = "localhost";
	$username = "id6335536_clarence";
	$password = "helloworld";
	$dbname = "id6335536_clarencedb";

		// 创建连接
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		// 检测连接
		if (!$conn) {
		    die("连接失败: " . $conn->connect_error);
		}

		$sql = "INSERT INTO users_playlist (username, songname, artist, album) VALUES ('$user_name', '$songname', '$artist', '$album')"; 
		if ($conn->query($sql) === TRUE) {
		    header("Location: index.php"); 
		} else{
			echo 'Database error, please contact with manager' . $conn->error;;
	        // exit; 
	    } 
   	}


	$conn->close();

	
?> 