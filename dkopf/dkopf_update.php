<?
require_once '../db.php';

    $id = $_POST['id'];
    $value = $_POST['value'];
    $teil = $_POST['teil'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    // puvodne jahrbedarf_stk_2011 2012 a 2013
    
    if($id=="jb_lfd_2") $value = intval($value);
    if($id=="jb_lfd_1") $value = intval($value);
    if($id=="jb_lfd_plus_1") $value = intval($value);
    if($id=="jb_lfd_j") $value = intval($value);
    if($id=="preis_stk_gut") $value = floatval(strtr($value, ',', '.'));
    if($id=="preis_stk_auss") $value = floatval(strtr($value, ',', '.'));

    $ar = $apl->updateDkopfField($id, $value, $teil);

    echo json_encode(array(
                            'id'=>$id,
                            'value'=>$value,
                            'teil'=>$teil,
                            'ar'=>$ar
        ));

?>
