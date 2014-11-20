<?
require_once '../security.php';
?>
<?
include "../fns_dotazy.php";
require_once '../db.php';

dbConnect();
require("../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}

	// stranka je kodovana v utf8, tak chci vysledky z databaze taky v utf8 , protoze je mam ulozene v cp1250
	mysql_query('set names utf8');

	if(isset($_GET['kunde']))
	{
		$kunde=$_GET['kunde'];	
		$sql="select ";
		$sql.="kunde,";
		$sql.="name1,";
		$sql.="name2,";
		$sql.="Straße as strasse,";
		$sql.="plz,";
		$sql.="ort,";
		$sql.="land,";	
		$sql.="tel,";
		$sql.="fax,";
		$sql.="`Preis-VZh` as preisvzh,";
		$sql.="`Rech-anschr` as rechanschr,";
		$sql.="konto,";
		$sql.="`waehr-kz` as waehrkz,";
		$sql.="preismin,";
		$sql.="preisfracht,";
		$sql.="preiszoll,";
		$sql.="preissonst,";
		$sql.="ico,";
		$sql.="dic,";
		$sql.="sachbearbeiteraby,";
		$sql.="telaby,";
		$sql.="faxaby,";
		$sql.="emailaby,";
		$sql.="statcislo,";
		$sql.="zahnlungziel,";
		$sql.="preis_runden,";
		$sql.="kunden_stat_nr";
		$sql.=" from dksd where (kunde='".$_GET['kunde']."')";
		$res=mysql_query($sql);
		$row=mysql_fetch_array($res);
		
		foreach($row as $klic=>$hodnota)
		{
			$varName=$klic."_value";
			$smarty->assign($varName,$hodnota);
		}	

		
		$konto_selected=$row['konto'];
		$kunden_stat_nr_selected=$row['kunden_stat_nr'];
		
		if(strlen($konto_selected)==0)
			$konto_selected='null';
			
		$sql="select konto,`text-konto` as textkonto,`text-verwzweck` as textverwzweck,stamp from dkonto order by konto";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$konto_options[$row['konto']]=$row['konto'];
		}
		// musim pridat i prazdnou variantu
		$konto_options['null']='';
		
		$smarty->assign("konto_options",$konto_options);		
		$smarty->assign("konto_selected",$konto_selected);
		
		
			
		$sql="select pg_nr from dproduktgruppen order by pg_nr";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$kunden_stat_nr_options[$row['pg_nr']]=$row['pg_nr'];
		}
		
		$smarty->assign("kunden_stat_nr_options",$kunden_stat_nr_options);		
		$smarty->assign("kunden_stat_nr_selected",$kunden_stat_nr_selected);
		
		// tabulka s predchozimi cenami
		$sql="select id,`ksd-knr` as kunde,preis,waehrung,DATE_FORMAT(`gueltig-bis`,'%d.%m.%Y') as gultigbis from `dksd-preis` where (`ksd-knr`='$kunde')";
		//echo $sql;
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$preiseRows[$row['id']]=$row;
		}		
		
		$smarty->assign('ehemaligepreise',$preiseRows);
		
		// dokumenty z gdatu
		$apl = AplDB::getInstance();
		$docsArray = array();
		$kundeGdatPath = $apl->getKundeGdatPath($kunde);
		if ($kundeGdatPath !== NULL) {
		    $i=0;
		foreach (new DirectoryIterator('/mnt/gdat/Dat/' . $kundeGdatPath) as $file) {
		    // if the file is not this file, and does not start with a '.' or '..',
		    // then store it for later display
		    if ( (!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF'])) ) {
			// if the element is a directory add to the file name "(Dir)"
			//echo ($file->isDir()) ? "(Dir) ".$file->getFilename() : $file->getFilename()."<br>";
			    if(!$file->isDir()){
				$docsArray[$i]['filename']=$file->getFilename();
				$docsArray[$i]['url']="/gdat".substr($file->getPath(),13) ."/". $file->getFilename();
				//echo "<a href='/gdat".substr($file->getPath(),13) ."/". $file->getFilename()."'>".$file->getFilename()."</a><br>";
				$i++;
			    }
		    }
		}
		}
		$smarty->assign('docs',$docsArray);
		if(count($docsArray)>0)
		    $smarty->assign('showDocsTable',1);
		else
		    $smarty->assign('showDocsTable',0);
	}
	$smarty->display('dksd.tpl');
?>
