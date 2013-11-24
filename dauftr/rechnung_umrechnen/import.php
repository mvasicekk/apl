<?
 session_start();
?>
<?
include "../../fns_dotazy.php";
//dbConnect();
dbConnectRemote(REMOTE_HOST,REMOTE_USER,REMOTE_PASS,REMOTE_DB);
require("../../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}


	mysql_query("set names utf8");
	

	$sql="SELECT d.`AuftragsNr`,DATE_FORMAT(d.Datum,'%d.%m.%Y') as rechnungsdatum,";
	$sql.=" DATE_FORMAT(d.`datum-auslief`,'%d.%m.%Y') as lieferdatum,";
	$sql.=" if(drechneu.origauftrag is null,'W','F') as wartet_fertig";
	$sql.=" FROM drechbew d"; 
	$sql.=" left join drechneu on d.AuftragsNr=drechneu.origauftrag group by d.AuftragsNr order by d.Datum desc limit 150";

	$res=mysql_query($sql);
	
			
	while($row=mysql_fetch_array($res))
	{
		$rows[$row['AuftragsNr']]=$row;
	}

	$smarty->assign("rechnungen",$rows);
	$smarty->assign("sqldauftr",$sql);
	$smarty->assign("sqlerror",$error);
	$smarty->display('import.tpl');
?>
