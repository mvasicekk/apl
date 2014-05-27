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
	
	$smarty->display('umbuchung.tpl');
//		// zjistim pocet radku v teildoku pro dany dil
//		$a = AplDB::getInstance();
//		$teilDokuArray = $a->getTeilDokuArray($teil);
//		$smarty->assign("pocet_teildoku",count($teilDokuArray));
//		// zjistit zda ma dil nejake prilohy
//		// plus upravim cestu k souborum , aby byly pouzitelne i na webu
//		// mam vytvoreny aslias /kunden/ na \\abyserver\Dat\Dat\11 Kunden , takze tuto cast odriznout a nahradit cestou kunden/
//		
//		$sql="select id_attachment,teil,attachment_typ,CONCAT('/kunden/',SUBSTRING(REPLACE(attachment_path,'\\\','/'),31)) as attachment_path,stamp,beschreibung from dkopf_attachment where (teil='".$_GET['teil']."') order by stamp desc";
//		$res=mysql_query($sql);
//		$pocet_priloh=0;
//		while($dattach_row=mysql_fetch_array($res))
//		{
//			$dattach_rows[$dattach_row['id_attachment']]=$dattach_row;
//			$pocet_priloh++;
//		}
//		$smarty->assign("dattach",$dattach_rows);
//		$smarty->assign("pocet_priloh",$pocet_priloh);
//		
//		//zjistim posledni reklamace k dilu
//		$letzteReklamationen = $a->getLetzteReklamationString($teil,5);
//		$smarty->assign("letzte_reklamationen",$letzteReklamationen);
//		$letzteReklamationArray = $a->getLetzteReklamation($teil);
//		$smarty->assign("letzte_reklamationen_array",$letzteReklamationArray);
//		
//		
//		//security
//		$elementsIdArray = array(
//		    "show_att_muster",
//		    "show_att_empb",
//		    "show_att_ppa",
//		    "show_att_gpa",
//		    "show_att_vpa",
//		    "show_att_qanf",
//		    "show_att_zeit",
//		    "show_att_liefer",
//		    "show_att_mehr",
//		    "show_att_rekl",
//		    "teillang_sec",
//		);
//		$puser = $_SESSION['user'];
//		foreach ($elementsIdArray as $elementId){
//		    $display_sec[$elementId] = $a->getDisplaySec('dkopf',$elementId,$puser)?'inline-block':'none';
//		}
//		$smarty->assign("display_sec",$display_sec);
//	}
	
?>
