<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$att = $o->att;
$teil = $o->teil;

$a = AplDB::getInstance();

$att2FolderArray = AplDB::$ATT2FOLDERARRAY;
    
$kunde = $a->getKundeFromTeil($teil);
$kundeGdatPath = $a->getKundeGdatPath($kunde);
$ppaDir='';
$gdatPath = "/mnt/gdat/Dat/";
// seznam dilu
if ($kundeGdatPath !== NULL) {
    $ppaDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teil . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
    $extensions = 'JPG|jpg|pdf|txt';
    $filter = "/.*.($extensions)$/";
    $docsArray = $a->getFilesForPath($ppaDir,$filter);
    // pro obrazky zkusim vygenerovat nahledy pro rychlejsi zobrazeni na strance
    // slozka pro thumbnaily
    if(!file_exists($ppaDir."/.thumbs")){
	@mkdir($ppaDir."/.thumbs");
    }
    if($docsArray!==NULL){
	foreach ($docsArray as $index=>$doc){
	    if(!file_exists($ppaDir."/.thumbs/".$doc['filename']) && $doc['ext']=='JPG'){
		$img = new Imagick($ppaDir."/".$doc['filename']);
		$img->thumbnailimage(200, 200, TRUE);
		$img->writeimage($ppaDir."/.thumbs/".$doc['filename']);
	    }
	}
    }
}


$returnArray = array(
    'att'=>$att,
    'dir'=>$ppaDir,
    'docsArray'=>$docsArray,
    );
    
echo json_encode($returnArray);
