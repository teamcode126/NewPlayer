<!DOCTYPE html>
<html>
<head>

</head>
<body>

<form action="setlyricssql.php?songname=<?php  echo $_GET['songname'];?>" method="post" enctype="multipart/form-data">
    Song name:<?php echo $_GET['songname'];?><br>
    Input Lyrics:
    <textarea name="Message" cols="30" rows="5" placeholder="Input the lyrics of this song"></textarea>
    <input type="submit" value="Set Lyrics" name="submit">
</form>
<div>
    <a href="../index.php">
        <i class="fas fa-undo"></i>
    </a>
</div>


</body>
</html>