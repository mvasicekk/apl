<?php
/*
 * Created on 07.01.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
require_once('DB.php');
require_once('XML/Query2XML.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;
header("Content-Type: text/html; charset=UTF-8");
$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$res=$db->query("select teil,teilbez,`TaetBez-Aby-D`,`TaetBez-Aby-T` from dkopf join dpos using(teil) limit 1000");


while($res->fetchInto($row))
{
	echo $row['teil']." ".$row['teilbez']." ".$row['TaetBez-Aby-D']." ".$row['TaetBez-Aby-T']."<br>";
} 
?>
