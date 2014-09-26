<?
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');
$id = $_POST['id'];
$a = AplDB::getInstance();

$text1len = 8;$text1maxlen=12;
$text2len = 8;$text2maxlen=12;
$text3len = 8;$text3maxlen=8;
$text4len = 8;$text4maxlen=12;
$text5len = 8;$text5maxlen=16;


$panelyRows = $a->getInfoTabloTextArray($ipKlienta);
$panelydiv = '<div id="panelytable">';
$panelydiv.="<table id='lagertable1'>";
if($panelyRows!==NULL){
    $panelydiv.="<tr>";
    
    $panelId = 11;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelId = 10;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
    
    $panelId = 9;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 8;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 7;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 6;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 5;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 4;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 3;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 2;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 33;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    
    $panelydiv.="</tr>";
    
    // ulicka
    $panelydiv.="<tr>";
    $panelydiv.="<td class='ulicka' colspan='11'>&nbsp;";$panelydiv.="</td>";
    $panelydiv.="</tr>";
    
    // spodni rada
    $panelydiv.="<tr>";
    
    $panelId = 41;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelId = 20;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 19;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 18;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 17;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 16;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 15;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 14;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 13;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 12;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelId = 39;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelydiv.="</tr>";
    
//    foreach ($panelyRows as $panel){
//        $panelydiv.="<tr>";
//        $panelydiv.="<td><strong>".$panel['idpanel']."</strong></td>";
//        $panelydiv.="<td>"."<input acturl='./saveInfoPanelText.php' id='text1_".$panel['itid']."' type='text' value='".$panel['text1']."' size='20' maxlength='20'"."</td>";
//        $panelydiv.="<td>"."<input acturl='./saveInfoPanelText.php' id='text2_".$panel['itid']."' type='text' value='".$panel['text2']."' size='6' maxlength='6'"."</td>";
//        $panelydiv.="</tr>";
//    }
}
else{
    $panelydiv.="<tr><td>No panels defined !!!</td></tr>";
}
$panelydiv.="</table>";
$panelydiv.="</div>";
 $value = array('divcontent'=>$panelydiv,'ip'=>$ip,'dt'=>$dt,'id'=>$id,'panelyRows'=>$panelyRows);
 
 echo json_encode($value);
