<?php 
session_start(); 
if(!$_SESSION['logged']){ 
    header("Location: ../index.html"); 
    exit; 
} 
else{
	if ($_GET['email']==null) {
		$tempmail='';
	}else{$tempmail=$_GET['email'];}
$_SESSION['email']=$tempmail;?>
	
<!DOCTYPE html>

<html>
<head>
   <title>Send Resume email</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
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
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
<form action="emailone.php" method="post" enctype="multipart/form-data">
<!-- /////////////////////////////////////////////////////////////////////////////////////////// -->
<div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg-01.jpg');">
			<div class="wrap-login100">
				<form class="login100-form validate-form">
					<span class="login100-form-logo">
						<i class="zmdi zmdi-landscape"></i>
					</span>

					<span class="login100-form-title p-b-34 p-t-27">
						Send Resume
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Enter username">
						<label for='uploaded_file'>User Name </label>
						<input class="input100" type="text" name="fname" placeholder="User Name">
            			<!-- <input class="input100" type="text" name="lname" placeholder="Last Name"> -->
						<span class="focus-input100" ></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="To">
						<label for='uploaded_file'>To Email </label>
						<?php echo $tempmail;?>
						<span class="focus-input100" ></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="To">
						<label for='uploaded_file'>Subject </label>
					<textarea  name="subject" rows="2" cols="40" placeholder="enter the subject"></textarea>
					<span class="focus-input100" ></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="To">
						<label for='uploaded_file'>Message </label>
					<textarea  name="message" rows="5" cols="40" placeholder="enter your useful message here"></textarea>
					<span class="focus-input100" ></span>
					</div>


					<td><label for='uploaded_file'>Select Resume</label></td>    
               		<div class="container-login100-form-btn">
               		<input type="file" name="attach"  accept=".doc,.docx, .pdf">
               		</div>
<!-- 					<div class="contact100-form-checkbox">
						<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
						<label class="label-checkbox100" for="ckb1">
							Remember me
						</label>
					</div> -->
					<div class="container-login100-form-btn">
					<input class="login100-form-btn" type="submit" name="submit" value="Send">
					</div>


				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
</form>
</body>
</html>
<?php
	//$_SESSION['logged']=FALSE;
    exit; 
}

?> 