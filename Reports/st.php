<?

require_once './reportsSetup.php';

// vytisk tabulky
$form_typ = '%';
$form_typ = $_GET['form_typ'];
$qArray = $apl->getSchlTabellenArray(NULL, $form_typ);
$typCountArray = array();
//AplDB::varDump($qArray);
$querys = array();
if ($qArray !== NULL) {
    foreach ($qArray as $q) {
	$key = $q['tabid'];
	$q['showButton'] = FALSE;
	// kontrola pristupu
	// 1. individualni pristup
	$benutzerArray = $q['benutzer_access'];
	if (strlen(trim($benutzerArray)) > 0) {
	    $bA = split(',', $benutzerArray);
	    //if(FALSE===($as=array_search($puser, $bA))){
	    if (FALSE !== ($as = array_search($puser, $bA))) {
		//$q['showButton']=FALSE;
		$q['showButton'] = TRUE;
	    }
	}

	$roleArray = $q['role_access'];
	if (strlen(trim($roleArray)) > 0) {
	    $rA = split(',', $roleArray);
	    $urA = $apl->getUserRolesArray($puser);
	    $roleFound = 0;
	    foreach ($urA as $ur) {
		$roleId = $ur['role_id'];
		if (array_search($roleId, $rA) !== FALSE) {
		    $roleFound = 1;
		    break;
		}
	    }
	    if($roleFound>0){
		$q['showButton'] = TRUE;
	    }
	}

	$querys[$key] = $q;
    }
}

foreach ($querys as $key => $a) {
    // mozna nepredavat prekodovane sql, ale pouze tabid - javascriptem bych si vytahnul sql dotaz samostatne
    $querys[$key]['sql'] = base64_encode($a['sql']);
    $querys[$key]['icon'] = strlen(trim($a['popisky'])) > 0 ? 'glyphicon-modal-window' : 'glyphicon-th-list';
    if ($querys[$key]['showButton'] == TRUE) {
	$typCountArray[$a['form_typ']] += 1;
    }
}
//AplDB::varDump($typCountArray);
//AplDB::varDump($querys);
$smarty->assign("querys", $querys);
$smarty->assign("typcountarray", $typCountArray);
$smarty->display('st.tpl');
