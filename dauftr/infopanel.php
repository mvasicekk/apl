<?
 session_start();
?>
<?
require_once '../db.php';
require("../libs/Smarty.class.php");

$smarty = new Smarty;
$a = AplDB::getInstance();

$panelyRows = $a->getInfoTabloTextArray($ipKlienta);
$panelydiv = '';
$panelydiv.="<table>";
if($panelyRows!==NULL){
    foreach ($panelyRows as $panel){
        $panelydiv.="<tr>";
        $panelydiv.="<td>".$panel['idpanel']."</td>";
        $panelydiv.="<td>"."<input acturl='./saveInfoPanelText.php' id='text1_".$panel['itid']."' type='text' value='".$panel['text1']."' size='20' maxlength='20'"."</td>";
        $panelydiv.="<td>"."<input acturl='./saveInfoPanelText.php' id='text2_".$panel['itid']."' type='text' value='".$panel['text2']."' size='6' maxlength='6'"."</td>";
        $panelydiv.="</tr>";
    }
}
else{
    $panelydiv.="<tr><td>No panels defined !!!</td></tr>";
}
$panelydiv.="</table>";


        $smarty->assign('panelydiv',$panelydiv);
        $smarty->display('infopanel.tpl');
?>
