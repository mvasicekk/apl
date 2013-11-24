<?
require_once '../db.php';

    $id = $_POST['id'];
    $value = $_POST['value'];
    $dauftrId = intval(substr($id, 0,strpos($id, '_')+1));
    $apl = AplDB::getInstance();
    $ar = $apl->updateDauftrAussBehaelterTypId($dauftrId,$value);
    // pokud jsem zmenil vytahnu si hmotnost noveho, aby mohl aktualizovat policko s hmotnosti prepravky
    if($ar>0){
        $behTypen = $apl->getBehaelterTypen();
        $behArray = array();
        foreach($behTypen as $poradi=>$radek){
            $behArray[$radek['id']]=$radek;
        }
        $behGewicht = $behArray[$value]['gewicht'];
    }
    else {
        $behGewicht = 0;
    }
    echo json_encode(array('dauftrid'=>$dauftrId,'affectedrows'=>$ar,'behgewicht'=>$behGewicht,'auss'=>1));
?>