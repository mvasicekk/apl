<?
require_once './reportsSetup.php';

// vytisk tabulky

$qArray = $apl->getSchlTabellenArray(NULL,'%');
//AplDB::varDump($qArray);
$querys = array();
if($qArray!==NULL){
    foreach ($qArray as $q){
	$key = $q['tabid'];
	$q['showButton'] = TRUE;
	// kontrola pristupu
	// 1. individualni pristup
	$benutzerArray = $q['benutzer_access'];
	if(strlen(trim($benutzerArray))>0){
	    $bA = split(',', $benutzerArray);
	    if(FALSE===($as=array_search($puser, $bA))){
		$q['showButton']=FALSE;
	    } 
	}
	else{
	    $roleArray = $q['role_access'];
	    if(strlen(trim($roleArray))>0){
		$rA = split(',', $roleArray);
		$urA = $apl->getUserRolesArray($puser);
		if($urA===NULL){
		    //nema zadne role ->nema zadny pristup podle roli
		    $q['showButton']=FALSE;
		}
		else{
		    $roleFound = 0;
		    foreach ($urA as $ur){
			$roleId = $ur['role_id'];
			if(array_search($roleId, $rA)!==FALSE){
			    $roleFound=1;
			    break;
			}
		    }
		    if($roleFound==0){
			$q['showButton']=FALSE;
		    }
		}
	    }
	}
	$querys[$key] = $q;
    }
}

foreach ($querys as $key=>$a){
    $querys[$key]['sql'] = base64_encode($a['sql']);
    $querys[$key]['icon'] = strlen(trim($a['popisky']))>0?'glyphicon-modal-window':'glyphicon-th-list';
}

//AplDB::varDump($querys);
$smarty->assign("querys",$querys);
$smarty->display('st.tpl');
