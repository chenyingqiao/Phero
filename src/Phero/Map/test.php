<?php 
$content=file_get_contents("/var/www/html/test/SimplePhp/Kernal/Database/DbUnit.class.php");
// $content=file_get_contents(__file__);
var_dump($content);

preg_match_all("/@map\[([\w]+)\]\(([\S]+)\)/", $content, $match);
var_dump($match);
$map['map']=array();
foreach ($match[2] as $key => $value) {
	$split=split(',',$value);
	foreach ($split as $key1 => $value1) {
		$key_value=split('=',$value1);
		$map['map'][$key_value[0]]=$key_value[1];
	}
}
var_dump($map);