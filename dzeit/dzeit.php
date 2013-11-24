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

    $res = mysql_query("select * from dtattypen where tat<>'a'");
//    $res = mysql_query("select * from dtattypen");
        $i = 0;
        while($row = mysql_fetch_array($res))
		{
			$tattypvalue[$i++]=$row['tat'];
			$tattypoutput[$i++]=$row['tat'];
		}
        
		
	// nastaveni pole s datumem
	// pokud dostanu hodnotu naposledy pouziteho datumu, tak ho nastavim na toto
	// jinak nastavim aktualni datum
	
	if(isset($_GET['lastdatum'])) 
		$smarty->assign("datumvalue",date('d.m.Y',strtotime($_GET['lastdatum'])));
	else
		$smarty->assign("datumvalue",date('d.m.Y'));

        // poslednich 5 zaznamu
        $sql = "select id,`PersNr` as persnr,DATE_FORMAT(`Datum`,'%d.%m.%Y') as datum,FORMAT(`Stunden`,2) as stunden,`Schicht` as schicht,tat as oe, DATE_FORMAT(anw_von,'%H:%i') as von,DATE_FORMAT(anw_bis,'%H:%i') as bis,FORMAT(pause1,2) as pause1,FORMAT(pause2,2) as pause2,comp_user_accessuser as user,stamp from dzeit order by stamp desc limit 5";
        $res = mysql_query($sql);
        $items = array();
        while($row = mysql_fetch_assoc($res)){
            array_push($items, $row);
        }

        mysql_close();

        $akt_den=date("d");
$akt_mesic=date("m");
$akt_rok=date("Y");

// posledni datum minuleho mesice ziskam jako den 0 aktualniho mesice

$lastday = mktime(0,0,0,$akt_mesic,0,$akt_rok);
$firstday = mktime(0,0,0,date('m',$lastday),1,$akt_rok);

$min_mesic_od=date('d.m.Y',$firstday);
$min_mesic_do=date('d.m.Y',$lastday);

$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $akt_mesic, $akt_rok);
$tagbis = $pocetDnuVMesici;

$prvniDenAktualnihoRoku = date('d.m.Y',mktime(1,1,1,1,1,$akt_rok));
$prvniDenAktualnihoMesice = date('d.m.Y',mktime(1,1,1,$akt_mesic,1,$akt_rok));
$dnes = date('d.m.Y');

$predchozi_den=date('d.m.Y',mktime(0,0,0,$akt_mesic,$akt_den-1,$akt_rok));

        $smarty->assign("lastrows",$items);
	$smarty->assign("tattypvalue",$tattypvalue);
	$smarty->assign("tattypoutput",$tattypoutput);
        $smarty->assign("predchozi_den",$predchozi_den);

	$smarty->display('dzeit.tpl');
?>
