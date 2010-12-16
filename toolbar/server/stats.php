<?php
 /*!
 * StudyBar
 *
 * Copyright (c) 2009. University of Southampton
 * http://access.ecs.soton.ac.uk/StudyBar/
 *
 * Licensed under the BSD Licence.
 * http://www.opensource.org/licenses/bsd-license.php
 *
 */

$loadAvg = sys_getloadavg();
$loadString = $loadAvg[0] . ", " . $loadAvg[1] . ", " . $loadAvg[2]; 

$dirSizes = str_replace("\n", "\n<br />", trim(shell_exec("du /var/www/projectsportal/htdocs/seb/StudyBar/TTS/cache/ -h")));

$numFiles = shell_exec("ls /var/www/projectsportal/htdocs/seb/StudyBar/TTS/cache/ -1 -R | wc -l");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>StudyBar TTS Backend Statistics</title>
	</head>
	<body>
	<h1>StudyBar TTS Backend Statistics</h1>
	<p>
		<b>Server Load:</b> <?php echo $loadString; ?><br />
		<h2>Cache</h2>
		<b>Cache directory sizes:</b><br /><?php echo $dirSizes; ?><br /><br />
		<b>Number of files in Cache:</b> <?php echo $numFiles; ?><br />
	</p>
	</body>
</html>
