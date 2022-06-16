<?php
	    session_start(); 
if(isset($_POST['submit'])){
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


    $usr = mysqli_real_escape_string($conn, $_POST['username']); 
    $mail = mysqli_real_escape_string($conn, $_POST['email']); 
    $pas = mysqli_real_escape_string($conn, $_POST['pass']); 
	$cpas = mysqli_real_escape_string($conn, $_POST['cpass']); 
	if ($pas !=$cpas) {
		$_SESSION['singupmessage']='Password must be same with Confirm Password';
		 header("Location: signup.php"); 
	}else{
		$_SESSION['singupmessage']='';


		$sql = "INSERT INTO userlist (id, user_name, email, password, state)
		VALUES ('1', '$usr', '$mail', '$pas', 'u')";

		if ($conn->query($sql) === TRUE) {
		    $_SESSION['singupmessage']='Your singup has successfuly done, please login';
		    header("Location: signup.php"); 
		} else{
			$_SESSION['singupmessage']='Database error, please contact with manager' . $conn->error;;
	        header("Location: signup.php"); 
	        exit; 
	    } 
	} 
	$conn->close();
}
    
	
?> 