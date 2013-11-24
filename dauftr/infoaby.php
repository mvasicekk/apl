<?
 session_start();
?>
<?
require_once '../db.php';
require("../libs/Smarty.class.php");
$smarty = new Smarty;
$a = AplDB::getInstance();

$ipKlienta = $_SERVER['REMOTE_ADDR'];

$abyInfoMinuten = array();

$infoArray = $a->getAbyInfoMinuten();


//var_dump($textArray);
$datumDnes = date('Y-m-d');
$datumVcera = date('Y-m-d',  time()-24*60*60);

if($infoArray!==NULL){
    foreach ($infoArray as $info){
	
	if($info['datum']==$datumDnes){
	    $abyInfoMinuten['dnes']=$info;
	}
	
	if($info['datum']==$datumVcera){
	    $abyInfoMinuten['vcera']=$info;
	}
	
	$abyInfoMinuten['mesic']['pg1']['vzkd'] += $info['pg1_vzkd'];
	$abyInfoMinuten['mesic']['pg3']['vzkd'] += $info['pg3_vzkd'];
	$abyInfoMinuten['mesic']['pg4']['vzkd'] += $info['pg4_vzkd'];
	$abyInfoMinuten['mesic']['pg9']['vzkd'] += $info['pg9_vzkd'];
	
	$abyInfoMinuten['mesic']['pg1']['vzaby'] += $info['pg1_vzaby'];
	$abyInfoMinuten['mesic']['pg3']['vzaby'] += $info['pg3_vzaby'];
	$abyInfoMinuten['mesic']['pg4']['vzaby'] += $info['pg4_vzaby'];
	$abyInfoMinuten['mesic']['pg9']['vzaby'] += $info['pg9_vzaby'];
	
	$abyInfoMinuten['mesic']['pg1']['verb'] += $info['pg1_verb'];
	$abyInfoMinuten['mesic']['pg3']['verb'] += $info['pg3_verb'];
	$abyInfoMinuten['mesic']['pg4']['verb'] += $info['pg4_verb'];
	$abyInfoMinuten['mesic']['pg9']['verb'] += $info['pg9_verb'];
	
	$abyInfoMinuten['mesic']['celkem']['vzkd'] += $info['celkem_vzkd'];
	$abyInfoMinuten['mesic']['celkem']['vzkd'] += $info['celkem_vzaby'];
	$abyInfoMinuten['mesic']['celkem']['vzkd'] += $info['celkem_verb'];
	
    }
}
        $smarty->assign('klientip',$ipKlienta);
	$smarty->assign('datumdnes',$datumDnes);
	$smarty->assign('datumvcera',$datumVcera);
	$smarty->assign('abyinfo',$abyInfoMinuten);
        $smarty->display('infoaby.tpl');
?>
