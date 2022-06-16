<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
</head>
<body>

<form action="upload.php?id=<?php  echo $_GET['id'];?>" method="post" enctype="multipart/form-data">
    Select image:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <!-- <input type="text" name="album_infomation" id="album_message" placeholder="Input infomation about this album or artist" maxlength="150"> -->
    Input information(not to include " or ')
    <textarea name="Message" cols="30" rows="5" placeholder="Input information about this album or artist"></textarea>
    <input type="submit" value="Set Image and information" name="submit">
</form>
<div>
	<a href="../index.php">
		<i class="fas fa-undo"></i>
	</a>
</div>


</body>
</html>