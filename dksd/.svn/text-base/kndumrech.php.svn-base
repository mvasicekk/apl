<?
 session_start();
?>
<?
include "../fns_dotazy.php";

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

	
	if((isset($_POST['save']))&&($_POST['save']==1)){
		// mam ulozit data
		// rozhodnout jestli update nebo insert
		$vom = $_POST['vom'];
		$an = $_POST['an'];
		
		$sql = "select vom from dkndumrech where ((vom='$vom') and (an='$an'))";
		$res = mysql_query($sql);
		if(mysql_affected_rows()>0){
			// dvojice existuje , bude update
			$sql = "update dkndumrech set ";
			$sql.= "letzterechnung='".$_POST['letzterechnung']."'";
			$sql.= ",letzterechnung_sonst='".$_POST['letzterechnung_sonst']."'";
			$sql.= ",minpreis='".$_POST['minpreis']."'";
			$sql.= ",fracht='".$_POST['fracht']."'";
			$sql.= ",zoll='".$_POST['zoll']."'";
			$sql.= ",sonst='".$_POST['sonst']."'";
			$sql.= ",wahr='".$_POST['wahr']."'";
			$sql.= ",mwst='".$_POST['mwst']."'";
			$sql.= ",zahlungsziel='".$_POST['zahlungsziel']."'";
			$sql.= ",rechtext='".$_POST['rechtext']."'";
			$sql.= ",kontotext='".$_POST['kontotext']."'";
			$sql.= ",verzweck='".$_POST['verzweck']."'";
			$sql.= ",fusszeile1='".$_POST['fusszeile1']."'";
			$sql.= ",fusszeile2='".$_POST['fusszeile2']."'";
			$sql.= ",fusszeile3='".$_POST['fusszeile3']."'";
			$sql.= " where ((vom='$vom') and (an='$an'))";
			$res = mysql_query($sql);
			$ar = mysql_affected_rows();
			$error = mysql_error();
			$smarty->assign("sql",$sql);
			$smarty->assign("error",$error);
			header("Location: kndumrech.php?vom=$vom&an=$an");
		}
		else{
			// dvojice neexistuje, bude insert
			$sql = "insert into dkndumrech ";
			$sql.= "(vom,an,letzterechnung,letzterechnung_sonst,minpreis,fracht,zoll,sonst,wahr,mwst,zahlungsziel,rechtext,kontotext,verzweck,fusszeile1,fusszeile2,fusszeile3) ";
			$sql.= "values (";
			$sql.= "'".$_POST['vom']."'";
			$sql.= ",'".$_POST['an']."'";
			$sql.= ",'".$_POST['letzterechnung']."'";
			$sql.= ",'".$_POST['letzterechnung_sonst']."'";
			$sql.= ",'".$_POST['minpreis']."'";
			$sql.= ",'".$_POST['fracht']."'";
			$sql.= ",'".$_POST['zoll']."'";
			$sql.= ",'".$_POST['sonst']."'";
			$sql.= ",'".$_POST['wahr']."'";
			$sql.= ",'".$_POST['mwst']."'";
			$sql.= ",'".$_POST['zahlungsziel']."'";
			$sql.= ",'".$_POST['rechtext']."'";
			$sql.= ",'".$_POST['kontotext']."'";
			$sql.= ",'".$_POST['verzweck']."'";
			$sql.= ",'".$_POST['fusszeile1']."'";
			$sql.= ",'".$_POST['fusszeile2']."'";
			$sql.= ",'".$_POST['fusszeile3']."'";
			$sql.= " )";
			$res = mysql_query($sql);
			$ar = mysql_affected_rows();
			$error = mysql_error();
			$smarty->assign("sql",$sql);
			$smarty->assign("error",$error);
			header("Location: kndumrech.php?vom=$vom&an=$an");
		}
	}
	// seznam vsech dvojic
	$sql = "select * from dkndumrech order by vom,an";
	$res=mysql_query($sql);
	$i=0;
	while($row = mysql_fetch_array($res)){
		$row['id']=$i;
		$rs[$i]=$row;
		$i++;
	}
	$smarty->assign("kndpaare",$rs);

	if((isset($_GET['vom']))&&($_GET['vom']>0)){
		$vom=$_GET['vom'];
		$an = $_GET['an'];
		$smarty->assign("haspaarinfo",1);
		$sql = "select * from dkndumrech where ((vom='$vom') and (an='$an'))";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		// zjistim si jmena zakazniku
		$vomname = getKndName($vom);
		$anname = getKndName($an);
		$row['vomname']=$vomname;
		$row['anname']=$anname;
		$smarty->assign("paarinfo",$row);
	}
	else
	{
		$smarty->assign("haspaarinfo",0);
	}
	/*
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
	}

	*/
	$smarty->display('kndumrech.tpl');
?>
