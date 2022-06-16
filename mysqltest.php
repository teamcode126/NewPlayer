<?php
	    session_start(); 
if(isset($_POST['submit'])){
	$servername = "localhost";
	$username = "id6335536_clarence";
	$password = "helloworld";
	$dbname = "id6335536_clarencedb";
	$_SESSION['state']=='u';

	// 创建连接
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// 检测连接
	if (!$conn) {
	    die("连接失败: " . $conn->connect_error);
	} 

// create database
// $sql = "CREATE DATABASE myDB2";
// if ($conn->query($sql) === TRUE) {
//     echo "数据库创建成功";
// } else {
//     echo "Error creating database: " . $conn->error;
// }


//create data table
// $sql = "CREATE TABLE MyGuests (
// id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
// user_name VARCHAR(30) NOT NULL,
// password VARCHAR(30) NOT NULL
// )";

// if ($conn->query($sql) === TRUE) {
//     echo "Table MyGuests created successfully";
// } else {
//     echo "创建数据表错误: " . $conn->error;
// }

// $sql = "INSERT INTO MyGuests (id, user_name, password)
// VALUES ('1', 'master', 'helloworld')";

// if ($conn->query($sql) === TRUE) {
//     echo "新记录插入成功";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }

	// $sql = "SELECT id, user_name, password FROM MyGuests";
	// $result = $conn->query($sql);
	 
	// if ($result->num_rows > 0) {
	//     // 输出数据
	//     while($row = $result->fetch_assoc()) {
	//         echo "id: " . $row["id"]. " , Name: " . $row["user_name"]. ", Password: " . $row["password"]. "<br>";
	//     }
	// } else {
	//     echo "0 结果";
	// }


    
    // $usr = mysql_real_escape_string($_POST['username']); 
    // $pas = mysql_real_escape_string($_POST['pass']); 
    $usr = mysqli_real_escape_string($conn, $_POST['username']); 
    $pas = mysqli_real_escape_string($conn, $_POST['pass']); 

    
	$sql = "SELECT * FROM userlist WHERE user_name='$usr' AND password='$pas' LIMIT 1";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
	    // 输出数据
	    $row = $result->fetch_assoc();
	    if ($row["block_state"]=='0') {
	    	header("Location: login.php?id=false"); 
        	exit;
	    }else{	
	        $_SESSION['username'] = $row["user_name"]; 
	        $_SESSION['password'] = $row["password"];  
	        $_SESSION['logged'] = TRUE;
	        $_SESSION['state'] = $row["state"];
	        header("Location: index.php"); // Modify to go to the page you would like 
	        exit; 
	    }

    }else{
        header("Location: login.php?id=false"); 
        exit; 
    } 

    $conn->close();

}else{    //If the form button wasn't submitted go to the index page, or login page 
    header("Location: index.php?id=false");     
    exit; 
	//echo "not submitted";
}
?> 