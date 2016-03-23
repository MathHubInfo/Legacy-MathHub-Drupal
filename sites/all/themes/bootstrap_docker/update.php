<!DOCTYPE html>
<html>
<head>
	<title>Bootstrap update</title>
</head>
<body>

<?php

$start="https://cdn.jsdelivr.net/bootstrap/";
$js="/js/bootstrap.js";
$css="/css/bootstrap.css";

if (isset($_GET["version"])) {

	if(!@copy($start.$_GET["version"].$css,'./css/bootstrap.css'))
	{
	    $errors= error_get_last();
	    echo "COPY ERROR: ".$errors['type'];
	    echo "<br />\n".$errors['message']."<br />\n";
	} else {
	    echo "CSS copied from remote!<br />";
	}

	if(!@copy($start.$_GET["version"].$js,'./js/bootstrap.js'))
	{
	    $errors= error_get_last();
	    echo "COPY ERROR: ".$errors['type'];
	    echo "<br />\n".$errors['message']."<br />\n";
	} else {
	    echo "JS copied from remote!<br />";
	}

} else {
	echo "<form method=\"get\" accept-charset=\"UTF-8\">\n";
	echo "Which Bootstrap version do you want?(e.g. 3.3.5):<br />\n";
	echo "<input type=\"text\" name=\"version\"><br />\n";
	echo "<input type=\"submit\" value=\"Submit\"><br />\n";
	echo "</form>\n";
}

?>

</body>
</html>