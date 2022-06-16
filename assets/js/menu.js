
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
}

function showRicent() {
	document.getElementById("music-page").className="ui-page ui-content-is-empty";
	document.getElementById("radio-page").className="ui-page ui-content-is-empty";
	document.getElementById("playing-page").className="ui-page ui-content-is-empty";
	document.getElementById("liked-page").className="ui-page ui-content-is-empty";
	document.getElementById("playlists-page").className="ui-page ui-content-is-empty";
	document.getElementById("recent-page").className="ui-page";
	document.getElementById("setting-page").className="ui-page ui-content-is-empty";
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
