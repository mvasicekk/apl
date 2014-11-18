<?
 session_start();
?>
<?
include "../fns_dotazy.php";
require_once '../db.php';

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

	// stranka je kodovana v utf8, tak chci vysledky z databaze taky v utf8 , protoze je mam ulozene v cp1250
	mysql_query('set character_set_results = utf8');

	$teil = $_GET['teil'];
	if(isset($_GET['teil']))
	{
		$sql="select * from dkopf where (teil='".$_GET['teil']."')";
		$res=mysql_query($sql);
		$row=mysql_fetch_array($res);
		$smarty->assign("teil_value",$row['Teil']);
		$smarty->assign("kunde_value",$row['Kunde']);
		$smarty->assign("teillang_value",$row['teillang']);
		$smarty->assign("bezeichnung_value",$row['Teilbez']);
		$smarty->assign("gew_value",$row['Gew']);
		$smarty->assign("brgew_value",$row['BrGew']);
		$smarty->assign("wst_value",$row['Wst']);
		$smarty->assign("fa_value",$row['FA']);
		$smarty->assign("jb_value",$row['JB']);
		$smarty->assign("vm_value",$row['verpackungmenge']);
                $smarty->assign("spg_value",$row['stk_pro_gehaenge']);
		$smarty->assign("reklamation_value",$row['Reklamation']);
		$smarty->assign("letzte_reklamation_value",$row['Letzte-Reklamation']);
		$smarty->assign("bemerk_value",$row['bemerk']);
		$smarty->assign("art_guseisen_value",$row['Art Guseisen']);
                $smarty->assign("preis_stk_gut_value",$row['preis_stk_gut']);
                $smarty->assign("preis_stk_auss_value",$row['preis_stk_auss']);
		$smarty->assign("schwierigkeitsgrad_S11_value",$row['schwierigkeitsgrad_S11']);
		$smarty->assign("schwierigkeitsgrad_S51_value",$row['schwierigkeitsgrad_S51']);
		$smarty->assign("schwierigkeitsgrad_SO_value",$row['schwierigkeitsgrad_SO']);
		
		// nove hodnoty jahresbedarf
		// jb_lfd_2 = aktualni rok - 2
		// jb_lfd_1 = aktualni rok - 1
		// jb_lfd_j = aktualni rok
		// gut_lfd_1 = dobre exportovane kusy aktualni rok - 1
		
                $smarty->assign("jb_lfd_2_value",$row['jb_lfd_2']);
                $smarty->assign("jb_lfd_1_value",$row['jb_lfd_1']);
                $smarty->assign("jb_lfd_j_value",$row['jb_lfd_j']);
		$smarty->assign("jb_lfd_plus_1_value",$row['jb_lfd_plus_1']);
                $smarty->assign("restmengen_verw_value",$row['restmengen_verw']);
                $smarty->assign("fremdauftr_dkopf_value",$row['fremdauftr_dkopf']);
                $smarty->assign("status_value",$row['status']);
		
		if(strlen($row['Muster-vom'])>=10)
			$muster_vom_value=substr($row['Muster-vom'],8,2).".".substr($row['Muster-vom'],5,2).".".substr($row['Muster-vom'],0,4);
		else
			$muster_vom_value=$row['Muster-vom'];
		$smarty->assign("muster_vom_value",$muster_vom_value);
		
		$smarty->assign("muster_platz_value",$row['Muster-Platz']);
		
		if(strlen($row['Muster-vorher-vom'])>=10)
			$muster_vorher_vom_value=substr($row['Muster-vorher-vom'],8,2).".".substr($row['Muster-vorher-vom'],5,2).".".substr($row['Muster-vorher-vom'],0,4);
		else
			$muster_vorher_vom_value=$row['Muster-vorher-vom'];
		$smarty->assign("muster_vorher_vom_value",$muster_vorher_vom_value);
		
		
		if(strlen($row['Muster-Freigabe-1-vom'])>=10)
			$muster_freigabe1_vom_value=substr($row['Muster-Freigabe-1-vom'],8,2).".".substr($row['Muster-Freigabe-1-vom'],5,2).".".substr($row['Muster-Freigabe-1-vom'],0,4);
		else
			$muster_freigabe1_vom_value=$row['Muster-Freigabe-1-vom'];
		$smarty->assign("muster_freigabe1_vom_value",$muster_freigabe1_vom_value);
		
		if(strlen($row['Muster-Freigabe-2-vom'])>=10)
			$muster_freigabe2_vom_value=substr($row['Muster-Freigabe-2-vom'],8,2).".".substr($row['Muster-Freigabe-2-vom'],5,2).".".substr($row['Muster-Freigabe-2-vom'],0,4);
		else
			$muster_freigabe2_vom_value=$row['Muster-Freigabe-2-vom'];
		$smarty->assign("muster_freigabe2_vom_value",$muster_freigabe2_vom_value);
		
		
		
		$smarty->assign("vom1_selected",$row['Muster-Freigabe-1']);
		$smarty->assign("vom2_selected",$row['Muster-Freigabe-2']);
		
		$sql="select name from mustervom order by name";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$vom1_options[$row['name']]=$row['name'];
		}
		$smarty->assign("vom1_options",$vom1_options);
		$smarty->assign("vom2_options",$vom1_options);
 

		// priradim seznam lagru
		$smarty->assign("lager_selected","0D");
		
		$sql="select lager from dlager order by lager";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$lagry_options[$row['lager']]=$row['lager'];
		}
		$smarty->assign("lagry",$lagry_options);

  
		// vytahnout informace o pracovnim planu
		$sql="select dpos_id,`kz-druck` as kz_druck,mittel,`TaetNr-Aby` as taetnr,`TaetBez-Aby-D` as bez_d,
		`TaetBez-Aby-T` as bez_t,`VZ-min-kunde` as vzkd,`VZ-min-aby` as vzaby,
		KzGut,bedarf_typ,lager_von,lager_nach from dpos where (teil='".$_GET['teil']."') order by taetnr asc,stamp desc";
		
		$res=mysql_query($sql);
		while($dpos_row=mysql_fetch_array($res))
		{
			$dpos_rows[$dpos_row['dpos_id']]=$dpos_row;
		}
		$smarty->assign("dpos",$dpos_rows);
		
		// zjistim pocet radku v teildoku pro dany dil
		$a = AplDB::getInstance();
		$teilDokuArray = $a->getTeilDokuArray($teil);
		$smarty->assign("pocet_teildoku",count($teilDokuArray));
		// zjistit zda ma dil nejake prilohy
		// plus upravim cestu k souborum , aby byly pouzitelne i na webu
		// mam vytvoreny aslias /kunden/ na \\abyserver\Dat\Dat\11 Kunden , takze tuto cast odriznout a nahradit cestou kunden/
		
		$sql="select id_attachment,teil,attachment_typ,CONCAT('/kunden/',SUBSTRING(REPLACE(attachment_path,'\\\','/'),31)) as attachment_path,stamp,beschreibung from dkopf_attachment where (teil='".$_GET['teil']."') order by stamp desc";
		$res=mysql_query($sql);
		$pocet_priloh=0;
		while($dattach_row=mysql_fetch_array($res))
		{
			$dattach_rows[$dattach_row['id_attachment']]=$dattach_row;
			$pocet_priloh++;
		}
		$smarty->assign("dattach",$dattach_rows);
		$smarty->assign("pocet_priloh",$pocet_priloh);
		
		//zjistim posledni reklamace k dilu
		$letzteReklamationen = $a->getLetzteReklamationString($teil,5);
		$smarty->assign("letzte_reklamationen",$letzteReklamationen);
		$letzteReklamationArray = $a->getLetzteReklamation($teil);
		$smarty->assign("letzte_reklamationen_array",$letzteReklamationArray);
		
		
		//security
		$elementsIdArray = array(
		    'kunde_sec',
		    "teillang_sec",
		    "status_sec",
		    'bezeichnung_sec',
		    "gew_sec",
		    "brgew_sec",
		    "wst_sec",
		    "fa_sec",
		    "vm_sec",
		    "spg_sec",
		    "restmengen_verw_sec",
		    "letzterekl_sec",
		    "bemerk_sec",
		    "art_guseisen_sec",
		    "preis_stk_gut_sec",
		    "preis_stk_auss_sec",
		    "fremdauftr_dkopf_sec",
		    "jbvor_sec",
		    "jbfuture_sec",
		    "schwierigkeitsgrad_S11_sec",
		    "schwierigkeitsgrad_S51_sec",
		    "schwierigkeitsgrad_SO_sec",
		    "showteildoku_sec",
		    "showlagerzettel_sec",
		    "showvpm_sec",
		    "showima_sec",
		    "showmittel_sec",
		    "show_att_muster",
		    "show_att_empb",
		    "show_att_ppa",
		    "show_att_gpa",
		    "show_att_vpa",
		    "show_att_qanf",
		    "show_att_rekl",
		    "dposedit",
		    "teilsuchen_sec",
		    "posneu_sec",
		    "D510info_sec",
		    "teilsave_sec",
		    "kzdruck_sec",
		    "dposvzkd_sec",
		    "apl_table",
		);
		$puser = $_SESSION['user'];
		foreach ($elementsIdArray as $elementId){
		    $display_sec[$elementId] = $a->getDisplaySec('dkopf',$elementId,$puser)?'inline-block':'none';
		    $edit_sec[$elementId] = $a->getPrivilegeSec('dkopf',$elementId,$puser,"schreiben")?'':'readonly="readonly"';
		}
		$smarty->assign("display_sec",$display_sec);
		$smarty->assign("edit_sec",$edit_sec);
	}
	$smarty->display('dkopf.tpl');
?>
