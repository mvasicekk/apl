<?
session_start();
require("./libs/Smarty.class.php");
$smarty = new Smarty;

include "fns_dotazy.php";
require_once './db.php';

dbConnect();

$apl = AplDB::getInstance();

	$prihlasen=0;
	// otestuju hodnoty POST promennych user a password
	// pokud budou neco obsahovat, zkusim uzivatele prihlasit
	if(isset($_POST['username'])&&isset($_POST['password']))
	{
		// podivam se do DB, zda mam odpovidajiciho uzivatele
		// pokud ano, tak ho prihlasim a nastavim SESSION promenne
		$puser=$_POST['username'];
		$ppassword=$_POST['password'];
		$ip = $_SERVER["REMOTE_ADDR"];
		
		$access = grantAccess($puser,$ppassword,$ip);		
		
		if($access['loginok']==1){
			$_SESSION['user']=$access['name'];
			$_SESSION['level']=$access['level'];
			$smarty->assign("prihlasen",1);
			$prihlasen=1;
			$smarty->assign("user",$access['name']);
			$smarty->assign("level",$access['level']);
                        $display_sec = array();

			$element_id = 'telbuch';
			$display_sec[$element_id] = $apl->getDisplaySec('start',$element_id,$puser)?'block':'none';
			$element_id = 'umtermin';
			$display_sec[$element_id] = $apl->getDisplaySec('start',$element_id,$puser)?'block':'none';
			$element_id = 'kundepflegen';
			$display_sec[$element_id] = $apl->getDisplaySec('start',$element_id,$puser)?'block':'none';
			$element_id = 'rundlauf';
			$display_sec[$element_id] = $apl->getDisplaySec('start',$element_id,$puser)?'block':'none';
			$element_id = 'reklamation';
			$display_sec[$element_id] = $apl->getDisplaySec('start',$element_id,$puser)?'block':'none';
			
                        $smarty->assign("display_sec",$display_sec);
		}
		else
		{
			unset ($_SESSION['user']);
			unset ($_SESSION['level']);
			session_destroy();
			$smarty->assign("prihlasen",0);
		}
		
		
		insertAccessLog($_POST['username'],$_POST[password],$prihlasen,get_pc_ip());
	}
	
	if(isset($_GET['akce'])&&$_GET['akce']=="logout")
	{
		unset ($_SESSION['user']);
		unset ($_SESSION['level']);
		unset ($_POST['username']);
		unset ($_POST['password']);
		session_destroy();
	}

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}

	
	if ($apl->userHasRole($_SESSION['user'], 'telbuch'))
	    $display_sec['telbuch'] = 'block';
	else
	    $display_sec['telbuch'] = 'none';

	if ($apl->userHasRole($_SESSION['user'], 'umtermin'))
	    $display_sec['umtermin'] = 'block';
	else
	    $display_sec['umtermin'] = 'none';

	$smarty->assign("display_sec", $display_sec);

// nstazeni informace o rolucj izivazele
// predat v poli s rolema
//$userName = $_SESSION['user'];
//$userName = trim(stripslashes($userName));
//$roleArray = $apl->getRolesArray($userName);
	//$roleArray = $apl->getRolesArray($userName);
	


// spocitam hodnoty pro tabulku s aktualnima vykonama pro tento mesic

	$sql_leistung="select DATE_FORMAT(drueck.datum,'%d.%m.%Y') as datum,sum(if(kunden_stat_nr=1,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg1,sum(if(kunden_stat_nr=3,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg3,sum(if(kunden_stat_nr=4,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg4,sum(if(kunden_stat_nr=9,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg9,sum(if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`)) as celkem from drueck join dkopf using (teil) join dksd using (kunde) where (datum between  subdate(current_date(),day(current_date())-1) and CURRENT_DATE()) group by drueck.datum order by drueck.datum desc limit 30";
	//echo $sql_leistung;
	mysql_query('set names utf8');
	
	$res = mysql_query($sql_leistung) or die(mysql_error());
	$i=0;
	while($row=mysql_fetch_array($res))
	{
		$datum=$row['datum'];
		$pole[$i]['datum']=$datum;$pg1=$row['pg1'];$pole[$i]['pg1']=$pg1;$sum_pg1+=$pg1;
		$pg3=$row['pg3'];$pole[$i]['pg3']=$pg3;$sum_pg3+=$pg3;
		$pg4=$row['pg4'];$pole[$i]['pg4']=$pg4;$sum_pg4+=$pg4;
		$pg9=$row['pg9'];$pole[$i]['pg9']=$pg9;$sum_pg9+=$pg9;
		$celkem=$row['celkem'];$pole[$i]['celkem']=$celkem;$sum_celkem+=$celkem;
		
		$i++;
	}
	$smarty->assign("datum",$datum);
	$smarty->assign("pole",$pole);
	$smarty->assign("sum_pg1",$sum_pg1);
	$smarty->assign("sum_pg3",$sum_pg3);
	$smarty->assign("sum_pg4",$sum_pg4);
	$smarty->assign("sum_pg9",$sum_pg9);
	$smarty->assign("sum_celkem",$sum_celkem);

        // zjistim seznam dnesnich importu
        $dnesniDatumDB = date('Y-m-d');
        $sql = "select daufkopf.kunde,daufkopf.auftragsnr,DATE_FORMAT(daufkopf.`Aufdat`,'%d.%m.%Y') as aufdat,DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(daufkopf.fertig,'%d.%m.%Y') as fertig from daufkopf where (daufkopf.`Aufdat`='$dnesniDatumDB') order by kunde,auftragsnr";
        $res = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_array($res)){
            $zakazkyIM[$row['auftragsnr']] = $row;
        }
        $smarty->assign("zakazkyIM",$zakazkyIM);

        // zjistim seznam dnesnich exportu
        $dnesniDatumDB = date('Y-m-d');
        $sql = "select daufkopf.kunde,daufkopf.auftragsnr,DATE_FORMAT(daufkopf.`Aufdat`,'%d.%m.%Y') as aufdat,DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(daufkopf.fertig,'%d.%m.%Y') as fertig from daufkopf where (daufkopf.ausliefer_datum='$dnesniDatumDB' or DATE_FORMAT(daufkopf.ex_datum_soll,'%Y-%m-%d')='$dnesniDatumDB' ) order by kunde,auftragsnr";
        $res = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_array($res)){
            $zakazkyEX[$row['auftragsnr']] = $row;
        }
        $smarty->assign("zakazkyEX",$zakazkyEX);

        
	$smarty->display('index.tpl');
?>

