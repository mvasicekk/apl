<?
require_once '../security.php';
require("../libs/Smarty.class.php");
require_once '../db.php';

$smarty = new Smarty;
$a = AplDB::getInstance();

if (isset($_SESSION['user']) && isset($_SESSION['level'])) {
    $smarty->assign("user", $_SESSION['user']);
    $smarty->assign("level", $_SESSION['level']);
    $smarty->assign("prihlasen", 1);
    require_once '../assignsecurity.php';
} else {
    header("Location: ../index.php");
}

$spedArray = $a->getSpediteurArray();

//$im = $_GET['import'];
$spedOptions = array();
foreach ($spedArray as $sped){
    $spedOptions[$sped['id']] = $sped['name'];
}

$spedOptions['*'] = "Alle Speditionen";
$smarty->assign('spedOptions',$spedOptions);
$smarty->assign('spedSelected','*');

$smarty->display('editRundlauf.tpl');