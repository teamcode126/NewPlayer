
<!-- **********************************table************************************** -->
<?php
   session_start();
   // if ($_SESSION['logged']==FALSE) {
   // 	header("Location: index.php"); 
   // }
   //$_SESSION['changealbum']='';
    // $_SESSION['album_name']='';
   // $_SESSION['playlistinfo']='';
   $servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "clarence_DB";
	 
	// 创建连接
	$conn = new mysqli($servername, $username, $password, $dbname);
	 
	// 检测连接
	if ($conn->connect_error) {
	    die("连接失败: " . $conn->connect_error);
	} 
	

error_reporting(E_ALL);
ini_set('display_errors', 'On');

//base options

//The path of the folder to read.
$path = 'uploads';
//True to recursively search into sub-folders, or an integer to specify the maximum depth.
$recurse = true;
//A Regexp filter for allowed file names. '/./' - any
$filter_allow = '/./';

//Regexp of files to exclude, have biger priority than $filter_allow
$filter_exclude =  '/(\.gif$|\.jpg$|\.jpeg$|\.png$)/i';
//Regexp of path for exclude, have biger priority than $filter_exclude
$filter_exclude_path = '/(\/\.svn|\/\.git|\/\CVS|\/\__MACOSX)/';
//True to read the files, false to read the folders only
$findfiles = true;
//Scip folders from result
$scipfolders = true;
// Whether try find the removed files when map comparasion
$findremoved = true;



 
// Method for count HASH of each file, for compare in future
// Important for make $findremoved work, but can cause a error on big files
// Possible values: 'md5', 'sha256', 'haval160,4', 'sha1', 'crc32' ...
// see http://www.php.net/manual/en/function.hash-algos.php
// false - for disable,
// $counthash = 'md5';


// Allow file download
$download = true;
// base name for a map file
$map_file_name = 'files_map';
// The date formating in the table
$date_format = 'Y-m-d H:i:s';
// How much show the files on the page
$pager_limit = 80;

//profiling
$time_exec = array();
$time_exec['start'] = getmicrotime();

//utilite functions

/**
 * Function to read the files/folders in a folder.
 * @return  array  Files.
 */
function getItems(
	$path, //The path of the folder to read.
	$recurse = true, //True to recursively search into sub-folders, or an integer to specify the maximum depth.
	$filter_allow = '/./', //A filter for file names.
	$filter_exclude = '', //Regexp of files to exclude
	$filter_exclude_path = '', //Regexp of path to exclude
	$findfiles = true, //True to read the files, false to read the folders
	$scipfolders = true, //Scip folders from result
	$counthash = false // Method used for count file hash, false - for disable
	){
		//@set_time_limit(ini_get('max_execution_time'));

		// Is the path a folder?
		if (!is_dir($path)){
			echo '<b>No valid folder path</b>';
			return false;
		}

		// // use RecursiveDirectoryIterator where it possible, php 5.2.x partiall suport
		// if (class_exists('RecursiveDirectoryIterator')) {//version_compare(PHP_VERSION, '5.3.0', 'ge')
		// 	return _getItemsDirectoryIterator($path, $recurse, $filter_allow,
		// 			$filter_exclude, $filter_exclude_path, $findfiles, $scipfolders, $counthash);
		// }

		$arr = array();
		

		// Read the source directory
		if (!($handle = @opendir($path))){
			return $arr;
		}

		$genre = array("Blues","Classic Rock","Country","Dance","Disco","Funk","Grunge",
						 "Hip-Hop","Jazz","Metal","New Age","Oldies","Other","Pop","R&B",
						 "Rap","Reggae","Rock","Techno","Industrial","Alternative","Ska",//22
						 "Death Metal","Pranks","Soundtrack","Euro-Techno","Ambient",
						 "Trip-Hop","Vocal","Jazz+Funk","Fusion","Trance","Classical",
						 "Instrumental","Acid","House","Game","Sound Clip","Gospel",//39
						 "Noise","AlternRock","Bass","Soul","Punk","Space","Meditative",
						 "Instrumental Pop","Instrumental Rock","Ethnic","Gothic",//50
						 "Darkwave","Techno-Industrial","Electronic","Pop-Folk",
						 "Eurodance","Dream","Southern Rock","Comedy","Cult","Gangsta",
						 "Top 40","Christian Rap","Pop/Funk","Jungle","Native American",
						 "Cabaret","New Wave","Psychadelic","Rave","Showtunes","Trailer",//71
						 "Lo-Fi","Tribal","Acid Punk","Acid Jazz","Polka","Retro",
						 "Musical","Rock & Roll","Hard Rock","Folk","Folk-Rock",
						 "National Folk","Swing","Fast Fusion","Bebob","Latin","Revival",
						 "Celtic","Bluegrass","Avantgarde","Gothic Rock","Progressive Rock",//93
						 "Psychedelic Rock","Symphonic Rock","Slow Rock","Big Band",
						 "Chorus","Easy Listening","Acoustic","Humour","Speech","Chanson",
						 "Opera","Chamber Music","Sonata","Symphony","Booty Bass","Primus",//109
						 "Porn Groove","Satire","Slow Jam","Club","Tango","Samba",
						 "Folklore","Ballad","Power Ballad","Rhythmic Soul","Freestyle",//120
						 "Duet","Punk Rock","Drum Solo","Acapella","Euro-House","Dance Hall",
						 "Goa","Drum & Bass", " Club-House", "Hardcore",  "Terror",  "Indie",
						 "BritPop", "Negerpunk", "Polsk Punk",  "Beat"," Christian Gangsta",
						  " Heavy Metal"," Black Metal", " Crossover", "Contemporary C",//141
						   "Christian Rock", "Merengue", "Salsa", "Thrash Metal", " Anime", "JPop",
						   " SynthPop", "Unknown");

		while (($file = readdir($handle)) !== false){
			// Compute the fullpath
			$fullpath = $path . '/' . $file;

			// Compute the isDir flag
			$isDir = is_dir($fullpath);

			if ($file != '.' && $file != '..'
				&& (empty($filter_exclude_path) || !preg_match($filter_exclude_path, $fullpath))
				&& (empty($filter_exclude) || !preg_match($filter_exclude, $file))
			){

				if ((($isDir && !$scipfolders) || ($findfiles && !$isDir))
					&& (empty($filter_allow) || preg_match($filter_allow, $file))
				){

					$about_file = array(
						'path' => '',
						'filename' => '',
						'ext' => '',
						'size' => 0, //size in bytes
						'type' => '', //file, folder, link
						'mtime' => '', //time of last modification (Unix timestamp)
						'ctime'	=> '', //time of last inode change (Unix timestamp)
						'state' => '',//1.same;2.changed;3.new;4.removed
						'title1' => '',
						'artist' => 'unknown',
						'album' => 'unknown',
						'year' => '',
						'comment' => '',
						'genre' => 'unknown',
					);

					$about_file['path'] = $fullpath;
					$about_file['filename'] = pathinfo($file, PATHINFO_FILENAME);
					$about_file['ext'] = pathinfo($file, PATHINFO_EXTENSION);
					$about_file['type'] = filetype($fullpath);

					if($about_file['type'] != 'link'){
						$stat = stat($fullpath);

						$about_file['size'] = $stat['size'];
						$about_file['mtime'] = $stat['mtime'];
						$about_file['ctime'] = $stat['ctime'];
						$about_file['mode'] = $stat['mode'];
					}


					$id_start=filesize($fullpath)-128; 
			        $fp=fopen($fullpath,"r"); 
			        fseek($fp,$id_start); 
			        $tag=fread($fp,3); 
			        if ($tag == "TAG") { 
			        	$about_file['title1']=fread($fp,30); 
			        	$about_file['artist']=chop(fread($fp,30)); 
			        	$about_file['album']=chop(fread($fp,30)); 
			        	$about_file['year']=fread($fp,4); 
			        	$about_file['comment']=fread($fp,30); 
			        	//$about_file['genre']=fread($fp,1); 
						$asciivalue= ord(fread($fp, 1));
						//Change the $data['genre'] inside the if tag, genre的值在0到147的范围内
						if ($asciivalue<148 && $asciivalue>=0) {//ord is to get ASCII value
							$about_file['genre'] = $genre[$asciivalue];
						}else $about_file['genre'] ='Unknown';
						///////////////////////////////////////

			        	fclose($fp); 
			       	} else { 
			        	fclose($fp); 
			        }

					$arr[$fullpath] = $about_file;
				}

				// Search recursively
				if ($isDir && $recurse){
					if (is_int($recurse)){
						// Until depth 0 is reached
						$arr = array_merge($arr, getItems($fullpath,  $recurse - 1, $filter_allow,
								$filter_exclude, $filter_exclude_path, $findfiles, $scipfolders, $counthash));
					}else{
						$arr = array_merge($arr, getItems($fullpath, $recurse, $filter_allow,
								$filter_exclude, $filter_exclude_path, $findfiles, $scipfolders, $counthash));
					}
				}
			}
		}
		closedir($handle);
		return $arr;
	}


/**
 * Function for sorting files/folders by on of they properties.
 * @return  array  sorted Files.
 */
function itemsSort(
	$files, //files Array
	$key, // key for sorting
	$dir = SORT_DESC // sorting direction SORT_DESC, SORT_ASC
) {
	$sort_arr = array();
	foreach ($files as $k => $f) {
		$sort_arr[$k] = $f[$key];
	}

	array_multisort($sort_arr, $dir, $files);

	return $files;
}
/**
 * format bytes
 * @return string
 */
function formatBytes($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');

	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	// Uncomment one of the following alternatives
	$bytes /= pow(1024, $pow);
	//$bytes /= (1 << (10 * $pow));

	return round($bytes, $precision) . ' ' . $units[$pow];
}
/**
 * State format
 * 1 = same, 2 = changed, 3 = new, 4 = removed
 * @return HTML string
 */
function stateFormat($state) {
	$title = 'unknown';
	switch ($state){
		case 1:
			$title = 'same';
			break;
		case 2:
			$title = 'changed';
			break;
		case 3:
			$title = 'new';
			break;
		case 4:
			$title = 'removed';
			break;
	}

	return '<span class="'.$title.'">'.$title.'</span>';
}
/**
 * Link to file, for allow download
 * @param string $full_path path to the file
 * @return string HTML link
 */
function linkToFile($full_path){
	return '<a href="?download='. base64_encode($full_path) . '" target="_blank"><i class="fas fa-download"></i></a>';
}

function setLyrics($song_name){
	return '<a href="lyrics/setlyrics.php?songname='. $song_name. '" name="Set Lyrics"><i class="far fa-file-alt"></i></a>';
}


/**
 * Download file method
 * @param string base64_encode file path
 */
function downloadFile($file){
	$file_path = realpath(base64_decode($file));

	if(!$file_path || !is_file($file_path) || !is_readable($file_path)){
		echo 'File "'.$file_path.'" not available!';
		exit;
	}

	//mime
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $file_path);

	//send to user
	header('Content-Description: File Transfer');
	header('Content-Type: '.$mime);
	header('Content-Disposition: attachment; filename="'.pathinfo($file_path, PATHINFO_BASENAME).'"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: '. filesize($file_path));
	ob_clean();
	flush();
	readfile($file_path);
	exit;
}

/**
 * build query
 * @return new query array or query string
 */
function buildUrlQuery($query, $build_query = false) {
	$old_query = !empty($_GET) ? $_GET : array();
	foreach ($query as $k => $v){
		$old_query[$k] = $v;
	}

	return $build_query ? http_build_query($old_query) : $old_query;
}


/**
 * Get the current time.
 * @return  float The current time
 */
function getmicrotime(){
	list ($usec, $sec) = explode(' ', microtime());
	return ((float) $usec + (float) $sec);
}

// check download request
if(!empty($_GET['download']) && $download){
	downloadFile($_GET['download']);
}





// init variables
$files = array();
$songlistarray= array();
$playlist_songlistarray=array();
$songlistall= array();
$albumlist=array();
$artistlist=array();
$lyrics=array();
$map_file = $map_file_name . '.map';
$map_file_current = empty($_GET['filelist']) ? $map_file : $_GET['filelist'].'.map';
$scan = isset($_GET['scan']);
$hashcompare = empty($_GET['hashcompare'])? false : $_GET['hashcompare'] .'.map';
$sort_by = empty($_GET['sort']) ? 'path' :  $_GET['sort'];
$sort_dir = empty($_GET['dir']) || $_GET['dir'] == 'asc' ? SORT_ASC : SORT_DESC ;
$page = empty($_GET['page']) || $_GET['page'] == 1 ? 0 : (int)  ($_GET['page'] - 1);



$table = '';//paint the table on music page
$artistinfo='';//paint the artist page
$albuminfo='';//paint the album page
$stored_maps = '';
$flag_log='';
$temp_username='';//using find users_playlist
$pager = '';
if(!isset($_SESSION['state'])){
   $_SESSION['state']='u';
}
if(!isset($_SESSION['logged'])){
   $_SESSION['logged']=FALSE;
   $flag_log=FALSE;
}else{
	$flag_log=$_SESSION['logged'];//give javascript the state of logged
}
if(!isset($_SESSION['username'])){
   	$_SESSION['username']='';
}else{
	$temp_username=$_SESSION['username'];
}
if(!isset($_SESSION['password'])){
   $_SESSION['password']='';
}
if(!isset($_SESSION['signupmessage'])){
   $_SESSION['signupmessage']='';
}
if(!isset($_SESSION['imagename'])){
   $_SESSION['imagename']='';
}


if ($_SESSION['state']=='a') {
		$table_head = array(
			'no' => 'No',
			'filename' => 'File Name',
			'artist' => 'Artist',
			'album' => 'Album',
			'size' => 'Size',
			// 'mtime' => 'Last modification',
			// 'ctime' => 'Last inode change',
			// 'state' => 'File state',
			// 'title1' => 'Title',
			// 'year' => 'Year',
			// 'comment' => 'Comment',
			'genre' => 'Genre',
			'path' => 'Download',
			'setlyrics' => 'Lyrics'
		);
		
	}else{
		$table_head = array(
			'no' => 'No',
			'filename' => 'File Name',
			'artist' => 'Artist',
			'album' => 'Album',
			'other' => 'More',
			'genre' => 'Genre',
			'size' => 'Size',
			);

	}


//prepare base URL query
$_GET['filelist'] = empty($_GET['filelist']) ? $map_file_name : $_GET['filelist'];
if (isset($_GET['scan'])) {
	unset($_GET['scan']);
}
if ($scan && isset($_GET['hashcompare'])) {
	unset($_GET['hashcompare']);
	$hashcompare = false;
}

//run
if(is_file($map_file_current) && !$scan && $stored_data = file_get_contents($map_file)){
	$files = unserialize($stored_data);
	$time_exec['Open Stored'] = getmicrotime();
} elseif ($scan) {
	$files = getItems($path, $recurse, $filter_allow, $filter_exclude,
			$filter_exclude_path, $findfiles, $scipfolders);

	file_put_contents($map_file, serialize($files));
	$time_exec['Scan Files'] = getmicrotime();
}

$files = itemsSort($files, $sort_by, $sort_dir);
$time_exec['Sorting'] = getmicrotime();

$files_total = count($files);

//cut by pager
if ($pager_limit && $files_total > $pager_limit) {
	$pager_start = $pager_limit * $page;
	$files =  array_slice($files, $pager_start, $pager_limit);

	//build pager
	$pages_total = round($files_total/$pager_limit, 0, PHP_ROUND_HALF_DOWN);
	$pager .= '<a href="?'.buildUrlQuery(array('page' => 1), true).'"><< First</a>&nbsp;...&nbsp;';
	//5 prev and 5 next
	for($i = max(1, $page - 5); $i <= min($page + 5, $pages_total); $i++){
		$pager .= '<a href="?'.buildUrlQuery(array('page' => $i), true).'">'.$i.'</a>';
	}
	$pager .= '&nbsp;...&nbsp;<a href="?'.buildUrlQuery(array('page' => $pages_total), true).'">Last >></a>';
}


//render table
$table = '<table id="table">';
$id=0;
//head
$table .= '<thead><tr>';
foreach ($table_head as $k => $h) {
	$active = ($k == $sort_by);
	if ($h!='Download' && $h!='No' && $h!='More') {//'Download' not show sort button
		$table .= '<th class="'.$k.'">'
				. $h
				. '&nbsp;&nbsp;'
				. '<a href="?'.buildUrlQuery(array('sort' => $k, 'dir' => 'desc'), true).'">'.(($active && $sort_dir == SORT_DESC) ? '&#11014;' : '&uarr;').'</a>'//desc
				. '&nbsp;'
				. '<a href="?'.buildUrlQuery(array('sort' => $k, 'dir' => 'asc'), true).'">'.(($active && $sort_dir == SORT_ASC) ? '&#11015;' : '&darr;') .'</a>'//asc
				.'</th>';
	}else $table .= '<th class="'.$k.'">'. $h;
			
}
$table .= '</tr></thead>';
$artistid=0;
$albumid=0;
if($files_total) {
	//rows
	$table .= '<tbody>';
	$columns = array_keys($table_head);
	$id=0;
	foreach ($files as $file) {
		$id++;
		// echo implode("|",$file).'<br/>';
		$table .= '<tr  id="tr'.$id.'" >';
		foreach ($columns as $col) {
			switch ($col) {
				case 'no':
					$table .= '<td onmouseover="mOver(this)" onmouseout="mOut(this)">'.$id.'</td>';
					break;
				case 'size':
					$table .= '<td >'.formatBytes($file[$col]).'</td>';
					$songlistall[$id]['size']= formatBytes($file[$col]);
					break;
				case 'mtime':
				case 'ctime':
					$table .= '<td>'. ($file[$col] ? date($date_format, $file[$col]) : '') .'</td>';
					break;
				// case 'state':
				// 	$table .= '<td>'.stateFormat($file[$col]).'</td>';
				// 	break;
				case 'path':
					$table .= '<td>'. ( $download ? linkToFile($file[$col]) : $file[$col]) .'</td>';
					// $songlistall[$id]['path'] = ($download ? linkToFile($file[$col]) : $file[$col]);
					break;

				case 'filename':
					$table .= '<td onclick="playSongwithId('.$id.')" >'.$file[$col].'</td>';
					$songlistarray[$id] = '"'.$file[$col].'.mp3"';
					$songlistall[$id]['filename']= $file[$col];
					break;
				case 'artist':
					if (strpos(implode($artistlist, " "),$file[$col])=='0') {//add artist list
						$artistlist[$artistid++]= $file[$col];
					}
					$table .= '<td onclick="playSongwithId('.$id.')" >'.$file[$col].'</td>';
					$songlistall[$id]['artist'] = $file[$col].' ';
					break;
				case 'album':
					$table .= '<td onclick="playSongwithId('.$id.')" >'.$file[$col].'</td>';
					$songlistall[$id]['album'] = $file[$col].' ';
					if (strpos(implode($albumlist, " "),$file[$col])=='0') {//add album list
							$albumlist[$albumid++]= $file[$col];
						}
					break;
				case 'other':
					$backgroundimage='url(image/album/noalbum.png)';
					$temptable='<div class="dropdown">
								<button onclick="functionMore('.$id.')" class="dropbtn" id="dropbtn'.$id.'">
									---
								</button>
							  <div id="myDropdown'.$id.'" class="dropdown-content">
							  	<div class="dropdownmenu">
							  		<i class="fa fa-play" style=" "></i>
							    	<a onclick="playSongwithId('.$id.')">Play</a>
							    </div>
							    <div class="dropdownmenu">
							  		<i class="far fa-heart"></i>
							    	<a onclick="addLikewithId()">Like</a>
							    </div>
							    <div class="dropdownmenu">
							  		<i class="fas fa-plus"></i>
							    	<a href="addPlaylistwithId.php?song='.$songlistall[$id]['filename'].'&amp;artist='.$songlistall[$id]['artist'].'&amp;album='.$songlistall[$id]['album'].'">Add Playlist</a>
							    </div>
							    <div class="dropdownmenu">
							  		<i class="far fa-file-alt"></i>
							    	<a onclick="showLyricswithId()">Show Lyrics</a>
							    </div>
							    <div class="dropdownmenu">
							  		<i class="fas fa-share"></i>
							    	<a onclick="share()">Share</a>
							    </div>

							  </div>
							</div>';
							   // <div class="dropdownmenu">
							  	// 	<i class="fas fa-compact-disc"></i>
							   //  	<a onclick="showAlbumWithId('.$tempalbum.','.$backgroundimage.')">Go to Album</a>
							   //  </div>
							   //  <div class="dropdownmenu">
							  	// 	<i class="far fa-user"></i>
							   //  	<a onclick="showArtistWithId()">Go to Artist</a>
							   //  </div> 


					$table.='<td>'.$temptable.'</td>';
					// $songlistall[$id]['other']=$temptable;
					break;
				case 'genre':
					$table .= '<td>'.$file[$col].'</td>';
					$songlistall[$id]['genre'] = $file[$col].' ';
					break;
				case 'setlyrics':
					$temp=$songlistall[$id]['filename'];
					$table .= '<td>'.  setLyrics($temp) .'</td>';
					break;
				default:
					break;
			}

		}
		$table .= '</tr>';
	}
	$table .= '</tbody>';
} else {
	$table .= '<tbody>
	<tr>
		<td colspan="'.count($table_head).'">
			<p>No filemap found. Please click <a href="?'. buildUrlQuery(array('scan' => true, 'hashcompare' => ''), true).'" title="click for scan">"Scan"</a></p>
		</td>
	</tr>
</tbody>';
}
$table .= '</table>';




//*********************************************sql ************************************************************//
	$sql = "SELECT song_name, song_lyrics FROM song_lyrics";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$id=1;
	    // 输出数据
	    while($row = $result->fetch_assoc()) {
	        $lyrics[$id]['song_name']=$row["song_name"];
	        $lyrics[$id]['song_lyrics']=$row["song_lyrics"];
	        $id++;
	    }
	}



	$sql = "SELECT id, album_name, image_name,album_message FROM album_image";
	
	//  if ($result->num_rows > 0) {
	//     // 输出数据
	//     while($row = $result->fetch_assoc()) {
	//         echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
	//     }
	// } else {
	//     echo "0 结果";
	// }
	
	


$albumid=0;
// $changealbum=0;
foreach ($albumlist as $album) {
	$albumid++;
	$result = $conn->query($sql);
	$backgroundimage='url(image/album/noalbum.png)';
	$album_message='No information about this album';
	$album_message="'".$album_message."'";
	// if ($albumid=='2') {
	//         	$backgroundimage='url(image/album/noalbum.png)';
	//          }
	if ($result->num_rows > 0) {
	    // 输出数据
	    while($row = $result->fetch_assoc()) {
	        if ($row["album_name"]==$album) {
	        	$backgroundimage='url('.$row["image_name"].')';
	        	$album_message="'".$row["album_message"]."'";
	         }
	    }
	}
	$backgroundimagewithouturl="'".$backgroundimage."'"; 
	// if ($_SESSION['album_name']==$album) {
		// $backgroundimage='url(image/uploads/'.$_SESSION['imagename'].')';
	// }else{
		
	// }
		$album_with_trim="'".$album."'";
		$albuminfo .='<div class="ui-obj " id="albumdiv"> 	
							<div class="ui-obj-image" id="album'.$albumid.'" style="background-image:'. $backgroundimage.';"
								onmouseover="hiddenimg(this);" onmouseout="showimg(this)" onclick="showAlbumsWithId('.$album_with_trim.','.$backgroundimagewithouturl.','.$album_message.')">
								<i class="far fa-play-circle" style="padding-top:100px;"></i>
							</div>
							<div class="ui-obj-info" >                          
								<div class="ui-obj-name ui-text-overflow-ellipsis">'
									.$album.
								'</div>
								<div class="ui-obj-songs" >
									<a href="image/imageupload.php?id=12album'.$album.'" title="upload file">
									<i class="fas fa-image"  title="Set image" onmouseover="mOver(this)" onmouseout="mOut(this)"></i>
									</a>
								</div>                     
							</div>                                          
					</div>';
}
// $_SESSION['changealbum']=$changealbum;



$artistid=0;
$changeartist=0;
	$sql = "SELECT id, artist_name, image_name,artist_message FROM artist_image";
foreach ($artistlist as $artist) {
	$artistid++;
	$result = $conn->query($sql);
	$backgroundimage='url(image/artist/noartistimage.png)';
	$artist_message='No information about this artist';
	$artist_message="'".$artist_message."'";
	if ($result->num_rows > 0) {
	    // 输出数据
	    while($row = $result->fetch_assoc()) {
	        if ($row["artist_name"]==$artist) {
	        	$backgroundimage='url('.$row["image_name"].')';
	        	$artist_message="'".$row["artist_message"]."'";
	         }
	    }
	} 
	$backgroundimagewithouturl="'".$backgroundimage."'"; 
	$artist_with_trim="'".$artist."'";

	$artistinfo .='<div class="ui-obj " id="artistdiv">                      
						<div class="ui-obj-image" id="artist'.$artistid.'"  style="background-image:'. $backgroundimage.';" onmouseover="hiddenimg(this);" onmouseout="showimg(this)" onclick="showArtistWithId('.$artist_with_trim.','.$backgroundimagewithouturl.','.$artist_message.')">
							<i class="far fa-play-circle" style="padding-top:100px;"></i>
						</div>                    

						<div class="ui-obj-info">                          
										                          
							<div class="ui-obj-sec-name ui-text-overflow-ellipsis" >'.$artist.'</div> 
                    		<div class="ui-obj-songs" >
								<a href="image/imageupload.php?id=12artist'.$artist.'" title="upload file">
									<i class="fas fa-image"  title="Set image" onmouseover="mOver(this)" onmouseout="mOut(this)"></i>
								</a>
							</div>
							               
						</div>		              
					</div>';
}

$playlistinfostring='';
$sql = "SELECT  songname, artist, album FROM users_playlist WHERE username='$temp_username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$playlistid=1;
	    // 输出数据
	    while($row = $result->fetch_assoc()) {
	    	$playlist_songlistarray[$playlistid]="'".$row["songname"].".mp3'";
	    	$playlistinfostring .='<tr onmouseover="mOver(this)" onmouseout="mOut(this)" 
	    					onclick="playSongwithId('.$playlistid.');"><td>'
	    												.$playlistid.'</td><td>'.
														$row["songname"].'</td><td>'.
														$row["artist"].'</td><td>'.
														$row["album"].'</td><td>
														<a onmouseover="mOver(this)" onmouseout="mOut(this)" 
														href="deleteplaylist.php?name='.$temp_username.
														'&songname='.$row["songname"].'">
															<i class="fas fa-backspace" name="Block this user"></i>
														</a></td></tr>';
			$playlistid++;
	    }
	}else{
		if ($temp_username==null) {
			$playlistinfostring ="<tr><td>Not login cannot have playlist</td></tr>";
		}else{
			$playlistinfostring ="<tr><td>You have not playlist yet</td></tr>";
		}
	} 

$conn->close();

?>
<!-- *******************************table********************************** -->


<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<title>HTML5 Music Player</title>
	<link href='assets/css/styles.css' rel='stylesheet' type='text/css'>
	<link href='assets/css/styles1.css' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="assets/css/font-awesome-4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
	<link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon" />

</head>
<body>

	<style type="text/css">

		
		<?php 
			if ($_SESSION['state']=='u') {
				echo '.ui-obj-songs {
	            display:none;
	        	}';
	        	echo '.ui-userlist-table {
	            display:none;
	        	}';
			}
		?>

        
        
		#cover-art{
			display: -webkit-flex;
			display: flex;
			-webkit-flex: 1 auto;
			flex: 1 auto;
			position: relative;
			overflow: hidden;
			pointer-events: auto;	 
		}

		#cover-art-big{
			background-color: rgb(237, 237, 237);
			margin: -12px ;
			-webkit-flex: 1;
			flex: 1;
			background-size: cover;
			-webkit-filter: none;
			margin: -12px ;
			display: flex;
		}

		#cover-art-big div:nth-of-type(1) {flex-grow: 1;}
		#cover-art-big div:nth-of-type(2) {flex-grow: 10;}
		.ui-title {
			font-size:50px;		
		}
		#ui-content-music-tab-btns{margin-bottom:30px; height: 30px; width: 100%;}
		.ui-content-music-tab-btn.active,.ui-content-music-tab-btn{
			text-align: center;
			display:inline-block;
			position: relative;
			font-weight:600;
			height:30px;
			line-height:30px;
			margin-right:30px;
			font-size: 35px;
		}
		.ui-content-music-tab-btn.active{color: #000000}
		.ui-content-music-tab-btn{color: #888888}
		.ui-page-filter{
			display:inline-block;
			margin-right:20px;
			font-size: 30px;
			position:relative}
		.ui-select-dropdown{
			position:absolute;
			min-width:115px;
			top:0;
			left:22px;
			z-index:100;
			line-height:36px;
			max-height:318px;
			max-width:180px;
			overflow:auto}
		.ui-dropdown-menu{
			position:absolute;
			display:none;
		}
		.ui-text-overflow-ellipsis{
			-o-text-overflow:ellipsis;
			text-overflow:ellipsis;
			overflow:hidden;
			white-space:nowrap
		}
		.currenttime{
			float: left;
			font-size: 10px;
		}
		.duration{
			float: right;
			font-size: 10px;
		}

		.ui-content-music-tab-btn{
			cursor: pointer;
		}
		.ui-content-music-tab-btn.active{
			cursor:default;
		}

		
		.ui-page{
			font-size: 20px;
			font-family:"Comic Sans MS", cursive, sans-serif;
			color: #555555;
		}
		.ui-title{
			margin-bottom: 50px;
			font-family: Tahoma, Geneva, sans-serif;
		}

		table {
			width: 100%;
			border: 1px solid grey;
			border-spacing: 0;
			border-collapse: collapse;
		}
		table th,table td {
			text-align: left;
			padding: 3px;
		}
		table th {
			border: 1px solid grey;
			background: #F2F2F2;
		}
		table th a {color: #000;}
		table tr:nth-child(even) {
			background: #F2F2F2;
		}
		table tr:hover{
			background: #EAF2D3;
		}
		.pager a{
			padding: 3px;
			margin-right: 3px;
		}
		span.new,
		span.changed,
		span.removed {color: #bf1122;}
		span.same{ color: #009159}

		.ui-menu span{
			visibility: hidden;}

		.ui-menu-search input{background:0 0;
		border:none;outline:0;
		font-size:18px;
		font-weight:inherit;
		height:100%;
		line-height:48px;
		width:100%;margin-left:14px;
		float:left;
		font-family:inherit}

/*		#ui-menu{
			background-color: #00ffff;
			display: flex;
			flex-direction: column;
			text-align: center;
			justify-content: center;

		}*/

	#container.disabled .player-control div{
		color: black;
	}
	.downpart, .downpart div{
		background-color:pink;
	}

	.ui-content-is-empty{
	display:none}

	.ui-page{
		margin-left: 50px;
		margin-top: 50px;
	}

	.ui-content-music-tab-content{display:none}

	.ui-content-music-tab-content.active{display:block}


.ui-obj{float:left;width:166px;height:240px;margin-right:30px;margin-bottom:30px;position:relative}

.ui-obj-image{
	background-position:center;
	-webkit-background-size:cover;
	-moz-background-size:cover;
	-o-background-size:cover;
	background-size:cover;
	width:166px;
	height:166px;
	text-align:center;
	line-height:166px;
	font-size:60px;
	z-index:2;
	position:relative}
.ui-obj-image-blur{
	background-position:center;
	-webkit-background-size:cover;
	-moz-background-size:cover;
	-o-background-size:cover;
	background-size:cover;
	width:166px;
	height:166px;
	text-align:center;
	line-height:166px;
	font-size:60px;
	z-index:2;
	position:relative
	-webkit-filter:blur(2px);
	filter:blur(2px);
	/*display: none;*/
}
.ui-obj-info{padding:14px 0;text-align:center}
/*///////////////////////////////////////////////////////////*/
.dropbtn {
    /*background-color: #4CAF50;*/
    color: black;
    padding: 16px;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

.dropbtn:hover, .dropbtn:focus {
    background-color: #aaaaaa;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #aaeeee;
    min-width: 200px;
    overflow: auto;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: #333333;
    padding: 12px;
    text-decoration: none;
    display: block;
}

.dropdown-content i{
    padding: 18px 10px;
}

.dropdown a:hover {background-color: #bbeeee}

.show {display:block;}
.dropdownmenu{
	display:flex; 
	flex-direction:row
}
.ui-content-class{ 
	display: block;
	width: 90%; 
	left: 108px; 
	border:solid #cccccc; 
	border-style: none none none solid;
	background-color: rgb(237, 237, 237); 
	overflow: scroll; 
	overflow-x: scroll;}
/*////////////////////////////*/
.modal {
    display: none; /* 默认隐藏 */
    position: fixed; 
    z-index: 1; 
    padding-top: 100px; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgb(0,0,0); 
    background-color: rgba(0,0,0,0.4);
}

/* 弹窗内容 */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* 添加动画 */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* showlyrics关闭按钮 */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

/* share关闭按钮 */
.close1 {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close1:hover,
.close1:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}        

.modal-header {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}

.modal-body {padding: 2px 16px;}

.modal-footer {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}
        
#share-buttons img {
width: 35px;
padding: 5px;
border: 0;
box-shadow: 0;
display: inline;
}        
	</style>

    
<div id="container" class="disabled">

	<div id="cover-art" style="height: 90%">
		<div id="cover-art-big" >
			<div id="ui-menu" style="width: 10%">
				<!-- <div id="ui-menu-bg" style="background-image: none; width: 715px; height: 715px;"></div> -->
				<!-- <div id="ui-menu-bg-brightness"></div> -->
				<!-- <div class="ui-menu ui-menu-back ui-hide">
					<i class="ui-icon-back"></i>
				</div> -->
				<div style="height: 50px;"></div>
				<div class="ui-menu ui-menu-menu" onmouseover="mOver(this)" onmouseout="mOut(this)" onclick="changemenu()">
					<i class="fas fa-align-justify"></i>
				</div>

				<div style="height: 50px;"></div>
				<div class="ui-menu ui-menu-search" data-ui-page="search" onmouseover="mOver(this)" onmouseout="mOut(this)">
					<i class="fas fa-search" id="search-icon" onclick="changemenu()"></i>
					<input type="search" id="ui-search" style="display: none" placeholder="Search" onsearch="search()">

					
					
				</div>
				<div style="height: 50px;"></div>
				<div class="ui-menu-div-ul">
					<ul>
						<li class="ui-menu active" onclick="showMusic()" onmouseover="mOver(this)" onmouseout="mOut(this)">
							<i class="fas fa-music"></i>
							<span>Music</span>
							
						</li>
						
						<div style="height: 30px;"></div>
						<li class="ui-menu" onclick="showPlaying()" onmouseover="mOver(this)" onmouseout="mOut(this)">
							<i class="fab fa-mixcloud"></i>
							<span>Playing</span>
							
						</li>
						<div style="height: 30px;"></div>
						<li class="ui-menu" onclick="showLiked()" onmouseover="mOver(this)" onmouseout="mOut(this)">
							<i class="far fa-heart"></i>
							<span>Liked</span>
						</li>
						<div style="height: 30px;"></div>
						<li class="ui-menu" onclick="showPlaylists()" onmouseover="mOver(this)" onmouseout="mOut(this)">
							<i class="fas fa-list-alt"></i>
							<span>Playlists</span>
						</li>
						<div style="height: 30px;"></div>
						<li class="ui-menu" onclick="showRicent()" onmouseover="mOver(this)" onmouseout="mOut(this)">
							<i class="fas fa-history"></i>
							<span>Recent</span>
						</li>
						<div style="height: 30px;"></div>
						<li class="ui-menu" onclick="showSettings()" onmouseover="mOver(this)" onmouseout="mOut(this)">
							<i class="fas fa-cog"></i>
							<span>Settings</span>
						</li>
						<div style="height: 30px;"></div>
						<?php 
							if ($_SESSION['logged']==TRUE) {
							echo '<a  href="through.php" >
									<li class="ui-menu" title="Logout"  onmouseover="mOver(this)" onmouseout="mOut(this)">
										<i class="fas fa-sign-out-alt"></i>
										<span>Log out</span>
									</li>
								</a>';
							}else{
							echo '<a href="login.php?id=true" >
									<li class="ui-menu" title="Login" onmouseover="mOver(this)" onmouseout="mOut(this)">
										<i class="fas fa-sign-in-alt"></i>
										<span>Log In</span>
									</li>
								</a>';
							};
 
						?>
						

					</ul>

						
				</div>
			</div>
<!-- ****************************************Left Menu end************************************************************** -->

			<div id="ui-content" class="ui-content-class"> 
				<div style="padding-top: 30px; padding-right: 30px; text-align: right;">
					<i class="fas fa-user"> <?php echo $temp_username; if ($temp_username==null) {echo "Not Login";} ?></i>
					
				</div>
				                        
				<div class="ui-page active" id="music-page">

					<div class="ui-title" >Music</div>
                    <!-- I got these buttons from simplesharebuttons.com -->

						                                                                           
					<div class="ui-content" data-ui-page="music">
						

						<div id="ui-content-music-tab-btns">
							<div class="ui-content-music-tab-btn active" style="left: 10px" id="songs-tag" onclick="showSongs()">
								Songs
							</div>
							<div class="ui-content-music-tab-btn" style="left: 10px" id="albums-tag" onclick="showAlbums()">
								Albums
							</div>
							<div class="ui-content-music-tab-btn" style="left: 10px" id="artists-tag" onclick="showArtists()">
								Artists
							</div>
						</div>
        
						<div class="ui-content-music-tab-content active" id="songs-tab">
							<!-- <div class="ui-page-more">              
								<div class="ui-page-filter">
									<div class="ui-filter-btn" data-ui-id="shuffle">
										<i class="fas fa-random"></i>
										<span>Shuffle all</span>
									</div>
								</div>              
								<div class="ui-page-filter ui-select-home ui-dropdown-home">                 
									<div class="ui-filter-btn ui-select-btn ui-dropdown-btn" data-ui-id="sorting">
										<i class="fas fa-sort-amount-down"></i>
										<span class="ui-select-current" data-ui-id="__All_genres__">
										Date added
										</span>
									</div>                 
									<div class="ui-select-dropdown ui-dropdown-menu" data-ui-for="sorting">                   
										<ul>                     
											<li class="ui-select-selected" data-ui-id="date">Date added</li>                     
											<li data-ui-id="title">Title</li>                     
											<li data-ui-id="year">Year</li>                     
											<li data-ui-id="artists">Artists</li>                     
											<li data-ui-id="albums">Albums</li>                     
											<li data-ui-id="genres">Genres</li>                   
										</ul>                 
									</div>              
								</div>              
								<div class="ui-page-filter ui-select-home ui-dropdown-home">                 
									<div class="ui-filter-btn ui-select-btn ui-dropdown-btn" data-ui-id="genres">
										<i class="fas fa-music"></i>
										<span class="ui-select-current" data-ui-id="__All_genres__">
										All genres
										</span>
									</div>                 
									<div class="ui-select-dropdown ui-dropdown-menu" data-ui-for="genres">                  
										<ul>                     
											<li class="ui-select-selected" data-ui-id="__All_genres__">
											All genres
											</li>                   
										</ul>                 
									</div>              
								</div>              
							</div> -->
							<div id="files-table" style="padding-right: 20px; padding-bottom: 20px">
								
								<div class="top-part" style="display: flex;">
									<div  style="width: 90%">
										<p>
											Files total: <?php echo $files_total;?><br/>	
										</p>
									</div>
									<div style="width: 10%; display: inline; align-content: flex-end;">
										<a href="?<?php echo buildUrlQuery(array('scan' => true, 'hashcompare' => ''), true); ?>" title="click for scan again"><i class="fas fa-sync-alt" ></i></a>
										<a href="multipleupload/pass.php?user_name=<?php echo $_SESSION['username']?>" title="upload file"><i class="fas fa-upload"></i></a>
										<a class="ui-userlist-table" href="usermanager.php" title="manage userlist"><i class="fas fa-users-cog"></i></a>
									</div> 	
								</div> 
								<?php if ($hashcompare): ?> vs <b><?php echo $hashcompare; ?></b> <?php endif; ?>
								<?php echo $table; ?>
								<!-- <table>
									<tbody>
										<tr ><td onclick="playSongwithId('2')">ok</td></tr>
									</tbody>
									
								</table> -->
								

								
							</div>
								<p class="pager"><?php echo $pager; ?></p>	
						</div>





						<div class="ui-content-music-tab-content" id="albums-tab" >
							

							<!--	<div class="ui-obj " data-ui-type="album" data-ui-id="1227072750">                      
									<div class="ui-obj-image" style="background-image: url(&quot;https://lastfm-img2.akamaized.net/i/u/300x300/0956ddf2bd294e55ce6ba63dc3b8d98b.png&quot;);"onmouseover="hiddenimg(this);" onmouseout="showimg(this)">
										<i class="far fa-play-circle" id="playalbum1" style="display: none; padding-top:100px"></i>
									</div>                    

									<div class="ui-obj-info">                          
										<div class="ui-obj-name ui-text-overflow-ellipsis" title="21">21</div>                          
										<div class="ui-obj-sec-name ui-text-overflow-ellipsis" title="Adele">Adele</div>                   
									</div>                      
										<div class="ui-obj-songs"></div>                      
								</div> -->
								
								<!-- <?php echo $albuminfo; ?> -->
							<!-- </div> -->
						</div>

                        
						<div class="ui-content-music-tab-content" id="aritsts-tab" >
							<div class="vscroll-wrapper" data-columns="3">
								<!-- <div class="ui-obj " data-ui-type="artis" data-ui-id="182160020">                      
									<div class="ui-obj-image" style="background-image: url(&quot;https://lastfm-img2.akamaized.net/i/u/300x300/c8cfa0a250a26a361f26fed40b5aec45.png&quot;);" onmouseover="hiddenimg(this);" onmouseout="showimg(this)">
										<i class="far fa-play-circle" id="playalbum" style="display:none;padding-top:100px;"></i>
									</div>                    

									<div class="ui-obj-info">                          
										                          
										<div class="ui-obj-sec-name ui-text-overflow-ellipsis" title="Adele">Adele</div> 
										<div class="ui-obj-sec-name ui-text-overflow-ellipsis">                  
										</div>                      
											<div class="ui-obj-songs"></div>                      
									</div>
								
									              
								</div>-->

							</div>
						</div>
					</div>
				</div>

				<div id="myModal" class="modal">

				  <!-- 弹窗内容 -->
					<div class="modal-content">
					  <div class="modal-header">
					    <span class="close">&times;</span>
					    <h2>Song Lyrics</h2>
					  </div>
					  <div class="modal-body">
					  	<br/>
					  	<br/>
					  	<br/>
					    
					    <p id="song_lyrics_text_p"></p>
					    
					    
					    <br/>
					    <br/>
					    <br/>
					    <br/>
						<!-- <div style="height: 50px"></div> -->
					  </div>
					  <div class="modal-footer">
					    <h3>From i y Sardar</h3>
					  </div>
					</div>

				</div>
                <div id="shareModal" class="modal">

				  <!-- 弹窗内容 -->
					<div class="modal-content">
					  <div class="modal-header">
					    <span class="close1">&times;</span>
					    <h2>Share</h2>
					  </div>
					  <div class="modal-body" id="share_body">
					  </div>
<!--
					  <div class="modal-footer">
					    <h3>From i y Sardar</h3>
					  </div>
-->
					</div>

				</div>
                
                

				<div class="ui-page ui-content-is-empty" id="radio-page">                                       
					<div class="ui-title">Radio</div>                                                                              
					<div class="ui-content" data-ui-page="radio">
						<div id="ui-vs-container" style="">
							<div id="ui-vs-wrapper">
								<table class="ui-table">
										<tbody>
									
										</tbody>
								</table>
							</div>
						</div>
					</div>                                       
					<div class="ui-content-empty">No streams have been added</div>                                    
				</div>

				<div class="ui-page ui-content-is-empty" id="playing-page">                                       
					<div class="ui-title">Now playing</div>                                                                             
					<div class="ui-content" data-ui-page="nowplaying">
						<div class="ui-layer" data-ui-layer-type="nowplaying">            
							<div class="ui-layer-header">                
								<div class="ui-layer-image ui-default" >
									<i class="fas fa-headphones-alt"></i>
								</div>                
								<div class="ui-layer-bg">
									
								</div>                
								<div class="ui-layer-bg-brightness"></div>                
								<!-- <div class="ui-layer-info">                   
									<div class="ui-layer-name ui-text-overflow-ellipsis"></div>                   
									<div class="ui-layer-sec-name ui-text-overflow-ellipsis"></div>                   
									<div class="ui-layer-other ui-text-overflow-ellipsis"></div>                   
									<div class="ui-layer-actions">
										<div class="ui-layer-actions-btn" data-ui-id="play">
											<i class="ui-icon-play"></i>
											<span>Play All</span>
										</div>
									</div>                
								</div>   -->           
							</div>            
							<div class="ui-layer-content ui-clearfix">                
								<div class="ui-layer-content-left ui-content-row">
									<div id="ui-vs-container" style="">
										<div id="ui-vs-wrapper">
											<table class="ui-table">
												<tbody></tbody>
											</table>
										</div>
									</div>
								</div>                
								<div class="ui-layer-content-right ui-content-row">
									
								</div>                
								<div class="ui-layer-content-bottom ui-content-row"></div>            
							</div>        
						</div>
					</div>                                       

					<div class="ui-content-empty" id="nowplaying_info_text">
						This is where you will see the songs you're playing and songs that are coming up.
					</div>                                    
				</div>


				<div class="ui-page ui-content-is-empty" id="liked-page">                                       
					<div class="ui-title">Liked</div> 

					<div>
						<table style="border-style: none;">
							<thead><td>No</td><td>SongName</td><td>Artist</td><td>Album</td></thead>
							<tbody id="likedtable">
								<td id="likedinfo" style="color: red;">Your liked songs will show up here</td> 
							</tbody>
						</table>
					</div>                                                                             
					<!-- <div class="ui-content" data-ui-page="liked">
						<div id="ui-vs-container" style="">
							<div id="ui-vs-wrapper">
								<table class="ui-table"><tbody></tbody></table>
							</div>
						</div>
					</div>                                       
					<div class="ui-content-empty">Your liked songs will show up here</div> -->                                    
				</div>

				<div class="ui-page ui-content-is-empty" id="playlists-page">                                       
					<div class="ui-title">Playlists</div>                                       
					<!-- <div class="ui-page-more">              
						<div class="ui-page-filter">
							<div class="ui-filter-btn" data-ui-id="newplaylist">
								<i class="fas fa-plus"></i>
								<span>New Playlist</span>
							</div>
						</div>              
					</div>                                       
					<div class="ui-content" data-ui-page="playlists"></div>                                       
					<div class="ui-content-empty">No playlists have been added</div>     -->
					<div>
						<table style="border-style: none;">
							<thead><td>No</td><td>SongName</td><td>Artist</td><td>Album</td><td>Del</td></thead>
							<tbody id="playlisttable">
								<?php  echo $playlistinfostring; ?> 
								<!-- <td id="playlistinfo" >No playlists have been added</td>  -->
							</tbody>
						</table>
					</div>                                 
				</div>


				<div class="ui-page ui-content-is-empty" id="recent-page">                                       
					<div class="ui-title">Recent plays</div>
					<div class="ui-content" data-ui-page="recentplays">
						<div id="ui-vs-container" style="">
							<div id="ui-vs-wrapper">
								<table class="ui-table ui-td-album-hide ui-td-artist-hide ui-td-genre-hide ui-td-released-hide">
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>                                       
					
					<div>
						<table style="border-style: none;">
							<thead><td>No</td><td>SongName</td><td>Artist</td><td>Album</td></thead>
							<tbody id="recenttable"><td id="recentinfo" style="color: red;">No recent plays</td> </tbody>
						</table>
					</div>                                   
				</div>


				<div class="ui-page ui-content-is-empty" data-ui-page="search">                               
					<div class="ui-title">Search results</div>                               
					<div class="ui-content" data-ui-page="search"></div>                               
					<div class="ui-content-empty">No results</div>                               
					<div class="ui-content-loading"><i class="ui-icon-sync"></i></div>                            
				</div>


				<div class="ui-page ui-content-is-empty" id="setting-page">                               
					<div class="ui-title">Settings</div>                               
					<div class="ui-content" data-ui-page="settings">
						<div class="ui-content-row">                            
							<!-- <div class="ui-layer-content-h2">Import</div>                            
							<div class="ui-clearfix">                                
								<div class="ui-input-file-home">                                   
									<input id="ui-input-file-folder" data-ui-id="folder" class="ui-input-file" type="file" accept="audio/*" webkitdirectory="" mozdirectory="" msdirectory="" odirectory="" directory="" multiple="">                                   
									<label for="ui-input-file-folder" class="ui-btn-with-icon">
										<i class="fas fa-folder-open"></i> 
										Folder
									</label>                                
								</div>                                
								<div class="ui-input-file-home">                                   
									<input id="ui-input-file-files" data-ui-id="files" class="ui-input-file" type="file" accept="audio/*" multiple="">                                   
									<label for="ui-input-file-files" class="ui-btn-with-icon">
										<i class="fas fa-file"></i> 
										Files
									</label>                                
								</div> 
								<div class="ui-input-file-home">                                   
									<input id="ui-input-file-json" data-ui-id="json" class="ui-input-file" type="file" accept="application/json">                                   
									<label for="ui-input-file-json" class="ui-btn-with-icon">
										<i class="fas fa-folder-open"></i> 
										JSON
									</label>                                
								</div>                          
							</div>   -->                         
						</div>                         
						<!-- <div class="ui-content-row">                            
							<div class="ui-layer-content-h2">Export</div>                            
							<div class="ui-clearfix">                                
								<div class="ui-btn-with-icon" id="ui-export-btn">
									<i class="fas fa-folder-open"></i> 
									Folder
								</div>                            
							</div>                         
						</div>    -->                      
						<div class="ui-content-row">                            
							<div class="ui-layer-content-h2">Version</div>                            
							<div class="ui-clearfix">1.0.0 - Supercharged HTML5 Music Player</div>                         
						</div>
					</div>                            
				</div>
			</div>
		</div>
	</div>
	


		



	<div  class="downpart" style="height: 10%; display:flex; flex-wrap: wrap;">
		
		
		<div id="songTitle" style=" order: 1; width: 100%; ">your song is</div>
		<div class="player-control" style=" order: 2; flex-grow: 3;">
			<div id="previous-button" title="Previous" onclick="previousSong();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fa fa-fast-backward"></i>
			</div>
			<div id="backward" title="Backward" onclick="decreasePlaybackRate();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fas fa-backward"></i>
			</div>
			<div id="play-button" title="Play" onclick="playSong()" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fa fa-play"></i>
			</div>
			<div id="pause-button" title="Pause" onclick="pauseSong();"onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fa fa-pause" ></i>
			</div>
			<div id="stop-button" title="Stop" onclick="stopSong();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fa fa-stop" ></i>
			</div>
			<div id="forward" title="Forward" onclick="increasePlaybackRate();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fas fa-forward"></i>
			</div>
			<div id="next-button" title="Next" onclick="nextSong();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fa fa-fast-forward" ></i>
			</div>
			<div id="volumedown" title="VolumeDown" onclick="volumeDown();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fas fa-volume-down"></i>
			</div>
			<input id="volumeSlider" type="range" min="0" max="1" value="1" step="0.05" onchange="adjustVolume();" style="width: 90px"/>
			<div id="volumeup" title="VolumeUp" onclick="volumeUp();" onmouseover="mOver(this);" onmouseout="mOut(this);">
				<i class="fas fa-volume-up"></i>
			</div>
			<!-- <div id="shuffle-button" title="Shuffle Off" onclick="shuffle();"><i class="fa fa-random" ></i></div> -->
			<!-- <div id="repeat-button" title="Repeat Off" onclick="repeat();"><i class="fa fa-refresh" ><span>1</span></i></div> -->
		</div>
		
				
		<div style="order: 3; flex-grow: 5; display: flex; margin-left: 50px">
					
			<div id="currentTime" class="currettime" style=" order: 1; width: 50px ">00:00</div>
			<input id="songSlider" class="songslider" type="range" min="0" step="1" onchange="seekSong();"
			 style="order: 2; flex-grow: 10" value="0" />					
			<div id="duration" class="duration" style="order: 3; width: 50px">00:00</div>
					
		</div>
		
	</div>
 <p id="demo"></p>
 <p id="demo1"></p>

<!-- <div class="ui-content-class">
	<div style="background-image:'url(image/album/noalbum.png)'; width: 300px; height: 300px; display: block;">

	</div>
	<div class="albun_title">Adele</div>
	
</div> -->


<!-- 
<script type="text/javascript" src="assets/js/myjs.js"></script> -->
<script type="text/javascript" src="assets/js/menu.js"></script>
<script type="text/javascript">
	// var songs=["Anything for Love.mp3",
	// 			"Avalanche (feat. Demi Lovato).mp3",
	// 			"Bad.mp3",
	// 			"Can You Hear Me.mp3",
	// 			"Ghost Of Love.mp3",
	// 			"Summer.mp3",
	// 			"Under Control.mp3",
	// 			"Unkiss Me.mp3",
	// 			"We Found Love (feat. Rihanna).mp3",
	// 			"Yours.mp3"];
	var songs = [<?php echo implode($songlistarray, ","); ?>];
	var song=new Audio;
	var currentSong=0;currentSongName='';
	var currentSongTitle=document.getElementById("songTitle");
	var songSlider=document.getElementById("songSlider");
	var currentTime=document.getElementById("currentTime");
	var duration=document.getElementById("duration");
	var volumeSlider=document.getElementById("volumeSlider");
	var menustyle=document.getElementById("ui-menu");
	var expanded=true;
	var songsall =<?php echo json_encode($songlistall); ?>;
	var lyrics=<?php echo json_encode($lyrics); ?>;
	var total=<?php echo $files_total;?>;
	var flag_log=<?php echo json_encode($flag_log);?>;
	var dropdownid=0;
	var lastid=0;
	var startflag=0;
	var albumsinfo=<?php echo json_encode($albuminfo)?>;
	var artistinfo=<?php echo json_encode($artistinfo)?>;
	var likedid=1; recentid=1; //show the number of song in liked tag or recent tag
	var showplaylistsflag=0;
	

	/////////////////////////////////////////////////////////////

	var songstab=document.getElementById("songs-tab");
	var albumstab=document.getElementById("albums-tab");
	var artiststab=document.getElementById("aritsts-tab");
	var songstag=document.getElementById("songs-tag");
	var albumstag=document.getElementById("albums-tag");
	var artiststag=document.getElementById("artists-tag");
	
	//at first no playlist ,so show this message
	// var playlistinfostring=''; playlistid=1;	playlistsongs=[];
	// '<td id="playlistinfo" >No playlists have been added</td>';
	
	// if (typeof(Storage) !== "undefined") {
	// 	if (sessionStorage.getItem("playlistinfosession")!=null) {
	// 		playlistinfostring = sessionStorage.getItem("playlistinfosession");// use to show playlist with memory
	// 	}
		
	// 	if (sessionStorage.getItem("playlistsongs")!=null) {
	// 		// document.getElementById("demo1").innerHTML=sessionStorage.getItem("playlistsongs");
	// 		playlistsongs = sessionStorage.getItem("playlistsongs").split(",");//use to nextSong, previous Song

	// 	}
		
	// 	// playlistsongs=sessionStorage.getItem("playlistsongs");
	// 	if (sessionStorage.getItem("playlistinfocurrentid")!=null) {
	// 	    playlistid=sessionStorage.getItem("playlistinfocurrentid");
	// 	}else{
	// 	    playlistid=1;
	// 	}
	// } 
	// sessionStorage.setItem("playlistsongs","")
	// sessionStorage.setItem("playlistinfosession","")
	// sessionStorage.setItem("playlistinfocurrentid","")


	window.onload=loadSong();
////////////////////////////////////载入歌曲
	function loadSong() {
		song.src="uploads/"+songs[currentSong];
		song.volume=volumeSlider.value;
		song.playbackRate=1;
		currentSongTitle.textContent=(currentSong+1)+"."+songs[currentSong];
		setTimeout(showDuration,1000);
	}
	function playSong() {
		song.playbackRate=1;
		var playPromise=song.play();
		 if (playPromise !== undefined) {
		    playPromise.then(_ => {
		      // Automatic playback started!
		      // Show playing UI.
		      // We can now safely pause video...
		      
		    })
		    .catch(error => {
		      // Auto-play was prevented
		      // Show paused UI.
		    });
		  }else{
		  	song.play();
		  }
		currentSongName=songs[currentSong];
		// document.getElementById("demo").innerHTML=currentSongName;
		showtextblue();
		changeShowNowPlaying();
		addRecent();
	}


	function playSongwithId(songid) {
		song.src="uploads/"+songs[songid-1];
		song.playbackRate=1;
		currentSongTitle.textContent=(songid)+"."+songs[songid-1];
		currentSong=songid-1;
		setTimeout(showDuration,1000);
		playSong();
		showtextblue(songid);
		
	}
	function showtextblue() {
		for (var i = 1; i <=total; i++) {
			document.getElementById("tr"+i).style.color="#555555";
		}
		if (songs.indexOf(currentSongName)>=0) {
			var id= songs.indexOf(currentSongName)+1;
			document.getElementById("tr"+id).style.color="blue";
		}
		
	}

	function pauseSong() {
		song.pause();
	}
	function previousSong() {
		currentSong--;
		if (currentSong<0) {currentSong=songs.length-1;}
		loadSong();
		playSong();		
	}
	function nextSong() {
		currentSong=(currentSong+1) % songs.length;
		loadSong();
		playSong();
	}
	function stopSong() {
		song.src="uploads/"+songs[currentSong];
	}
	function seekSong() {
		song.currentTime=songSlider.value;
		currentTime.textContent=convertTime(song.currentTime);
	}

	setInterval(updateSongSlider,1000);
	function updateSongSlider() {
		var c=Math.round(song.currentTime);
		songSlider.value=c;
		currentTime.textContent=convertTime(c);
		if (song.ended) {
			nextSong();
		}
	}
	function convertTime(secs) {
		var min=Math.floor(secs/60);
		var sec=secs%60;
		min=(min<10)? "0"+min:min;
		sec=(sec<10)? "0"+sec:sec;
		return (min+":"+sec);
	}
	function showDuration() {
		var d=Math.floor(song.duration);
		songSlider.setAttribute("max", d);
		duration.textContent=convertTime(d);
		// document.getElementById("demo").innerHTML =convertTime(d);
	}
	function adjustVolume() {
		song.volume=volumeSlider.value;
		
	}
	function volumeUp() {
		
		
			volumeSlider.value=1;
		// }

		// if (volumeSlider.value>1) {volumeSlider.value=1;}
		song.volume =volumeSlider.value;
		// 	document.getElementById("demo").innerHTML = volumeSlider.value;
		
	}
	function volumeDown() {
		volumeSlider.value -=0.05;
		if (volumeSlider.value<0) {volumeSlider.value=0;}
		song.volume =volumeSlider.value;
		document.getElementById("Listen").style.visibility='hidden';
		//backgroundImage="url('image/uploads/image.png')";
	}

	function decreasePlaybackRate() {
		song.playbackRate -= 0.5;
	}
	function increasePlaybackRate() {
		song.playbackRate += 0.5;
	}
////////////////////////////music control end////////////////////////////////////////////
	

	function mOver(obj){
		obj.style.color='blue';
		// menustyle.style.width='300px';
	}
	function mOut(obj){
		obj.style.color='#555555';
	}

	function showimg(Obj) {
			Obj.style.webkitfilter='blur(0px)';
			Obj.style.filter='blur(0px)';
	}

	function hiddenimg(Obj) {
			Obj.style.webkitfilter='blur(3px)';
			Obj.style.filter='blur(3px)';
	}


	function changeShowNowPlaying() {//当你点击的是playlist时，artist和album有错误
		document.getElementById("nowplaying_info_text").innerHTML= "<pre><div>The song played now is:  "+songs[currentSong]+"</div><div>Album is:                 "+songsall[currentSong+1]['album']+"</div><div>Artist is:                "+songsall[currentSong+1]['artist']+"</div></pre>";
	}

	function addRecent() {
		document.getElementById("recentinfo").style.display='none';
		var recentinfo=document.getElementById("recenttable");
		var recentsongid=currentSong+1;
		recentinfo.innerHTML=recentinfo.innerHTML+'<tr onmouseover="mOver(this)" onmouseout="mOut(this)" onclick="playSongwithId('
													+recentsongid+')"><td>'+recentid+'</td><td>'+
													songsall[currentSong+1]['filename']+'</td><td>'+
													songsall[currentSong+1]['artist']+'</td><td>'+
													songsall[currentSong+1]['album']+"</td></tr>";
		recentid++;
	}
	function addLikewithId() {
		document.getElementById("likedinfo").style.display='none';
		var likedinfo=document.getElementById("likedtable");
		likedinfo.innerHTML=likedinfo.innerHTML+'<tr onmouseover="mOver(this)" onmouseout="mOut(this)" onclick="playSongwithId('
													+dropdownid+')"><td>'+likedid+'</td><td>'+
													songsall[dropdownid]['filename']+'</td><td>'+
													songsall[dropdownid]['artist']+'</td><td>'+
													songsall[dropdownid]['album']+"</td></tr>";
		likedid++;
	}


	function showPlaylists() {
		// document.getElementById("playlisttable").innerHTML = playlistinfostring;
		document.getElementById("music-page").className="ui-page ui-content-is-empty";
		document.getElementById("radio-page").className="ui-page ui-content-is-empty";
		document.getElementById("playing-page").className="ui-page ui-content-is-empty";
		document.getElementById("liked-page").className="ui-page ui-content-is-empty";
		document.getElementById("playlists-page").className="ui-page";
		document.getElementById("recent-page").className="ui-page ui-content-is-empty";
		document.getElementById("setting-page").className="ui-page ui-content-is-empty";
		showplaylistsflag=1;
		songs = [<?php echo implode($playlist_songlistarray, ","); ?>];
		
	}
	function showMusic() {
		document.getElementById("music-page").className="ui-page";
		document.getElementById("radio-page").className="ui-page ui-content-is-empty";
		document.getElementById("playing-page").className="ui-page ui-content-is-empty";
		document.getElementById("liked-page").className="ui-page ui-content-is-empty";
		document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
		document.getElementById("recent-page").className="ui-page ui-content-is-empty";
		document.getElementById("setting-page").className="ui-page ui-content-is-empty";
		showplaylistsflag=0;
		songs = [<?php echo implode($songlistarray, ","); ?>];
		showtextblue();
	}
	
function showRadio() {
	document.getElementById("music-page").className="ui-page ui-content-is-empty";
	document.getElementById("radio-page").className="ui-page";
	document.getElementById("playing-page").className="ui-page ui-content-is-empty";
	document.getElementById("liked-page").className="ui-page ui-content-is-empty";
	document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
	document.getElementById("recent-page").className="ui-page ui-content-is-empty";
	document.getElementById("setting-page").className="ui-page ui-content-is-empty";
}
function showPlaying() {
	document.getElementById("music-page").className="ui-page ui-content-is-empty";
	document.getElementById("radio-page").className="ui-page ui-content-is-empty";
	document.getElementById("playing-page").className="ui-page";
	document.getElementById("liked-page").className="ui-page ui-content-is-empty";
	document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
	document.getElementById("recent-page").className="ui-page ui-content-is-empty";
	document.getElementById("setting-page").className="ui-page ui-content-is-empty";
}
function showLiked() {
	document.getElementById("music-page").className="ui-page ui-content-is-empty";
	document.getElementById("radio-page").className="ui-page ui-content-is-empty";
	document.getElementById("playing-page").className="ui-page ui-content-is-empty";
	document.getElementById("liked-page").className="ui-page";
	document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
	document.getElementById("recent-page").className="ui-page ui-content-is-empty";
	document.getElementById("setting-page").className="ui-page ui-content-is-empty";
// 	songs = [<?php echo implode($songlistarray, ","); ?>];
	showplaylistsflag=0;
}

function showRicent() {
	document.getElementById("music-page").className="ui-page ui-content-is-empty";
	document.getElementById("radio-page").className="ui-page ui-content-is-empty";
	document.getElementById("playing-page").className="ui-page ui-content-is-empty";
	document.getElementById("liked-page").className="ui-page ui-content-is-empty";
	document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
	document.getElementById("recent-page").className="ui-page";
	document.getElementById("setting-page").className="ui-page ui-content-is-empty";
// 	songs = [<?php echo implode($songlistarray, ","); ?>];
	showplaylistsflag=0;
}
function showSettings() {
	document.getElementById("music-page").className="ui-page ui-content-is-empty";
	document.getElementById("radio-page").className="ui-page ui-content-is-empty";
	document.getElementById("playing-page").className="ui-page ui-content-is-empty";
	document.getElementById("liked-page").className="ui-page ui-content-is-empty";
	document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
	document.getElementById("recent-page").className="ui-page ui-content-is-empty";
	document.getElementById("setting-page").className="ui-page";
}
function changemenu(){
		if (expanded==true) {
			document.getElementById("ui-menu").style.width='20%';
			document.getElementById("ui-content").style.width='80%';
			document.getElementsByTagName("span")[0].style.visibility="visible";
			document.getElementsByTagName("span")[1].style.visibility="visible";
			document.getElementsByTagName("span")[2].style.visibility="visible";
			document.getElementsByTagName("span")[3].style.visibility="visible";
			document.getElementsByTagName("span")[4].style.visibility="visible";
			document.getElementsByTagName("span")[5].style.visibility="visible";
			document.getElementsByTagName("span")[6].style.visibility="visible";
			document.getElementById("ui-search").style.display="inline-block";
			document.getElementById("search-icon").style.display="none";
			expanded=false;

		}else{
			document.getElementById("ui-menu").style.width='10%';
			document.getElementById("ui-content").style.width='90%';
			document.getElementsByTagName("span")[0].style.visibility="hidden";
			document.getElementsByTagName("span")[1].style.visibility="hidden";
			document.getElementsByTagName("span")[2].style.visibility="hidden";
			document.getElementsByTagName("span")[3].style.visibility="hidden";
			document.getElementsByTagName("span")[4].style.visibility="hidden";
			document.getElementsByTagName("span")[5].style.visibility="hidden";
			document.getElementsByTagName("span")[6].style.visibility="hidden";
			document.getElementById("ui-search").style.display="none";
			document.getElementById("search-icon").style.display="inline-block";
			expanded=true;
		}
		
	}




	
	function showSongs() {
		songstab.className="ui-content-music-tab-content active";
		albumstab.className="ui-content-music-tab-content";
		artiststab.className="ui-content-music-tab-content";
		songstag.className="ui-content-music-tab-btn active";
		albumstag.className="ui-content-music-tab-btn";
		artiststag.className="ui-content-music-tab-btn";

	}
	function showAlbums() {
		songstab.className="ui-content-music-tab-content";
		albumstab.className="ui-content-music-tab-content active";
		artiststab.className="ui-content-music-tab-content";
		songstag.className="ui-content-music-tab-btn";
		albumstag.className="ui-content-music-tab-btn active";
		artiststag.className="ui-content-music-tab-btn";
		albumstab.innerHTML=albumsinfo;
	}

	//When click the albums image
	function showAlbumsWithId(album, backgroundimage, albummessage){
		// var albumsinfo=document.getElementById("albums-tab");
		// var albumname=json_encode(album);
		albumstab.innerHTML=
		'<div id="albums-tab-withid" style="display: flex; flex-direction: column; margin: 50px">'+
			'<div style="display: flex; height: 200px; flex-direction: row;">'+		
				'<div  class="ui-obj-image"	style="background-image:'+backgroundimage+'; ">'+					
				'</div>'+				
				'<div style="font-size: 30px; font-family: Tahoma, Geneva, sans-serif; margin:50px;">'+album+
					'<div style="font-size: 20px; font-family: Tahoma, Geneva, sans-serif; margin:10px; ">'+albummessage+
					'</div>'+
				'</div>'+
				
			'</div>'+
		'</div><table style="border:none"><thead><td>No</td><td>SongName</td><td>Artist</td><td>Album</td><td>Size</td><td>Genre</td></thead><tbody id="albumtablebody"></tbody></table>';

		var id=1;
		var tempid=1;
		var songinfo='';
		var albumtablebody=document.getElementById("albumtablebody");
		// albumtablebody.innerHTML=albumtablebody.innerHTML+'<tr><td>No</td><td>SongName</td><td>Artist</td><td>Album</td></tr>';
		while(songsall[id]!=null){
			songinfo=songsall[id].filename+songsall[id]['album']+songsall[id]['artist'];

			if (songinfo.indexOf(album)!=-1) {		
			// albumtablebody.innerHTML=albumtablebody.innerHTML+'<tr><td>No</td><td>SongName</td><td>Artist</td><td>Album</td></tr>'						
			albumtablebody.innerHTML=albumtablebody.innerHTML+'<tr onmouseover="mOver(this)" onmouseout="mOut(this)" onclick="playSongwithId('+id+')"><td>'+tempid+
											'</td><td>'+songsall[id]['filename']+
											'</td><td>'+songsall[id]['artist']+
											'</td><td>'+songsall[id]['album']+
											'</td><td>'+songsall[id]['size']+
											'</td><td>'+songsall[id]['genre']+'</td></tr>';
			tempid++
			}	
			id++;
		}
	}

	//when click the artist tags		
	function showArtists() {
		songstab.className="ui-content-music-tab-content";
		albumstab.className="ui-content-music-tab-content";
		artiststab.className="ui-content-music-tab-content active";
		songstag.className="ui-content-music-tab-btn";
		albumstag.className="ui-content-music-tab-btn";
		artiststag.className="ui-content-music-tab-btn active";
		artiststab.innerHTML=artistinfo;
	}

	//When click the artist's image
	function showArtistWithId(artist, backgroundimage, artistmessage){
		artiststab.innerHTML=
		'<div id="albums-tab-withid" style="display: flex; flex-direction: column; margin: 50px">'+
			'<div style="display: flex; height: 200px; flex-direction: row;">'+		
				'<div  class="ui-obj-image"	style="background-image:'+backgroundimage+'; ">'+					
				'</div>'+				
				'<div style="font-size: 30px; font-family: Tahoma, Geneva, sans-serif; margin:50px; ">'+artist+
					'<div style="font-size: 20px; font-family: Tahoma, Geneva, sans-serif; margin:10px; ">'+artistmessage+
					'</div>'+
				'</div>'+
			'</div>'+
		'</div><table style="border:none"><thead><td>No</td><td>SongName</td><td>Artist</td><td>Album</td><td>Size</td><td>Genre</td></thead><tbody id="artisttablebody"></tbody></table>';


		var id=1;
		var tempid=1;
		var songinfo='';
		var artisttablebody=document.getElementById("artisttablebody");

		while(songsall[id]!=null){
			// document.getElementById("demo").innerHTML+="///"+songsall[id]['filename']+"***"+songsall[id].length;
			songinfo=songsall[id].filename+songsall[id]['album']+songsall[id]['artist'];

			if (songinfo.indexOf(artist)!=-1) {							
			artisttablebody.innerHTML=artisttablebody.innerHTML+'<tr onmouseover="mOver(this)" onmouseout="mOut(this)" onclick="playSongwithId('+id+')"><td>'+tempid+
											'</td><td >'+songsall[id]['filename']+
											'</td><td>'+songsall[id]['artist']+
											'</td><td>'+songsall[id]['album']+
											'</td><td>'+songsall[id]['size']+
											'</td><td>'+songsall[id]['genre']+'</td></tr>';
			tempid++
			}	
			id++;
		}
	}

	function changemenu(){
		if (expanded==true) {
			document.getElementById("ui-menu").style.width='20%';
			document.getElementById("ui-content").style.width='80%';
			document.getElementsByTagName("span")[0].style.visibility="visible";
			document.getElementsByTagName("span")[1].style.visibility="visible";
			document.getElementsByTagName("span")[2].style.visibility="visible";
			document.getElementsByTagName("span")[3].style.visibility="visible";
			document.getElementsByTagName("span")[4].style.visibility="visible";
			document.getElementsByTagName("span")[5].style.visibility="visible";
			document.getElementsByTagName("span")[6].style.visibility="visible";
			document.getElementById("ui-search").style.display="inline-block";
			document.getElementById("search-icon").style.display="none";
			expanded=false;

		}else{
			document.getElementById("ui-menu").style.width='10%';
			document.getElementById("ui-content").style.width='90%';
			document.getElementsByTagName("span")[0].style.visibility="hidden";
			document.getElementsByTagName("span")[1].style.visibility="hidden";
			document.getElementsByTagName("span")[2].style.visibility="hidden";
			document.getElementsByTagName("span")[3].style.visibility="hidden";
			document.getElementsByTagName("span")[4].style.visibility="hidden";
			document.getElementsByTagName("span")[5].style.visibility="hidden";
			document.getElementsByTagName("span")[6].style.visibility="hidden";
			document.getElementById("ui-search").style.display="none";
			document.getElementById("search-icon").style.display="inline-block";
			expanded=true;
		}
		
	}

		// document.getElementById("demo").innerHTML=song_lyrics_text;
			var modal = document.getElementById('myModal');

		// 获取 <span> 元素，用于关闭弹窗 that closes the modal
		var span = document.getElementsByClassName("close")[0];
// 点击 <span> (x), 关闭弹窗
		span.onclick = function() {
		    modal.style.display = "none";
		}


	function showLyricswithId() {
		modal.style.display = "block";
		var id=1;
		var song_lyrics_text="There is no song lyrics";
		document.getElementById("song_lyrics_text_p").innerHTML=song_lyrics_text+songsall[dropdownid]['filename'];
		while(lyrics[id]['song_name']!=''){
			if(songsall[dropdownid]['filename']==lyrics[id]['song_name']){
				song_lyrics_text=lyrics[id]['song_lyrics'];
				document.getElementById("song_lyrics_text_p").innerHTML=song_lyrics_text;
			}
			id++;
		}	
	}
    

        var share_modal = document.getElementById('shareModal');

		// 获取 <span> 元素，用于关闭弹窗 that closes the modal
		var span1 = document.getElementsByClassName("close1")[0];
// 点击 <span> (x), 关闭弹窗
		span1.onclick = function() {
		    share_modal.style.display = "none";
		}
    function share() {
		share_modal.style.display = "block";
        var share_text='<br/><br/><div id="share-buttons">'+
            
            '<!-- Buffer --><a href="https://bufferapp.com/add?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3&amp;text=Simple Share Buttons" target="_blank"><img src="https://simplesharebuttons.com/images/somacro/buffer.png" alt="Buffer" /></a>'+
            
            '<!-- Digg --><a href="http://www.digg.com/submit?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3" target="_blank"><img src="https://simplesharebuttons.com/images/somacro/diggit.png" alt="Digg" /></a>'+
            
            '<!-- Email --><a href="mailto:?Subject=Simple Share Buttons&amp;Body=I%20saw%20this%20and%20thought%20of%20you!%20 http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3"><img src="https://simplesharebuttons.com/images/somacro/email.png" alt="Email" /></a>'+
            
            '<!-- Facebook --><a href="http://www.facebook.com/sharer.php?u=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3" target="_blank"><img src="https://simplesharebuttons.com/images/somacro/facebook.png" alt="Facebook" /></a>'+
            
            '<!-- Google+ --><a href="https://plus.google.com/share?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3"><img src="https://simplesharebuttons.com/images/somacro/google.png" alt="Google" /> </a>'+
            
            '<!-- LinkedIn --><a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3" target="_blank">        <img src="https://simplesharebuttons.com/images/somacro/linkedin.png" alt="LinkedIn" /> </a>'+
            
//            '<!-- Pinterest --><a href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());">
//        <img src="https://simplesharebuttons.com/images/somacro/pinterest.png" alt="Pinterest" />
//    </a>'
//   
 
            '<!-- Print --><a href="javascript:;" onclick="window.print()">  <img src="https://simplesharebuttons.com/images/somacro/print.png" alt="Print" /> </a>'+
            
            '<!-- Reddit --> <a href="http://reddit.com/submit?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3&amp;title=Simple Share Buttons" target="_blank">      <img src="https://simplesharebuttons.com/images/somacro/reddit.png" alt="Reddit" /> </a>'+
            
            '<!-- StumbleUpon--> <a href="http://www.stumbleupon.com/submit?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3&amp;title=Simple Share Buttons" target="_blank">    <img src="https://simplesharebuttons.com/images/somacro/stumbleupon.png" alt="StumbleUpon" />  </a>'+
            
            '<!-- Tumblr--> <a href="http://www.tumblr.com/share/link?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3&amp;title=Simple Share Buttons" target="_blank">   <img src="https://simplesharebuttons.com/images/somacro/tumblr.png" alt="Tumblr" />  </a>'+
            
            '<!-- Twitter --> <a href="https://twitter.com/share?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3&amp;text=Simple%20Share%20Buttons&amp;hashtags=simplesharebuttons" target="_blank">        <img src="https://simplesharebuttons.com/images/somacro/twitter.png" alt="Twitter" /> </a>'+
            
            '<!-- VK -->    <a href="http://vkontakte.ru/share.php?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3" target="_blank">        <img src="https://simplesharebuttons.com/images/somacro/vk.png" alt="VK" />    </a>'+
            
            '<!-- Yummly -->    <a href="http://www.yummly.com/urb/verify?url=http://clarenceli419.000webhostapp.com/MusicPlayer/MusicPlayer/uploads/'+songsall[dropdownid]['filename']+'.mp3&amp;title=Simple Share Buttons" target="_blank">        <img src="https://simplesharebuttons.com/images/somacro/yummly.png" alt="Yummly" />    </a>'+
            
            '</div><br/>';
        
            //'<br/><iframe src="https://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fclarenceli419.000webhostapp.com%2FMusicPlayer%2FMusicPlayer%2Fuploads%2F'+songsall[dropdownid]['filename']+'.mp3&width=126&layout=button_count&action=recommend&size=large&show_faces=true&share=true&height=46&appId" width="126" height="46" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe><br/>';
        document.getElementById("share_body").innerHTML=share_text;
	}


	function search() {
		var search_key=document.getElementById("ui-search").value;
		var tbl = document.getElementById("table"); // Get the table
		var id=0;
		while(id<(tbl.getElementsByTagName("tbody")).length){
			tbl.removeChild(tbl.getElementsByTagName("tbody")[id]);
		}
		
		var id=1;
		var tempid=1;
		var songinfo='';
		tbl.innerHTML=tbl.innerHTML+'<tbody>';
		while(songsall[id]['filename']!=''){
			songinfo=songsall[id]['filename']+songsall[id]['album']+songsall[id]['artist'];
			if (songinfo.indexOf(search_key)!=-1) {									
				tbl.innerHTML=tbl.innerHTML+'<tr ><td >'+tempid+
											'</td><td onmouseover="mOver(this)" onmouseout="mOut(this)" onclick="playSongwithId('+id+')">'+songsall[id]['filename']+
											'</td><td>'+songsall[id]['artist']+
											'</td><td>'+songsall[id]['album']+
											'</td><td>'+songsall[id]['other']+
											'</td><td>'+songsall[id]['genre']+
											'</td><td>'+songsall[id]['size']+'</td></tr>';
			tempid++;
			}
				id++;
		}
		tbl.innerHTML = tbl.innerHTML +'</tbody>';
		
	}



	function functionMore(id) {
	    document.getElementById("myDropdown"+id).classList.add("show");
	    dropdownid=id;
	    if (startflag==0) {
	    	lastid=dropdownid;
	    	startflag=1;
	    }
	}

	window.onclick = function(event) {
		if (event.target == modal) {
		        modal.style.display = "none";
		    }
	  if (!event.target.matches('.dropbtn') || dropdownid!=lastid) {
	  	startflag=0;
	    var dropdowns = document.getElementsByClassName("dropdown-content");
	    var i;
	    for (i = 0; i < dropdowns.length; i++) {
	      var openDropdown = dropdowns[i];
	      if (openDropdown.classList.contains('show')) {
	        openDropdown.classList.remove('show');
	      }
	    }
	    
	  }
	lastid=dropdownid

}

</script>

</body>
</html>
