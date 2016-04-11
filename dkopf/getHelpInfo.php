<?php
session_start();
require_once '../db.php';

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
	$hI = $a->getHelpInfo($form_id, $elementId, $user);
	$hIArray[$elementId] = $hI;
	if($hI!==NULL){
	    $showArray[$elementId] = nl2br($hI[0]['help_text']);
	}
	else{
	    $showArray[$elementId] = 'No Help !';
	}
    }
}

$securityInfo = array(
    'form_id'=>$form_id,
    'user'=>$user,
    'userpc'=>$userpc,
    'roles'=>$rolesArray,
    'helpText'=>$showArray,
    'hiArray'=>$hIArray, /* zde jsou vsechny potrebna informace, helpText je redund. */
);

$returnArray = array(
    'help'=>$securityInfo,
);

echo json_encode($returnArray);
