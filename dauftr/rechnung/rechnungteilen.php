<?
session_start();
require "../../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$seznamPolozek = $_GET['seznampolozek'];
	$rechnungNeu = trim($_GET['rechnungNeu']);

    $idArray = split(",", $seznamPolozek);


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	
	$nodeElement = $doc->createElement("rechnungNeu");
	$nodeText = $doc->createTextNode($rechnungNeu);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    foreach ($idArray as $poradi => $id) {
        if(strlen($id)>0){
            $sql = "update drechneu set auftragsnr='$rechnungNeu' where (id='$id') limit 1";
            $nodeElement = $doc->createElement("sql");
            $nodeText = $doc->createTextNode($sql);
            $nodeElement->appendChild($nodeText);
            $root->appendChild($nodeElement);

            $nodeElement = $doc->createElement("id");
            $nodeText = $doc->createTextNode($id);
            $nodeElement->appendChild($nodeText);
            $root->appendChild($nodeElement);
            
            mysql_query($sql);
        }
    }

    $pocetRadku = mysql_affected_rows();
    
	$nodeElement = $doc->createElement("affectedrows");
	$nodeText = $doc->createTextNode($pocetRadku);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

    $error = mysql_error();
    if(strlen($error)>0){
        $nodeElement = $doc->createElement("error");
        $nodeText = $doc->createTextNode($error);
        $nodeElement->appendChild($nodeText);
        $root->appendChild($nodeElement);
    }
	

	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

