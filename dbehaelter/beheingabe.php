<?
require_once '../security.php';
?>
<?
require_once '../db.php';
require("../libs/Smarty.class.php");

$auftrag = $_GET['auftrag'];
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

        $smarty->assign('auftrag', $auftrag);
        $smarty->display('beheingabe.tpl');
?>
