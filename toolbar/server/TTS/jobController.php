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
error_reporting(E_ERROR | E_WARNING);
require("classes/speech.class.php");

if(@$_POST['page'] && @$_POST['data']){	

	$speech = new speech($_POST['data'], $_POST['page']);
	
	echo $speech->execute()->returnStatus();
}

?>