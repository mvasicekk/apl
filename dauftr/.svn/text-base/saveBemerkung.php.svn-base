<?
require_once '../db.php';

    $id = $_POST['id'];
    $value = trim($_POST['value']);
    $auftrag = $_POST['auftrag'];
    $a = AplDB::getInstance();


    $ar = $a->updateDaufkopfField('bemerkung', $value, $auftrag);

    echo json_encode(array(
                            'affectedrows'=>$ar,
                            'bemerkung'=>$value,
        ));
?>
