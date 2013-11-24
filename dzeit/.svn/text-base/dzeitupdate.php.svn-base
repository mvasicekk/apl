<?
session_start();
require_once '../db.php';

if($_SESSION['user']==""){
    header("Location: ../index.php");
}
else{

    $persnr = $_GET['persnr'];
    $schicht = trim($_GET['schicht']);
    $datum=trim($_GET['datum']);
    $von=trim($_GET['von']);
    $bis=trim($_GET['bis']);
    $pause1=floatval(trim($_GET['pause1']));
    $pause2=floatval(trim($_GET['pause2']));
    $tatigkeit=trim($_GET['tatigkeit']);
    $stunden=floatval(trim($_GET['stunden']));


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	
	// rozdelim parametr column na jmeno sloupce a hodnotu id
	// oddelovaci znak bude _
	
	$nodeElement = $doc->createElement("persnr");
	$nodeText = $doc->createTextNode($persnr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("schicht");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("datum");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("von");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("bis");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("pause1");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("pause2");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("tatigkeit");
	$nodeText = $doc->createTextNode($schicht);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("stunden");
	$nodeText = $doc->createTextNode($stunden);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $aplDB = AplDB::getInstance();

    $auto = $aplDB->insertAnwesenheit(
                                        $persnr,
                                        $schicht,
                                        $datum,
                                        $von,
                                        $bis,
                                        $pause1,
                                        $pause2,
                                        $stunden,
                                        $tatigkeit
                                    );


    $nodeElement = $doc->createElement("auto");
	$nodeText = $doc->createTextNode($auto['verb']);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $nodeElement = $doc->createElement("noaduplicita");
	$nodeText = $doc->createTextNode($auto['noaduplicita']);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);


    // poslednich pet zaznamu
        $sql = "select id,`PersNr` as persnr,DATE_FORMAT(`Datum`,'%d.%m.%Y') as datum,FORMAT(`Stunden`,2) as stunden,`Schicht` as schicht,tat as oe, DATE_FORMAT(anw_von,'%H:%i') as von,if(anw_bis is null,'00:00',DATE_FORMAT(anw_bis,'%H:%i')) as bis,FORMAT(pause1,2) as pause1,FORMAT(pause2,2) as pause2,comp_user_accessuser as user,stamp from dzeit order by stamp desc limit 10";
        $res = mysql_query($sql);
        $items = array();
        $nodeElement = $doc->createElement("zaznamy");
        while($row = mysql_fetch_assoc($res)){
            $zaznamElement = $doc->createElement("radek");
            foreach ($row as $column=>$value){
                $sloupecElement = $doc->createElement($column);
                $nodeText = $doc->createTextNode($value);
                $sloupecElement->appendChild($nodeText);
                $zaznamElement->appendChild($sloupecElement);
            }
            $nodeElement->appendChild($zaznamElement);
        }
        $root->appendChild($nodeElement);
        // pridam do seznamu
        // 
    //--------------------------------------------------------------------------------------------
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
}
?>

