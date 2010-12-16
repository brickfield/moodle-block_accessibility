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
error_reporting(0);
require_once('../../../../../config.php');
$spellURI = $CFG->wwwroot.'/spell/spellcheck.php?';
$dictURI = "http://en.wiktionary.org/w/api.php?";
$ttsURI = $CFG->wwwroot.'/TTS/chunkCoordinator.php?';
//$updateURI = "http://access.ecs.soton.ac.uk/StudyBar/update.php";
ini_set('user_agent', 'hello');
switch($_GET['rt']){

	case "spell":
		$vars = $_GET;
		
		unset($vars['rt'], $vars['id']);
		$remData = file_get_contents( $spellURI . http_build_query($vars, null, '&') );
		$ro['data'] = $remData;
		echo "var CSresponseObject = " . json_encode($ro) . ";";	
	break;
	
	case "dict":
		$vars = $_GET;
		unset($vars['rt'], $vars['id']);
		$remData = file_get_contents( $dictURI . http_build_query($vars, null, '&') );
		$ro['data'] = $remData;
		echo "var CSresponseObject = " . json_encode($ro) . ";";
	break;
	
	case "tts":
	
		$vars = $_GET;

		list($chunkTotal, $currentChunk) = explode('-', $_GET['chunkData']);

		unset($vars['rt'], $vars['chunkData'], $vars['page']);
		
		file_put_contents( "../TTS/cache/chunks/" . $vars['id'] . ".txt", $vars['data'], FILE_APPEND);

		if($currentChunk == $chunkTotal){
			require("../TTS/classes/speech.class.php");
			
			//file_put_contents("../TTS/cache/chunks/tmpOutput.txt", base64_decode( str_replace(array("-", "_"), array("/", "+"), file_get_contents("../TTS/cache/chunks/" . $vars['id'] . ".txt") ) ) );
			
			$speech = new speech( file_get_contents("../TTS/cache/chunks/" . $vars['id'] . ".txt"), $_GET['page']);
			echo "var CSresponseObject = " . $speech->execute()->returnStatus() . ";";
			
		} else {
			$ro['data'] = array('message' => "ChunkSaved", "debugID" => $currentChunk . "-" . $chunkTotal);
			echo "var CSresponseObject = " . json_encode($ro) . ";";		
		}

	break;
	
//	case "update":
//		if(!$_GET['ch']) $_GET['ch'] = "beta";
//		$remData = file_get_contents( $updateURI . "?b=" . $_GET['b'] . "&ch=" . $_GET['ch'] );
//		if(@$_GET['callback']) {
//			echo $_GET['callback'] . "(" . $remData . ")";
//		} else {
//			echo $remData;
//		}
//	break;
	
	default:
		$ro['data'] = "test dataset " . rand(0, 999);		
		echo "var CSresponseObject = " . json_encode($ro) . ";";	
	break;


}



?>