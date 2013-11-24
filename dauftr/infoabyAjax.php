<?
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');

$a = AplDB::getInstance();

$tA = array(
        't1'=>'',
        't2'=>'',
        't3'=>'',
        );

$textArray = $a->getInfoTabloTextArray($ip);

//var_dump($textArray);

if($textArray!==NULL){
    $tA['t1']=$textArray[0]['text1'];
    $tA['t2']=$textArray[0]['text2'];
    $tA['t3']=$textArray[0]['text3'];
    if(strlen(trim($tA['t3']))==0) $tA['t3']=$dt;
}
else{
    $tA['t1']='Keine Info';
    $vzkd = $a->getVzKdProDatum(date('Y-m-d'));
    $tA['t2']=  number_format($vzkd, 0,',',' ');
    $tA['t3']=$ip;
}
    
 $value = array('t1'=>$tA['t1'],'t2'=>$tA['t2'],'t3'=>$tA['t3']);
 
 echo json_encode($value);
