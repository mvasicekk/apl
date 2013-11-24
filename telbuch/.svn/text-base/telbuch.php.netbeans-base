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

	//mysql_query("set names utf8");
	
        $apl = AplDB::getInstance();

        
	$adressenTable = '';
        $smarty->assign("adressenTable",$adressenTable);

        $smarty->display('telbuch.tpl');
?>
