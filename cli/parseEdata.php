#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';

$a = AplDB::getInstance();

// najit nejnovejsi xml soubor v adresari
$path = "/home/runt/edata";

$latest_ctime = 0;
$latest_filesize = 0;
$latest_filename = '';

$d = dir($path);
while (false !== ($entry = $d->read())) {
    $filepath = "{$path}/{$entry}";
    // could do also other checks than just checking whether the entry is a file
    if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
        $latest_ctime = filectime($filepath);
        $latest_filename = $entry;
        $latest_filesize = filesize($filepath);
    }
}

foreach($entry as $file){
    
}
// now $latest_filename contains the filename of the newest file
// najit posledni zpracovany log file podle databaze
$lastParsedFileArrayFromDB = $a->getLastParsedEdataFile();
$lastParsedFileFromDB = $lastParsedFileArrayFromDB['filename'];
$lastParsedFileSizeFromDB = intval($lastParsedFileArrayFromDB['size']);

echo "latest filename : $latest_filename ($latest_filesize), latest Parsed File from DB : $lastParsedFileFromDB ($lastParsedFileSizeFromDB)\n";

if ((trim($latest_filename) != trim($lastParsedFileFromDB)) || ($latest_filesize!=$lastParsedFileSizeFromDB)) {
    //parsuju soubor a ulozim informace do DB
    $filePath = "{$path}/{$latest_filename}";
    echo "filePath: $filePath\n";
    $dom = new DOMDocument;
    $dom->load($filePath);
    if (!$dom) {
        echo "chyba pri parsovani dokumentu\n";
        exit;
    }
   
// casove servery
//IP: 212.65.193.4
//IP: 212.65.242.210
//IP: 147.228.57.10
//IP: 147.228.52.11
//IP: 62.24.64.9
//IP: 193.85.3.51
//IP: 81.95.96.3
// projit vsechny elementy event
// <event> </event>
// // vzdy pro  jeden import
// a nacist atributy class, id, time = ve formatu unix timestamp, type, address, badgenumber = cislo karty, reason = nemusi byt u vsech typu udalosti, persno = osobni cislo zamestnance
// projit event elementy a vybrat zajimave info
    $events = $dom->getElementsByTagName('event');
    foreach ($events as $event) {
        $class = $event->getAttribute('class');
        $idevent = $event->getAttribute('id');
        $time = $event->getAttribute('time');
        $datetime = date('Y-m-d H:i:s', intval($time));
        $type = $event->getAttribute('type');
        $address = $event->getAttribute('address');
	// misto badgenumber je ted jen number
        $badgenumber = $event->getAttribute('number');
        $reason = $event->getAttribute('reason');
        $persnr = intval($event->getAttribute('persno'));

        echo "class=$class, idevent=$idevent, time=$time, datetime=$datetime, type=$type, address=$address, badgenumber=$badgenumber, reason=$reason\n";
        $insertedRows = $a->insertEdataEvent($class, $idevent, $time, $datetime, $type, $address, $badgenumber, $reason, $persnr);
//    echo "insertedRows = $insertedRows\n";
    }
    // ukladani do logu jde dolu
    // 
    //nakonec ulozim info o parsovani do edatalogs
    echo "vkladam $latest_filename do DB\n";
    $info = $a->insertLastEdataFile(trim($latest_filename),$latest_filesize);
    echo "info=$info\n";
}
?>
