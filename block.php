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

    
	$sql = "UPDATE userlist SET block_state='0' WHERE user_name='$user_name'";
	$conn->query($sql);
    $conn->close();
    header("Location: usermanager.php");     
 

?> 