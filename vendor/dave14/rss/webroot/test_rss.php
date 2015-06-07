<?php
require '../src/library/CRSS.php';

$feed = new \Dave14\library\CRSS([
	'http://feeds.reuters.com/news/reutersmedia'
]);
?>

<!doctype html>
<meta charset=utf8>
<title>Exempel med RSS</title>
<h1>Detta är ett avskalat exempel för att visa ett RSS-flöde.</h1>
<?=$feed->printFeed()?>