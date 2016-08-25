<?
session_start();
require_once '../db.php';

function toDBDate($t){
    $d = date('Y-m-d',  strtotime($t));
    if($d=="1970-01-01"){
	return NULL;
    }
    else{
	return $d;
    }
}

$data = file_get_contents("php://input");

$o = json_decode($data);
$rekl = $o->rekl;
$beenden = $o->beenden;
$apl = AplDB::getInstance();
$updatedRows = -1;

$user = $_SESSION['user'];

//sanace obsahu v promennych, prirazeni k nazvum sloupcu v DB
$reklId = $rekl->id;
$field2Value = array(
    'rekl_nr' => trim($rekl->rekl_nr), //*
    'kd_rekl_nr' => trim($rekl->kd_rekl_nr), //*
    'kd_kd_rekl_nr' => trim($rekl->kd_kd_rekl_nr), //*
    'kunde' => intval(trim($rekl->kunde)), //*
//    'import' => trim($rekl->import),
    'export' => trim($rekl->export), //*
    'export_beh' => trim($rekl->export_beh), //*
    'rekl_datum' => toDBDate($rekl->rekl_datum1), //*
    'rekl_erledigt_am' => toDBDate($rekl->rekl_erledigt_am1), //*
    'teil' => trim($rekl->teil), //*
    'stk_expediert' => intval($rekl->stk_expediert), //*
    'stk_reklammiert' => intval($rekl->stk_reklammiert), //*
    'ppm' => intval($rekl->ppm), //*
    'beschr_abweichung' => trim($rekl->beschr_abweichung), //*
    'beschr_ursache' => trim($rekl->beschr_ursache), //*
    'beschr_beseitigung' => trim($rekl->beschr_beseitigung), //*
    'zeichnungsnummer' => trim($rekl->zeichnungsnummer), //*
    'zeichnungsstand' => trim($rekl->zeichnungsstand), //*
    'team_bespr_name1' => trim($rekl->team_bespr_name1), //*
    'team_bespr_name2' => trim($rekl->team_bespr_name2), //*
    'team_bespr_name3' => trim($rekl->team_bespr_name3), //*
    'team_bespr_name4' => trim($rekl->team_bespr_name4), //*
    'team_bespr_name5' => trim($rekl->team_bespr_name5), //*
    'team_bespr_abteilung1' => trim($rekl->team_bespr_abteilung1), //*
    'team_bespr_abteilung2' => trim($rekl->team_bespr_abteilung2), //*
    'team_bespr_abteilung3' => trim($rekl->team_bespr_abteilung3), //*
    'team_bespr_abteilung4' => trim($rekl->team_bespr_abteilung4), //*
    'team_bespr_abteilung5' => trim($rekl->team_bespr_abteilung5), //*
    'team_bespr_leiter1' => intval($rekl->team_bespr_leiter1), //*
    'team_bespr_leiter2' => intval($rekl->team_bespr_leiter2), //*
    'team_bespr_leiter3' => intval($rekl->team_bespr_leiter3), //*
    'team_bespr_leiter4' => intval($rekl->team_bespr_leiter4), //*
    'team_bespr_leiter5' => intval($rekl->team_bespr_leiter5), //*
    'gefordert_8D' => intval($rekl->gefordert_8D), //*
    'termin_8D' => toDBDate($rekl->termin_8D1), //*
    'gesendet_am_8D' => toDBDate($rekl->gesendet_am_8D1), //*
    'report8D_3a' => trim($rekl->report8D_3a), //*
    'report8D_3b' => trim($rekl->report8D_3b), //*
    'report8D_3c' => trim($rekl->report8D_3c), //*
    'report8D_3a_einsatzdatum' => toDBDate($rekl->report8D_3a_einsatzdatum1), //*
    'report8D_3b_einsatzdatum' => toDBDate($rekl->report8D_3b_einsatzdatum1), //*
    'report8D_3c_einsatzdatum' => toDBDate($rekl->report8D_3c_einsatzdatum1), //*
    'report8D_4_erstmalig' => intval($rekl->report8D_4_erstmalig), //*
    'report8D_4_wiederholfehler' => intval($rekl->report8D_4_wiederholfehler), //*
    'report8D_5' => trim($rekl->report8D_5), //*
    'report8D_5a' => trim($rekl->report8D_5a), //*
    'report8D_6a' => trim($rekl->report8D_6a), //*
    'report8D_6b' => trim($rekl->report8D_6b), //*
    'report8D_6c' => trim($rekl->report8D_6c), //*
    'report8D_6a_einsatzdatum' => toDBDate($rekl->report8D_6a_einsatzdatum1), //*
    'report8D_6b_einsatzdatum' => toDBDate($rekl->report8D_6b_einsatzdatum1), //*
    'report8D_6c_einsatzdatum' => toDBDate($rekl->report8D_6c_einsatzdatum1), //*
    'report8D_7a' => trim($rekl->report8D_7a), //*
    'report8D_7b' => trim($rekl->report8D_7b), //*
    'report8D_7c' => trim($rekl->report8D_7c), //*
    'report8D_7b_einsatzdatum' => toDBDate($rekl->report8D_7b_einsatzdatum1), //*
    'report8D_7c_einsatzdatum' => toDBDate($rekl->report8D_7c_einsatzdatum1), //*
    'report8D_7a_einsatzdatum' => toDBDate($rekl->report8D_7a_einsatzdatum1), //*
    'interne_bewertung' => intval($rekl->interne_bewertung), //*
    'anerkannt_stk_ausschuss' => intval($rekl->anerkannt_stk_ausschuss), //*
    'anerkannt_stk_nacharbeit' => intval($rekl->anerkannt_stk_nacharbeit), //*
    'dif_falsch_deklariert_stk' => intval($rekl->dif_falsch_deklariert_stk), //*
    'verpackung_stk' => intval($rekl->verpackung_stk), //*
    'kreislauf_stk' => intval($rekl->kreislauf_stk), //*
    'unklar_stk' => intval($rekl->unklar_stk), //*
    'anerkannt_stk_nein' => intval($rekl->anerkannt_stk_nein), //*
    'analyse_erhalten_am' => toDBDate($rekl->analyse_erhalten_am1),
    'analyse_erledigt_am' => toDBDate($rekl->analyse_erledigt_am1),
    'analyse_nichtanerkant_stk' => intval($rekl->analyse_nichtanerkant_stk),
    'analyse_anerkant_stk' => intval($rekl->analyse_anerkant_stk),
//'strafe_persnr'=>,
//'strafe_wert'=>,
    'erstellt' => trim($rekl->erstellt),
    'bemerkung' => trim($rekl->bemerkung),
    'stempel' => trim($rekl->stempel), //*
    'pragestempel' => trim($rekl->pragestempel), //*
//'andere_kosten'=>,
    'giesstag' => trim($rekl->giesstag), //*
    'mt_fax' => intval($rekl->mt_fax),
    'mt_email' => intval($rekl->mt_email), //*
    'mt_brief' => intval($rekl->mt_brief),
    'mt_telefon' => intval($rekl->mt_telefon), //*
    'mt_mund' => intval($rekl->mt_mund), //*
    'bearbeiter_kunde' => trim($rekl->bearbeiter_kunde), //*
    'mt_datum' => toDBDate($rekl->mt_datum1), //*
//'negativ_muster'=>,
    'identif_hinweise' => trim($rekl->identif_hinweise),
    'letzt_geandert' => $user,
//'kd_kd_kd_rekl_nr'=>,
//'stk_unklar'=>,
    "anerkannt_ausschuss_beleg" => trim($rekl->anerkannt_ausschuss_beleg),
    "anerkannt_ausschuss_preis_eur" => floatval(strtr($rekl->anerkannt_ausschuss_preis_eur, ',', '.')),
    "anerkannt_nacharbeit_beleg" => trim($rekl->anerkannt_nacharbeit_beleg),
    "anerkannt_nacharbeit_preis_eur" => floatval(strtr($rekl->anerkannt_nacharbeit_preis_eur, ',', '.')),
    "dif_falsch_deklariert_beleg" => trim($rekl->dif_falsch_deklariert_beleg),
    "dif_falsch_deklariert_preis_eur" => floatval(strtr($rekl->dif_falsch_deklariert_preis_eur, ',', '.')),
    "verpackung_beleg" => trim($rekl->verpackung_beleg),
    "verpackung_preis_eur" => floatval(strtr($rekl->verpackung_preis_eur, ',', '.')),
    "kreislauf_beleg" => trim($rekl->kreislauf_beleg),
    "kreislauf_preis_eur" => floatval(strtr($rekl->kreislauf_preis_eur, ',', '.')),
    "pauschale_beleg" => trim($rekl->pauschale_beleg),
    "pauschale_preis_eur" => floatval(strtr($rekl->pauschale_preis_eur, ',', '.')),
    'forecast_anerkannt_ausschuss_eur'  => floatval(strtr($rekl->forecast_anerkannt_ausschuss_eur, ',', '.')),
    'forecast_anerkannt_nacharbeit_eur'  => floatval(strtr($rekl->forecast_anerkannt_nacharbeit_eur, ',', '.')),
    'forecast_dif_falsch_deklariert_eur'  => floatval(strtr($rekl->forecast_dif_falsch_deklariert_eur, ',', '.')),
    'forecast_verpackung_eur'  => floatval(strtr($rekl->forecast_verpackung_eur, ',', '.')),
    'forecast_kreislauf_eur'  => floatval(strtr($rekl->forecast_kreislauf_eur, ',', '.')),
    'forecast_pauschale_eur'  => floatval(strtr($rekl->forecast_pauschale_eur, ',', '.')),
    'klaren_bis' => toDBDate($rekl->klaren_bis1), //*
    'wider_am' => toDBDate($rekl->wider_am1), //*
    "wider_per" => trim($rekl->wider_per),
);

// v pripade ze se ukoncuje reklamace
if(intval($beenden)!=0){
    $field2Value['abgeschlossen'] = $user;
}
// vytvorit updatovaci dotaz
// mam reklId -> je to update
if($reklId>0){
    $sql.="update dreklamation set ";
    foreach ($field2Value as $fieldName=>$fieldValue){
	if($fieldValue===NULL){
	    $sql.="`$fieldName`=NULL";
	}
	else{
	    $sql.="`$fieldName`='$fieldValue'";
	}
	$sql.=",";
    }
    $sql = substr($sql, 0, strlen($sql)-1);
    $sql.=" where id='$reklId' limit 1";
    $updatedRows = $apl->query($sql);
}

$returnArray = array(
	'ar'=>$updatedRows,
	"insertId"=>$insertId,
	"field2Value"=>$field2Value,
	"reklId"=>$reklId,
	"objdata"=>$o,
	"sql"=>$sql,
	"beenden"=>$beenden,
	"user"=>$user,
    );
    
    echo json_encode($returnArray);
