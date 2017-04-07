    <?php
    require_once '../db.php';

    $a = AplDB::getInstance();

    $data = file_get_contents("php://input");
    $o = json_decode($data);


    $persvon = $o->persvon;//$_GET['persvon'];
    $persbis = $o->persbis;//$_GET['persbis'];
    $jahr = $o->jahr;//$_GET['jahr'];
    $monat = $o->monat;//$_GET['monat'];
    
    $lohnArray = $a->getLohnArray($persvon,$persbis,$jahr,$monat);
    echo json_encode($lohnArray);

