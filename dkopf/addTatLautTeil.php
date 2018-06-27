<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $teil = $_POST['teil'];
    $lautteil = $_POST['lautteil'];
    $user = get_user_pc();
    
    $apl = AplDB::getInstance();
    
    $insArray = array();
    
    $teilDPOSArray = $apl->getQueryRows("select Teil,KzGut,`TaetNr-Aby` as abgnr,`TaetBez-Aby-D` as tat_d,`TaetBez-Aby-T` as tat_t,mittel,`VZ-h` as vzh,`VZ-min-kunde` as vzkd,`vz-min-aby` as vzaby,`kz-druck` as kzdruck,lager_von,lager_nach,sorting,bedarf_typ from dpos where teil='$lautteil'");
    $counter = 0;
    if($teilDPOSArray!==NULL){
	foreach ($teilDPOSArray as $tat){
	    $i = "insert into dpos";
	    $i.=" (Teil,KzGut,`TaetNr-Aby`,`TaetBez-Aby-D`,`TaetBez-Aby-T`,`VZ-min-kunde`,`vz-min-aby`,`kz-druck`,lager_von,lager_nach,sorting,bedarf_typ)";
	    $i.=" values(";
	    $i.="'".$teil."',";
	    $i.="'".$tat['KzGut']."',";
	    $i.="'".$tat['abgnr']."',";
	    $i.="'".$tat['tat_d']."',";
	    $i.="'".$tat['tat_t']."',";
	    $i.="'".$tat['vzkd']."',";
	    $i.="'".$tat['vzaby']."',";
	    $i.="'".$tat['kzdruck']."',";
	    $i.="'".$tat['lager_von']."',";
	    $i.="'".$tat['lager_nach']."',";
	    $i.="'".$tat['sorting']."',";
	    $i.="'".$tat['bedarf_typ']."'";
	    $i.=")";
	    array_push($insArray, $i);
	    $ar = $apl->insert($i);
	}
    }
    

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    
    $ar = 0;
    
    //$ar = $apl->addTeilVPM($teil,$n_vpm_nr,$n_anzahl,$n_bemerkung,$user);
    
    $returnArray = array(
	'insArray'=>$insArray,
	'ar'=>$counter,
	'id'=>$id,
	'teil'=>$teil,
	'lautteil'=>$lautteil,
    );
    echo json_encode($returnArray);

?>

