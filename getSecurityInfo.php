<?php
session_start();
require_once './db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);


$a = AplDB::getInstance();

$form_id=$o->form_id;

$user = $_SESSION['user'];
$userpc = $a->get_user_pc();
$rolesArray = $a->getUserRolesArray($user);
$showArray = array();
$editArray = array();

//security
$elementsIdArray = $a->getResourcesForFormId($form_id);
$display_sec = array();
if ($elementsIdArray !== NULL) {
    foreach ($elementsIdArray as $elementId) {
	$showArray[$elementId] = $a->getDisplaySec($form_id, $elementId, $user);
	$editArray[$elementId] = $a->getPrivilegeSecFull($form_id, $elementId, $user,"schreiben");
    }
}

$securityInfo = array(
    'form_id'=>$form_id,
    'user'=>$user,
    'userpc'=>$userpc,
    'roles'=>$rolesArray,
    'showArray'=>$showArray,
    'editArray'=>$editArray,
);

$returnArray = array(
    'securityInfo'=>$securityInfo,
);

echo json_encode($returnArray);
