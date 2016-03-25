<!DOCTYPE html>
<html>
<head>
	<title>Bootstrap update</title>
	<link rel="stylesheet" type="text/css" href="./update.css">
</head>
<?php

function getcurVersion()
{
	$f=file_get_contents('https://cdn.jsdelivr.net/bootstrap/latest/css/bootstrap.css', NULL, NULL, 0, 1000);
	$matches = array();
	preg_match('/Bootstrap\s+v\d+\.\d+\.\d+/', $f, $matches);
	preg_match('/\d+\.\d+\.\d+/', $matches[0], $matches);
	return $matches[0];
}

function updateBootstrap($version)
{
	$ret="";
	$suc=true;
	$start="https://cdn.jsdelivr.net/bootstrap/";
	$js="/js/bootstrap.js";
	$css="/css/bootstrap.css";
	if(!@copy($start.$version.$css,'./css/bootstrap.css'))
	{
	    $errors= error_get_last();
	    $ret .= "COPY ERROR: ".$errors['type'];
	    $ret .= "<br />\n".$errors['message']."<br />\n";
	    $suc =false;
	} else {
	    $ret .= "CSS(".$version.") copied from remote!<br />";
	}

	if(!@copy($start.$version.$js,'./js/bootstrap.js'))
	{
	    $errors= error_get_last();
	    $ret .= "COPY ERROR: ".$errors['type'];
	    $ret .= "<br />\n".$errors['message'];
	    $suc = false;
	} else {
	    $ret .= "JS(".$version.") copied from remote!";
	}
	return array($suc,$ret);
}

?>
<body>
<div class="container">
<div class="row">

<?php

echo '<div class="col-md-6 col-md-offset-3">';

if (isset($_GET["submit"])&&$_GET["submit"]=="version") {
	list($suc,$s)=updateBootstrap($_GET["version"]);
	if($suc){
		echo '<div class="alert alert-success" role="alter">';
			print($s);
		echo '</div>';
	} else {
		echo '<div class="alert alert-danger" role="alter">';
			print($s);
		echo '</div>';
	}
}

echo "<form method=\"get\" accept-charset=\"UTF-8\">\n";
echo "Which Bootstrap version do you want?(latest version: ".getcurVersion().")<br />\n";
echo "<input type=\"text\" name=\"version\" value=\"".getcurVersion()."\"><br />\n";
echo "<button class=\"btn btn-warning btn-xs\" name=\"submit\" type=\"submit\" value=\"version\">Update</button><br />\n";
echo "</form>\n";
echo '<button onclick="window.location = \'/mh/administrate_mathhub\'" class="btn btn-primary btn-xs">BACK(admin)!</button>';
echo "</div>";

?>

</div>
</div>

</body>
</html>