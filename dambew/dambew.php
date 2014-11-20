<?
 require_once '../security.php';
?>
<?
include "../fns_dotazy.php";
require_once '../db.php';

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


	$smarty->assign('auftragsnr_value',$_SESSION['auftragsnr_old']);
	$_SESSION['auftragsnr_old']='';
	
	if(strlen($_SESSION['pal_old'])==0)
		$smarty->assign('pal_value',0);
	else
		$smarty->assign('pal_value',$_SESSION['pal_old']);
		
	$_SESSION['pal_old']='';

	$dnesDatum = date('Y-m-d');
	
	$sql = "select drueck_id,auftragsnr,teil,`pos-pal-nr`as pal,taetnr,`Stück` as stk,`auss-stück` as aussstk,`auss-art` as aart,auss_typ as atyp";
	$sql.= ",`vz-soll` as vzkd,`vz-ist` as vzaby,DATE_FORMAT(datum,'%d.%m.%Y') as datum,persnr,DATE_FORMAT(`verb-von`,'%H:%i') as von,DATE_FORMAT(`verb-bis`,'%H:%i') as bis,`verb-zeit` as verb,`verb-pause` as pause";
	$sql.= ",schicht,`marke-aufteilung` as aufteilung,comp_user_accessuser as user";
	$sql.= " from drueck order by stamp desc limit 5";

	$res = mysql_query($sql);
	while($row=mysql_fetch_array($res))
	{
		$stornoRow[$row['drueck_id']]=$row;
	}

        $apl = AplDB::getInstance();

        $oeInfoArray = $apl->getOEInfoArray();
        $oes = array();
        foreach ($oeInfoArray as $oeInfo) {
            array_push($oes, $oeInfo['tat']);
        }

        $smarty->assign('oes',$oes);
        $smarty->assign('oeselected',$oes[0]);
        
	$smarty->assign("stornorows",$stornoRow);
	$smarty->assign("sql",$sql);
	
	
	$smarty->display('dambew.tpl');
?>
