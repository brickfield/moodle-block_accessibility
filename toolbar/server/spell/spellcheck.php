<?php
error_reporting(E_ERROR || E_WARNING);
header("Content-Type: text/xml; charset=utf-8");

$url = "https://www.google.com/tbproxy/spell?lang=en";
$text = urldecode($_GET['text']);

$body = '<?xml version="1.0" encoding="utf-8" ?>';
$body .= '<spellrequest textalreadyclipped="0" ignoredups="1" ignoredigits="' . $_GET['ignoredigits'] . '" ignoreallcaps="' . $_GET['ignorecaps'] . '">';
$body .= '<text>' . $text . '</text>';
$body .= '</spellrequest>';

$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $body);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec ($ch);
curl_close ($ch); 


// Custom misspelling engine here
/*
require("../lib/db.class.php");
$database = db::singleton();

$words = split(" ", $text);
$args = "";
$SQL = "SELECT c.word AS correction, i.word AS incorrect FROM spelling_matches AS m LEFT JOIN spell_correctwords AS c ON (m.correctID = c.correctID) LEFT JOIN spell_mispellings AS i ON (m.misspellID = i.misspellID) WHERE";

foreach($words as $word) $args .= " i.word = '" . $database->real_escape_string($word) . "' OR";

$args = rtrim($args, 'OR');



$result = $database->single($SQL . $args);


if(count($result) > 0){

	$returnXML = new DOMDocument();
	
	$returnXML->loadXML($contents);
	
	$contents = $returnXML->saveXML();
}
*/
// Give the browser the results.
echo $contents;
?>