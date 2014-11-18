<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $imarray = $_POST['imarray'];
    $imarray_g = $_POST['imarray_g'];
    $imarray_a = $_POST['imarray_a'];
    $imarray_gem = $_POST['imarray_gem'];
    $palarray = $_POST['palarray'];
    $dauftrid = $_POST['dauftrid'];
    $dauftrid_g = $_POST['dauftrid_g'];
    $dauftrid_a = $_POST['dauftrid_a'];
    $dauftrid_gem = $_POST['dauftrid_gem'];
    $palarray_g = $_POST['palarray_g'];
    $palarray_gem = $_POST['palarray_gem'];
    $palarray_a = $_POST['palarray_a'];
    $tatarray = $_POST['tatarray'];
    $tatarray_g = $_POST['tatarray_g'];
    $tatarray_a = $_POST['tatarray_a'];
    $tatarray_gem = $_POST['tatarray_gem'];
    $ema_antrag_text = $_POST['ema_antrag_text'];
    $ema_genehmigt_bemerkung = $_POST['ema_genehmigt_bemerkung'];
    $ema_genehmigt_stamp = $_POST['ema_genehmigt_stamp'];
    $ema_genehmigt_user = $_POST['ema_genehmigt_user'];
    $bemerkungid = $_POST['bemerkungid'];
    

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();
    $field=FALSE;
    if(strstr($id,"selime")){
	$field="auftragsnrarray";
	$value = $imarray;
    }
    if(strstr($id,"selpale")){
	$field="palarray";
	$value = $palarray;
    }
    if(strstr($id,"seltate")){
	$field="tatundzeitarray";
	$value = $tatarray;
    }

    if(strstr($id,"ima_select_auftragsnr_gen")){
	$field="ima_auftragsnrarray_genehmigt";
	$value = $imarray_g;
    }

    
    if(strstr($id,"ema_select_auftragsnr_anf")){
	$field="ema_auftragsarray";
	$value = $imarray_a;
    }

    if(strstr($id,"ema_select_auftragsnr_gem")){
	$field="ema_auftragsarray_genehmigt";
	$value = $imarray_gem;
    }

    if(strstr($id,"selimanf")){
	$field="ema_auftragsarray";
	$value = $imarray_a;
    }

    if(strstr($id,"selimgem")){
	$field="ema_auftragsarray_genehmigt";
	$value = $imarray_gem;
    }

    if(strstr($id,"selimgen")){
	$field="ima_auftragsnrarray_genehmigt";
	$value = $imarray_g;
    }

    if(strstr($id,"ima_select_pal_gen")){
	$field="ima_palarray_genehmigt";
	$value = $palarray_g;
    }

    if(strstr($id,"ema_select_pal_anf")){
	$field="ema_palarray";
	$value = $palarray_a;
    }

    if(strstr($id,"ema_select_pal_gem")){
	$field="ema_palarray_genehmigt";
	$value = $palarray_gem;
    }

    if(strstr($id,"selpalgen")){
	$field="ima_palarray_genehmigt";
	$value = $palarray_g;
    }

    if(strstr($id,"selpalanf")){
	$field="ema_palarray";
	$value = $palarray_a;
    }

    if(strstr($id,"selpalgem")){
	$field="ema_palarray_genehmigt";
	$value = $palarray_gem;
    }

    if(strstr($id,"ima_select_tat_gen")){
	$field="ima_tatundzeitarray_genehmigt";
	$value = $tatarray_g;
    }

    if(strstr($id,"seltatgen")){
	$field="ima_tatundzeitarray_genehmigt";
	$value = $tatarray_g;
    }
    
    if(strstr($id,"ema_select_tat_anf")){
	$field="ema_tatundzeitarray";
	$value = $tatarray_a;
    }
    
    if(strstr($id,"ema_select_tat_gem")){
	$field="ema_tatundzeitarray_genehmigt";
	$value = $tatarray_gem;
    }

    if(strstr($id,"seltatanf")){
	$field="ema_tatundzeitarray";
	$value = $tatarray_a;
    }

    if(strstr($id,"seltatgem")){
	$field="ema_tatundzeitarray_genehmigt";
	$value = $tatarray_gem;
    }

    if(strstr($id,"ema_antrag_text")){
	$field="ema_antrag_text";
	$value = $ema_antrag_text;
    }

    if(strstr($id,"ema_genehmigt_bemerkung")){
	$field="ema_genehmigt_bemerkung";
	$value = $ema_genehmigt_bemerkung;
    }

    if(strstr($id,"ema_genehmigt_user")){
	$field="ema_genehmigt_user";
	$value = trim($ema_genehmigt_user);
    }

    if($id=="ima_dauftrid"){
	$field="ima_dauftrid_array";
	$value = $dauftrid;
    }

    if($id=="ima_dauftrid_e"){
	$field="ima_dauftrid_array";
	$value = $dauftrid;
    }
    
    if($id=="ima_dauftrid_gen"){
	$field="ima_dauftrid_array_genehmigt";
	$value = $dauftrid_g;
    }

    if($id=="ema_dauftrid_anf"){
	$field="ema_dauftrid_array";
	$value = $dauftrid_a;
    }

    if($id=="ema_dauftrid_gem"){
	$field="ema_dauftrid_array_genehmigt";
	$value = $dauftrid_gem;
    }

    if(strstr($id,"ema_genehmigt_stamp")){
	$field="ema_genehmigt_stamp";
	$value=NULL;
	if(strlen(trim($ema_genehmigt_stamp))>0){
	    $value = $apl->make_DB_datum($ema_genehmigt_stamp);
	}
	if($value=='') $value=NULL;
    }

    $imaid = substr($bemerkungid, strrpos($bemerkungid, '_')+1);
    if($field)
	$ar = $apl->updateIMAField($field,$value,$imaid);
    
    $returnArray = array(
	'field'=>$field,
	'ar'=>$ar,
	'id'=>$id,
	'imaid'=>$imaid,
	'value'=>$value,
	'user'=>$user,
	'imarray'=>$imarray,
	'palarray'=>$palarray,
	'tatarray'=>$tatarray,
	'bemerkungid'=>$bemerkungid,
    );
    echo json_encode($returnArray);

?>

