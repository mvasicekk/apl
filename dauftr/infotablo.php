<?
//require_once '../security.php';
?>
<?
require_once '../db.php';
require("../libs/Smarty.class.php");
$smarty = new Smarty;
$a = AplDB::getInstance();

$ipKlienta = $_SERVER['REMOTE_ADDR'];

$tA = array(
        't1'=>'',
        't2'=>'',
        't3'=>'',
        );

$textArray = $a->getInfoTabloTextArray($ipKlienta);

//var_dump($textArray);


if($textArray!==NULL){
    $tA['t1']=$textArray[0]['text1'];
    $tA['t2']=$textArray[0]['text2'];
    $tA['t3']=$textArray[0]['text3'];
}
        $smarty->assign('klientip',$ipKlienta);
        $smarty->assign('ta',$tA);
        $smarty->display('infotablo.tpl');
?>
