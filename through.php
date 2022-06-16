<?php
	session_start();
	$_SESSION['logged']=FALSE;
	$_SESSION['username'] = ''; 
    $_SESSION['password'] =''; 
    header("Location: index.php");
?>