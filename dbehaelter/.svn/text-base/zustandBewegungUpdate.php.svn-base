<?
require_once '../db.php';

    $id = $_POST['id'];
    $zustandId = $_POST['value'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();


    $zustandArray = $apl->getBehaelterStandArray(1);

    //vyzkousim zda existuje  takovy zustand_id
    $zustand_id = 0;
    $zustand_text = '';

    $found = 0;
    foreach ($zustandArray as $zustand){
        $zustand_id = $zustand['zustand_id'];
        $zustand_text = $zustand['zustand_text'];
        if($zustandId==$zustand_id){
            $found = 1;
            break;
        }
    }

    echo json_encode(array(
                            'id'=>$id,
                            'found'=>$found,
                            'zustand_id'=>$zustand_id,
                            'zustand_text'=>$zustand_text,
        ));
?>
