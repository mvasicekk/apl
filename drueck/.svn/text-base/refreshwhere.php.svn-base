<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$filterparam = $_GET['filterparam'];
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	//mysql_query('set names utf8');
	//$sql = "select * from drueck where (drueck_id='$drueck_id')";
	//$result=mysql_query($sql);
	//$user = get_user_pc();
	//$rowSelect = mysql_fetch_array($result);
	// vytvorim obracene hodnoty ke kusu, ausschussum a verb
	
	// rozkouskuju si filter param na jednotlive polozky
	list($auftragsnr,$teil,$pal,$taetnr,$datum,$persnr)=explode(";",$filterparam);
	// datum prevedu do formatu vhodneho pro db
	$datumOld=$datum;
	if(strlen($datum)>0)
	{
			$datum=make_DB_datum($datum);
	}
		
	$whereFilter = "";
	
	if(strlen($auftragsnr)>0)
	{
		if(strlen($whereFilter)>0)	
			$whereFilter.=" and (drueck.auftragsnr='$auftragsnr')";
		else
			$whereFilter.=" (drueck.auftragsnr='$auftragsnr')";
	}
	
	if(strlen($teil)>0)
	{
		if(strlen($whereFilter)>0)	
			$whereFilter.=" and (drueck.teil='$teil')";
		else
			$whereFilter.=" (drueck.teil='$teil')";
	}
	
	if(strlen($pal)>0)
	{
		if(strlen($whereFilter)>0)	
			$whereFilter.=" and (drueck.`pos-pal-nr`='$pal')";
		else
			$whereFilter.=" (drueck.`pos-pal-nr`='$pal')";
	}

	if(strlen($taetnr)>0)
	{
		if(strlen($whereFilter)>0)	
			$whereFilter.=" and (drueck.`taetnr`='$taetnr')";
		else
			$whereFilter.=" (drueck.`taetnr`='$taetnr')";
	}
	
	if(strlen($datum)>0)
	{
		if(strlen($whereFilter)>0)	
			$whereFilter.=" and (drueck.`datum`='$datum')";
		else
			$whereFilter.=" (drueck.`datum`='$datum')";
	}
	
		if(strlen($persnr)>0)
	{
		if(strlen($whereFilter)>0)	
			$whereFilter.=" and (drueck.`persnr`='$persnr')";
		else
			$whereFilter.=" (drueck.`persnr`='$persnr')";
	}
	
	if(strlen($whereFilter)>0)
		$whereFilter = "( ".$whereFilter." )";
	
	// podminka na nevyexportovane pozice
	//$noExWhere = "( ( dauftr.`auftragsnr-exp` is null) and (dauftr.`pal-nr-exp` is null) )";
	$noExWhere = "( 1 )";
	mysql_query('set names utf8');
	$sql = "select drueck_id,drueck.auftragsnr,drueck.teil,drueck.`pos-pal-nr`as pal,drueck.taetnr,drueck.`Stück` as stk,drueck.`auss-stück` as aussstk,drueck.`auss-art` as aart,drueck.auss_typ as atyp";
	$sql.= ",drueck.`vz-soll` as vzkd,drueck.`vz-ist` as vzaby,DATE_FORMAT(drueck.datum,'%d.%m.%Y') as datum,drueck.persnr,DATE_FORMAT(drueck.`verb-von`,'%H:%i') as von,DATE_FORMAT(drueck.`verb-bis`,'%H:%i') as bis,drueck.`verb-zeit` as verb,drueck.`verb-pause` as pause";
	$sql.= ",drueck.oe,drueck.`marke-aufteilung` as aufteilung,SUBSTRING(drueck.comp_user_accessuser,14) as user,DATE_FORMAT(drueck.stamp,'%d.%m.%y %H:%i:%s') as stamp,";
	$sql.= "if(( dauftr.`auftragsnr-exp` is not null) and (dauftr.`pal-nr-exp` is not null),1,0) as exportflag";
	$sql.= " from drueck ";
	//$sql.= " where ( $whereFilter ) order by drueck.stamp desc limit 100";
	
	
	$sql.= " left join dauftr on dauftr.auftragsnr=drueck.auftragsnr and dauftr.teil=drueck.teil and dauftr.`pos-pal-nr`=drueck.`pos-pal-nr` and dauftr.abgnr=drueck.taetnr ";
	if(strlen($whereFilter)>0)
		$sql.= " where ( ($whereFilter) and $noExWhere ) order by drueck.stamp desc limit 100";
	else
		$sql.= " where ( $noExWhere ) order by drueck.stamp desc limit 100";
	
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	$result=mysql_query($sql);
	
	if(mysql_affected_rows()>0)
	{
		// vytvorim pole s nazvy podle vybranych sloupcu z dotazu
		$nodes=getFieldsArray($result);
		while ($row = mysql_fetch_array($result))
		{
			$rowNode = $doc->createElement("row");
			foreach($nodes as $node)
			{
				$nodeElement = $doc->createElement($node);
				$nodeText = $doc->createTextNode($row[$node]);
				$nodeElement->appendChild($nodeText);
				$rowNode->appendChild($nodeElement);
			}
			$root->appendChild($rowNode);
		}
	}
	else
	{
		//$nodeElement = $doc->createElement('error');
		//$nodeText = $doc->createTextNode("where error");
		//$nodeElement->appendChild($nodeText);
		//$root->appendChild($nodeElement);
	}

	//$auftragsnr,$teil,$pal,$taetnr,$datum,$persnr
	
	$filterElement = $doc->createElement("filter");
	
	$nodeElement = $doc->createElement('auftragsnr');
	$nodeText = $doc->createTextNode($auftragsnr);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement('teil');
	$nodeText = $doc->createTextNode($teil);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
		
	$nodeElement = $doc->createElement('pal');
	$nodeText = $doc->createTextNode($pal);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement('taetnr');
	$nodeText = $doc->createTextNode($taetnr);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement('datum');
	$nodeText = $doc->createTextNode($datumOld);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement('persnr');
	$nodeText = $doc->createTextNode($persnr);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement('sql');
	$nodeText = $doc->createTextNode($sql);
	$nodeElement->appendChild($nodeText);
	$filterElement->appendChild($nodeElement);
	
	$root->appendChild($filterElement);
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
	
?>

