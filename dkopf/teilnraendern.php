<?
 session_start();
?>
<?
include "../fns_dotazy.php";
include "../db.php";

dbConnect();
require("../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promenne uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}

	// stranka je kodovana v utf8, tak chci vysledky z databaze taky v utf8 , protoze je mam ulozene v cp1250
	mysql_query('set character_set_results = utf8');

        if(isset($_GET['teil'])){
            $smarty->assign('teilOld',$_GET['teil']);
        }
        
        if(isset($_POST['teilNew']) && isset($_POST['go']) && $_POST['go']==1){
            // pokus o zmenu dilu
            $smarty->assign('zprava',"menim dil");
            $smarty->assign('teilOld',$_POST['teilOld']);
            $smarty->assign('teilNew',$_POST['teilNew']);
        }

	$smarty->display('teilnraendern.tpl');
?>
