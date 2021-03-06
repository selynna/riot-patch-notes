<?php
set_time_limit(3600);

require_once('helper.php');

$ldb = new PDO('mysql:host=localhost;dbname=riotpatchnotes', 'root', '');

$cres = $ldb->prepare("
	SELECT *
	FROM champions
	WHERE name LIKE :name
");

$ires = $ldb->prepare("
	SELECT *
	FROM items
	WHERE name LIKE :name
");


$iires = $ldb->prepare("
	INSERT INTO patch_champion_items
	(patchId, championId, itemId)
	VALUES
	(:patchId, :championId, :itemId)
");

$irres = $ldb->prepare("
	INSERT INTO patch_champion_runes
	(patchId, championId, runeId)
	VALUES
	(:patchId, :championId, :runeId)
");


function fixJSON($json) {
    $regex = <<<'REGEX'
~
    "[^"\\]*(?:\\.|[^"\\]*)*"
    (*SKIP)(*F)
  | '([^'\\]*(?:\\.|[^'\\]*)*)'
~x
REGEX;

    return preg_replace_callback($regex, function($matches) {
        return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
    }, $json);
}

$str = file_get_contents('info.json');

$fix = fixJSON($str);

$json = json_decode($fix, true);

foreach($json['data'] as $data){
	$name = $data['name'];
	$name = strtolower($name);
	$name = str_replace(' ', '', $name);
	$name = str_replace('.', '', $name);
	$name = str_replace('\'', '', $name);
	$cres->bindParam('name', $name);
	$cres->execute();
	$crow = $cres->fetch();
	if($crow === false){
		echo 'UNKNOWN CHAMPION ID '.$name.'<br>';
	}
	$championId = $crow['id'];
	foreach($data['runes'] as $rune){
		$runeId = getRuneIdFromName($rune);
		if($runeId === false) echo 'UNKNOWN RUNE ID '.$rune.'<br>';
		
		$irres->bindValue('patchId', '8.22');
		$irres->bindParam('championId', $championId);
		$irres->bindParam('runeId', $runeId);
		$irres->execute();
	}
}

echo '<pre>';
//print_r($json);
echo '</pre>';

?>