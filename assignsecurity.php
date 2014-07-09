<?
// jiz musi byt inkludovano db.php
global $smarty;
$apl = AplDB::getInstance();
//zjistit seznam roli pro uzivatele
$roles = '';
$puser = $_SESSION['user'];
$rolesArray = $apl->getUserRolesArray($puser);
if ($rolesArray !== NULL) {
    foreach ($rolesArray as $role) {
	$roles.=$role['rolename'] . ", ";
    }
    $roles = substr($roles, 0, strrpos($roles, ','));
}
$smarty->assign("roles", $roles);
?>

