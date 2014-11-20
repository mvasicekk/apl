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

	//mysql_query("set names utf8");
	
        $export = $_GET['export'];
        $apl = AplDB::getInstance();

        $termin = "P".$export;
        $zeilen = $apl->getAuftragZeilenProTermin($termin);

        foreach ($zeilen as $poradi => $radek) {
            $istGewNetto = $radek['abywaage_brutto']-$radek['abywaage_behaelter_ist'];
//            if($radek['stk_laut_waage']==0)
//                $radek['stk_laut_waage'] = $radek['abywaage_kg_stk10']!=0?floor($istGewNetto/$radek['abywaage_kg_stk10']):0;
            $stk_laut_waage = $radek['stk_laut_waage'];
            //$sollGewBrutto = $radek['stkexp']*$radek['abywaage_kg_stk10']+$radek['abywaage_behaelter_ist'];
            $sollGewBrutto = $stk_laut_waage*$radek['abywaage_kg_stk10']+$radek['abywaage_behaelter_ist'];
            $radek['soll_kg_brutto'] = round($sollGewBrutto,2);
            $radek['ist_kg_netto'] = $istGewNetto;
            $radek['kunde_behaelter_bestellung_netto'] = round($radek['kg_stk_bestellung']*$radek['stkimport'],2);
            $importy[$radek['import']][$poradi]=$radek;
        }

        // popis, hmotnost, zkratka....
        // behaelter typen
        $behaelterTypen = $apl->getBehaelterTypen();

        $behaeltertypenValues = array();
        $behaeltertypenNames = array();

        array_push($behaeltertypenValues,9999);
        array_push($behaeltertypenNames,"-");

        foreach ($behaelterTypen as $key => $row) {
            array_push($behaeltertypenValues,$row['id']);
            array_push($behaeltertypenNames,$row['typ']);
        }

        // predani hodnot promennych do sablony
        
        $smarty->assign("behaeltertypenValues",  $behaeltertypenValues);
        $smarty->assign("behaeltertypenNames",  $behaeltertypenNames);
        $smarty->assign("importy",  $importy);
        $smarty->assign("export",  $export);
	
        $smarty->display('exporttablo.tpl');
?>
