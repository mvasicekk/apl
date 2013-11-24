<?php
	session_start();
	
	//print_r($_SESSION['ses_pop']);
	
	
	$datum=$_GET['datevon'];
	$parameters=$_GET;
	
	// casti datumu povolim oddelovat znaky : ,.- a mezera
	$vymenit=array(",",".","-"," ");
	if(strlen($datum)>=5)
	{
		// sjednotim si oddelovaci znak
		$novy_datum=str_replace($vymenit,"/",$datum);
		// rozkouskuju na jednotlivy casti
		$dily=explode("/",$novy_datum);
		// trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
		if(($dily[1]<13)&&($dily[1]>0)&&($dily[0]>0)&&($dily[0]<32))
		{
			$timestamp=mktime(0,0,0,$dily[1],$dily[0],$dily[2]);
			$rok=date("Y",$timestamp);
			$mesic=date("m",$timestamp);
			$den=date("d",$timestamp);
			// provedena jen mala kontrola datumu
			//echo "$den.$mesic.$rok";
		}
	}
	
	$datum=$rok."-".$mesic."-".$den;
	
	//
	
	require_once('S165_xml.php');

	$domxsl = new DOMDocument;
	$domxsl->load("S165.xsl");
	
	$proc = new XSLTProcessor;
	
	$proc->importStyleSheet($domxsl);
	
	//header('Content-Type: application/xml');
	//echo "<pre>".$domxml->saveXML()."</pre>";
    echo $proc->transformToXML($domxml);
	
	// zahodim nastavene parametry
	unset($_SESSION['ses_pop']);
	//print $domxml->saveXML();
	//$domxml->save("S165.xml");
?>
