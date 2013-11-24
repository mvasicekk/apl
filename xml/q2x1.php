<?php
	 require_once('XML/Query2XML.php');
     require_once('DB.php');
     $query2xml = XML_Query2XML::factory($db= &DB::connect('mysql://root:nuredv@localhost/apl'));
	 $db->query("set character set cp1250");
     $dom = $query2xml->getflatXML("SELECT * from dessen");
        header('Content-Type: application/xml');
      print $dom->saveXML();
?>
