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
	    $extPos = strrpos($doc['filename'], '.');
	    $thumbsFilename = $ppaDir."/.thumbs/".substr($doc['filename'],0,$extPos).'.jpg';
	    if(!file_exists($thumbsFilename) && ($doc['ext']=='JPG'||$doc['ext']=='PDF')){
		$img = new Imagick($ppaDir."/".$doc['filename'].'[0]');
		
		if($doc['ext']=='PDF'){
		    $img->setImageFormat('jpg');
		    $img = $img->flattenImages();
		}
		$img->thumbnailimage(200, 200, TRUE);
		if($doc['ext']=='PDF'){
		    $img->writeimage($thumbsFilename);
		    $doc['ext']=='JPG';
		}
		else{
		    $img->writeimage($thumbsFilename);
		}
	    }
	    $separatorPos = strrpos($doc['url'], '/');
	    $docsArray[$index]['thumburl'] = substr($doc['url'],0,$separatorPos)."/.thumbs/".substr($doc['filename'],0,$extPos).'.jpg';
	}
    }
}


$returnArray = array(
    'att'=>$att,
    'dir'=>$ppaDir,
    'docsArray'=>$docsArray,
    );
    
echo json_encode($returnArray);
