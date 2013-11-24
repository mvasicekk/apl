<?
 session_start();
?>
<?
include "../fns_dotazy.php";
dbConnect();
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


    $res = mysql_query("select * from dlager order by Lager");
        $i = 0;
        while($row = mysql_fetch_array($res))
		{
			$lagervalue[$i++]=$row['Lager'];
			$lageroutput[$i++]=$row['Lager']." - ".$row['LagerBeschreibung'];
		}
        mysql_close();
		
	// nastaveni pole s datumem
	// pokud dostanu hodnotu naposledy pouziteho datumu, tak ho nastavim na toto
	// jinak nastavim aktualni datum
	
	if(isset($_GET['lastdatum'])) 
		$smarty->assign("datumvalue",date('d.m.Y',strtotime($_GET['lastdatum'])));
	else
		$smarty->assign("datumvalue",date('d.m.Y'));
		
	$smarty->assign("lagervalue",$lagervalue);
	$smarty->assign("lageroutput",$lageroutput);
	$smarty->display('dlager_zugang.tpl');
?>
