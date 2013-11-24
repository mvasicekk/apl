<?
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');
$s = date('s');

// posledni oktet adresy
$poa = substr($ip, strrpos($ip, '.')+1);

$a = AplDB::getInstance();

$tA = array(
        't1'=>'',
        't2'=>'',
        't3'=>'',
	't4'=>'',
	't5'=>'',
        );

$textArray = $a->getInfoTabloTextArray($ip);

//var_dump($textArray);

if($textArray!==NULL){
//    $tA['t1']=$textArray[0]['text1']."  (IP$poa.$s)";
    $tA['t1']=$textArray[0]['text1'];
    $tA['t2']=$textArray[0]['text2'];
    $tA['t3']=$textArray[0]['text3'];
    $tA['t4']=$textArray[0]['text4'];
    $tA['t5']=$textArray[0]['text5'];
//    if(strlen(trim($tA['t3']))==0) $tA['t3']=$dt;
}
else{
    $tA['t1']='Abydos s.r.o.';
//    $vzkd = $a->getVzKdProDatum(date('Y-m-d'));
//    $tA['t2']=  number_format($vzkd, 0,',',' ');
    $tA['t2']='Infopanel';
    $tA['t3']='not set !';
    $tA['t4']=$ip;
    $tA['t5']=$dt;
}
    
 $value = array('t1'=>$tA['t1'],'t2'=>$tA['t2'],'t3'=>$tA['t3'],'t4'=>$tA['t4'],'t5'=>$tA['t5']);
 
 echo json_encode($value);
