<?
require_once '../security.php';
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

	$table_id = $_GET['table_id'];
	$place_id = $_GET['place_id'];
	$place = $_GET['place'];
	
	$a = AplDB::getInstance();
	
	$table_infoArray = $a->getInfoTabloTextArray(NULL, $table_id);
	if($table_infoArray!==NULL)
	    $table_info = $table_infoArray[0];
	
	$smarty->assign('place_id',$place_id);
	$smarty->assign('place',$place);
	$smarty->assign('table',$table_info);
	$smarty->display('panel.tpl');
?>
