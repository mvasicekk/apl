<?
require_once '../security.php';
?>
<?
include "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

require("../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}

	$dnesDatum = date('Y-m-d');
	
	if(isset($_GET['where']))
		$where=$_GET['where'];
	else
		$where="(datum='$dnesDatum')";
		
	$where=stripcslashes($where);
	
	// pro zacatek vyberu vykony pro aktualni datum
	
	$dnesDatumFormat = date('d.m.Y');
	$sql = "select drueck_id,auftragsnr,teil,`pos-pal-nr`as pal,taetnr,`Stück` as stk,`auss-stück` as aussstk,`auss-art` as aart,auss_typ as atyp";
	$sql.= ",`vz-soll` as vzkd,`vz-ist` as vzaby,DATE_FORMAT(datum,'%d.%m.%Y') as datum,persnr,DATE_FORMAT(`verb-von`,'%H:%i') as von,DATE_FORMAT(`verb-bis`,'%H:%i') as bis,`verb-zeit` as verb,`verb-pause` as pause";
	$sql.= ",schicht,oe,`marke-aufteilung` as aufteilung,SUBSTRING(comp_user_accessuser,14) as user,DATE_FORMAT(stamp,'%d.%m.%y %H:%i:%s') as stamp";
	$sql.= " from drueck where $where order by stamp desc limit 100";

	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
		$stornoRow[$row['drueck_id']]=$row;
	}
	$smarty->assign("stornorows",$stornoRow);
	$smarty->assign("sql",$sql);
	$smarty->assign("dnes",$dnesDatumFormat);
	//$smarty->assign("where",$where);
	
	$smarty->display('storno.tpl');
?>
