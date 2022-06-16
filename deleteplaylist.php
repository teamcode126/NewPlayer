<?php
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


	 $user_name=$_GET['name'];
	 $song_name=$_GET['songname'];
	// echo $user_name."/////////".$song_name;


	$sql = "DELETE FROM users_playlist WHERE songname='$song_name' AND username='$user_name'";
	if ($conn->query($sql)) {
		$conn->close();
 		header("Location: index.php");  
	}else{
		echo "Error in deleting playlist, contact with manager!";
	}
 	   
 

?> 