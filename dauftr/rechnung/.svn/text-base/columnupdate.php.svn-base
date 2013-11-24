<?
session_start();
require "../../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$column = $_GET['column'];
	$value = trim($_GET['value']);

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	
	// rozdelim parametr column na jmeno sloupce a hodnotu id
	// oddelovaci znak bude _
	
	$pozice = strpos($column,"_");
	$id = substr($column,$pozice+1);
	$column = substr($column,0,$pozice);
	
	$nodeElement = $doc->createElement("column");
	$nodeText = $doc->createTextNode($column);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("value");
	$nodeText = $doc->createTextNode($value);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	$nodeElement = $doc->createElement("id");
	$nodeText = $doc->createTextNode($id);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$pocetRadku = 0;
	$sql = "";
	$error = "";
	//-----------------------------------------------------------------------------------------
	// pole ifu, podle jmena sloupce provadim odpovidajici update
	if($column=="auftragsnr"){
		$sql = "update drechneu set auftragsnr='$value' where (id='$id') limit 1";
	}
	
	if($column=="ausschuss"){
		// musi byt cislo
		if(strlen($value)>0){
			$value = round($value);
			$sql = "update drechneu set ausschuss='$value' where (id='$id') limit 1"; 	
		}
		else{
			$sql="";
			$pocetRadku = 0;
			$error = "Ausschuss value ERROR";
		}
		
	}
	
	if($column=="stk"){
		// musi byt cislo
		if(strlen($value)>0){
			$value = round($value);
			$sql = "update drechneu set `stück`='$value' where (id='$id') limit 1"; 	
		}
		else{
			$sql="";
			$pocetRadku = 0;
			$error = "Stück value ERROR";
		}
		
	}
	
	if($column=="preis"){
		// musi byt cislo
		if(strlen($value)>0){
			//$value = round($value);
			$sql = "update drechneu set `dm`='$value' where (id='$id') limit 1"; 	
		}
		else{
			$sql="";
			$pocetRadku = 0;
			$error = "Preis value ERROR";
		}
		
	}
	
	if($column=="text1"){
		if(strlen($value)>0){
			$sql = "update drechneu set text1='$value' where (id='$id') limit 1"; 	
		}
		else{
			$sql = "update drechneu set text1=null where (id='$id') limit 1";
		}
		
	}
	
	if($column=="fremdauftr"){
		if(strlen($value)>0){
			$sql = "update drechneu set fremdauftr='$value' where (id='$id') limit 1"; 	
		}
		else{
			$sql = "update drechneu set fremdauftr=null where (id='$id') limit 1";
		}
		
	}
	
	if($column=="fremdpos"){
		if(strlen($value)>0){
			$sql = "update drechneu set fremdpos='$value' where (id='$id') limit 1"; 	
		}
		else{
			$sql = "update drechneu set fremdpos=null where (id='$id') limit 1";
		}
		
	}
	
	if($column=="del"){
		$sql = "delete from drechneu where (id='$id') limit 1";
	}
	//-----------------------------------------------------------------------------------------
	
	if(strlen($sql)>0){
		mysql_query('set names utf8');
		mysql_query($sql);
	
		$error = mysql_error();
		$pocetRadku = mysql_affected_rows();
	}
	
	$nodeElement = $doc->createElement("affectedrows");
	$nodeText = $doc->createTextNode($pocetRadku);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("error");
	$nodeText = $doc->createTextNode($error);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("sql");
	$nodeText = $doc->createTextNode($sql);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

