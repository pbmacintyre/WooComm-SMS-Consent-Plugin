<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 *
 */

/* ================== */
/* Turn on Error codes */
/* ================== */
function show_errors($toggle = 1) {
    error_reporting();
//     error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', $toggle);
}
function echo_admin_spaces($text, $value="", $lines=0) {
    $count = 50;
    for ($i = 1; $i <= $count; $i++) echo "&nbsp;" ;
	echo "<strong><p style='color: red; display: inline'>$text:</p></strong> " ;
	if (is_string($value) || is_int($value))  { echo $value; }
	if (is_object($value) || is_array($value)) { ringcentral_dump_obj($value); }
	if (is_bool($value)) { if ($value) echo "TRUE / Success"; else echo 'FALSE / Failure'; }
	$i = 1 ;
	while ($i <= $lines) {
		echo "<br />" ;
		$i++;
	}
}
function echo_spaces($text, $value="", $color="red", $bold=0, $lines=0 ) {
	if ($bold == 1) { echo "<strong>" ; }
	echo "<p style='color: $color; display: inline'>$text";
	if ($value !== "") { echo ": " ; }
	echo "</p>";
	if ($bold == 1) { echo "</strong>" ; }
	if (is_string($value) || is_int($value))  { echo $value; }
	if (is_object($value) || is_array($value)) { ringcentral_dump_obj($value); }
	if (is_bool($value)) { if ($value) echo "TRUE / Success"; else echo 'FALSE / Failure'; }
	$i = 1 ;
	while ($i <= $lines) {
		echo "<br />" ;
		$i++;
	}
}
/* ======================== */
/* Dump the provided object */
/* ======================== */
function ringcentral_dump_obj($object) {
    echo "<pre>";
    var_dump($object);
    echo "</pre>";
}
