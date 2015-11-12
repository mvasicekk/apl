<?

require_once '../db.php';
require_once './commons.php';

$id = $_POST['id'];


$apl = AplDB::getInstance();
$a = $apl;

$rundlaufid = substr($id, strpos($id, '_')+1);
$sectionA = getLkwFormDivs($rundlaufid);
$exCount = $sectionA['exCount'];
$imCount = $sectionA['imCount'];
 $ab_aby_soll_dateVorschlag = $sectionA['ab_aby_soll_dateVorschlag'];
    $ab_aby_soll_timeVorschlag = $sectionA['ab_aby_soll_timeVorschlag'];
    $an_aby_soll_dateVorschlag = $sectionA['an_aby_soll_dateVorschlag'];
    $an_aby_soll_timeVorschlag = $sectionA['an_aby_soll_timeVorschlag'];


$returnArray = array(
    'exCount'=>$exCount,
    'imCount'=>$imCount,
    'ab_aby_soll_date_vorschlag'=>$ab_aby_soll_dateVorschlag,
    'ab_aby_soll_time_vorschlag'=>$ab_aby_soll_timeVorschlag,
    'an_aby_soll_date_vorschlag'=>$an_aby_soll_dateVorschlag,
    'an_aby_soll_time_vorschlag'=>$an_aby_soll_timeVorschlag,
    'rundlaufid'=>$rundlaufid,

);

echo json_encode($returnArray);

