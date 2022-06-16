<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Manage userlist</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="image/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/css/main.css">
<!--===============================================================================================-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
</head>
<body>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('image/bg-01.jpg');">
			<div class="wrap-login100" style="width: 1000px">
				<form class="login100-form validate-form" >
					<span class="login100-form-logo">
						<i class="zmdi zmdi-landscape"></i>
					</span>

					<span class="login100-form-title p-b-34 p-t-27">
						Manage userlist
					</span>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
						<thead>
							<td>Id</td>
							<td>Username</td>
							<td>Email</td>
							<td>Password</td>
							<td>Blockstate</td>
							<td>Block</td>
						</thead>
						<tbody>
							<?php
								$userdata='';
						    	session_start(); 

								$servername = "localhost";
                            	$username = "id6335536_clarence";
                            	$password = "helloworld";
                            	$dbname = "id6335536_clarencedb";

								// 创建连接
								$conn = mysqli_connect($servername, $username, $password, $dbname);
								$userid=1;
								// 检测连接
								if (!$conn) {
								    die("Cannot access database, please contact with manager " . $conn->connect_error);
								} else{	

									$sql = "SELECT * FROM userlist ";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
									    // output data of each row
									    while($row = $result->fetch_assoc()) {
									    	$user_name=$row["user_name"];
									    	$userdata=$userdata.'<tr><td>'. $userid.'</td><td>'. $row["user_name"].'</td><td>
									    	<a href="email/users_page.php?email='. $row["email"].'">' . $row["email"].'</a></td><td>' . $row["password"].'</td><td>'. $row["block_state"].'</td><td>
									    	<a onmouseover="mOver(this)" onmouseout="mOut(this)" href="block.php?name='.$user_name.'">
												<i class="fas fa-backspace" name="Block this user"></i>
											</a></td></tr>';
									    	$userid++;
									    }
									} else {
									    $userdata= '<td>0 users</td>';
									}
									$conn->close();
									echo $userdata;
								}
							?> 
						</tbody>
					</table>

				

					
				<!--	<div class="wrap-input100 validate-input" data-validate = "Enter username">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100" data-placeholder="&#xf207;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<input class="input100" type="password" name="pass" placeholder="Password">
						<span class="focus-input100" data-placeholder="&#xf191;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<input class="input100" type="password" name="cpass" placeholder="Confirm Password">
						<span class="focus-input100" data-placeholder="&#xf191;"></span>
					</div>

				<div class="contact100-form-checkbox">
						<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
						<label class="label-checkbox100" for="ckb1">
							Remember me
						</label>
					</div>
					<div class="container-login100-form-btn">
					<input class="login100-form-btn" type="submit" name="submit" value="Signup">
					</div>
					 <div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div> -->
					<div class="text-center p-t-90">
						<a class="txt1" href="index.php">
							Return
						</a>
					</div>

				</form> 

			</div>
		</div>
	</div>
<script type="text/javascript">
	function mOver(obj){
		obj.style.color='blue';
		// menustyle.style.width='300px';
	}
	function mOut(obj){
		obj.style.color='black';
	}
</script>
</body>
</html>