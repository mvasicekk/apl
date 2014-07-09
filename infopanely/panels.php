<?
 session_start();
?>
<?
require_once '../db.php';
require("../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}
	else{
	    header("Location: ../index.php");
	}

	$place_id = $_GET['place_id'];
	$place = $_GET['place'];
	
	$a = AplDB::getInstance();
	
	$panels = $a->getInfoPanelsForPlaceId($place_id);
	
	$smarty->assign('place_id',$place_id);
	$smarty->assign('place',$place);
	$smarty->assign('panels',$panels);
	$smarty->display('panels.tpl');
?>
