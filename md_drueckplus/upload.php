<?php
require_once '../db.php';

$att = $_POST['att'];
$persnr = $_POST['persnr'];


$a = AplDB::getInstance();

    
$gdatPath = "/mnt/gdat/Dat/";
if($att=='dokument'){
    $targetDir = AplDB::$GDatPath . 'Aby 18 Mitarbeiter -/02 Arbeitsverhaltnis - Pr.smlouvy,dodatky,skonceni PP/08 Slozky_novych_MA/'."$persnr"."/";
}
else{
    $targetDir = AplDB::$GDatPath . 'Aby 18 Mitarbeiter -/02 Arbeitsverhaltnis - Pr.smlouvy,dodatky,skonceni PP/08 Slozky_novych_MA/'."$persnr"."/"."$att"."/";
}


// 5 minutes execution time
@set_time_limit(5 * 60);

// Get parameters
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
$fileName = $_FILES['file']['name'];
//$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

function toAscii($s)
     {
         $s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s);
         $s = strtr($s, '`\'"^~', "\x01\x02\x03\x04\x05");
         if (ICONV_IMPL === 'glibc') {
             $s = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', $s); // intentionally @
             $s = strtr($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
                 . "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
                 . "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
                 . "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe\x96",
                 "ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt-");
         } else {
             $s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s); // intentionally @
         }
         $s = str_replace(array('`', "'", '"', '^', '~'), '', $s);
         return strtr($s, "\x01\x02\x03\x04\x05", '`\'"^~');
     }

function webalize($s, $charlist = NULL, $lower = TRUE)
     {
         $s = toAscii($s);
         if ($lower) {
             $s = strtolower($s);
         }
         $s = preg_replace('#[^a-z0-9' . preg_quote($charlist, '#') . ']+#i', '-', $s);
         $s = trim($s, '-');
         return $s;
     }

$fileName = trim(webalize($fileName, '.', FALSE), '.-');
if($att=='foto'){
    $fileName = "ma_foto.jpg";
}
$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// pokud existuje, smazu
if ($att == 'foto') {
    if (file_exists($filePath)) {
	unlink($filePath);
	//i nahled
	@unlink($targetDir . DIRECTORY_SEPARATOR . '.thumbs/' . $fileName);
    }
}

// Create target dir
if (!file_exists($targetDir)){
    umask(0000);
    @mkdir($targetDir,0777,TRUE);
}

move_uploaded_file( $_FILES['file']['tmp_name'] , $filePath );

