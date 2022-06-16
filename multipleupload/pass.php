<?php 
	session_start();
	if ($_SESSION['logged']==TRUE) {
		header("Location: index.php");
	}else{
		header("Location: ../login.php?id=false");
	}
	
?>