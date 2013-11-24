<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
		$kunde=trim($_GET['kunde']);
		$name1=trim($_GET['name1']);
		$name2=trim($_GET['name2']);
		$strasse=trim($_GET['strasse']);
		$plz=trim($_GET['plz']);
		$ort=trim($_GET['ort']);
		$land=trim($_GET['land']);	
		$tel=trim($_GET['tel']);
		$fax=trim($_GET['fax']);
		$preisvzh=trim($_GET['preisvzh']);
		$rechanschr=trim($_GET['rechanschr']);
		$konto=trim($_GET['konto']);
		$waehrkz=trim($_GET['waehrkz']);
		$preismin=trim($_GET['preismin']);
		$preisfracht=trim($_GET['preisfracht']);
		$preiszoll=trim($_GET['preiszoll']);
		$preissonst=trim($_GET['preissonst']);
		
		
		//----------------------------------------------------------------------------------------------
		$ico=trim($_GET['ico']);
		// pokud bude ico prazdny vlozim do databaze hodnotu NULL
		if(strlen($ico)==0)
			$ico='null';
		//----------------------------------------------------------------------------------------------
		
		
		$dic=trim($_GET['dic']);
		$sachbearbeiteraby=trim($_GET['sachbearbeiteraby']);
		
		$telaby=trim($_GET['telaby']);
		$faxaby=trim($_GET['faxaby']);
		$emailaby=trim($_GET['emailaby']);
		
		
		$statcislo=trim($_GET['statcislo']);
		$zahnlungziel=trim($_GET['zahnlungziel']);
		$preis_runden=trim($_GET['preis_runden']);
		$kunden_stat_nr=trim($_GET['kunden_stat_nr']);
	

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	// limit 1 je pro jistotu, kdyby se pokazilo kriterium ve where
	
	$sql="update dksd set ";
	$sql.="	kunde='$kunde',";
	$sql.="	name1='$name1',";
	$sql.="	name2='$name2',";
	$sql.="	straße='$strasse',";
	$sql.="	plz='$plz',";
	$sql.="	ort='$ort',";
	$sql.="	land='$land',";	
	$sql.="	tel='$tel',";
	$sql.="	fax='$fax',";
	$sql.="	`preis-vzh`='$preisvzh',";
	$sql.="	`rech-anschr`='$rechanschr',";
	$sql.="	konto='$konto',";
	$sql.="	`waehr-kz`='$waehrkz',";
	$sql.="	preismin='$preismin',";
	$sql.="	preisfracht='$preisfracht',";
	$sql.="	preiszoll='$preiszoll',";
	$sql.="	preissonst='$preissonst',";
	$sql.="	ico=$ico,";
	$sql.="	dic='$dic',";
	$sql.="	sachbearbeiteraby='$sachbearbeiteraby',";
	$sql.="	telaby='$telaby',";
	$sql.="	faxaby='$faxaby',";
	$sql.="	emailaby='$emailaby',";
	$sql.="	statcislo='$statcislo',";
	$sql.="	zahnlungziel='$zahnlungziel',";
	$sql.="	preis_runden='$preis_runden',";
	$sql.="	kunden_stat_nr='$kunden_stat_nr'";
	
	$sql.=" where (kunde='".$kunde."') limit 1";
	
	mysql_query('set names utf8');
	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';
	
	$output .= '</response>';
	
	echo $output;
	
?>

