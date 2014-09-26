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
$panelydiv = '<div id="panelytable2">';
$panelydiv.="<table id='lagertable12'>";
if($panelyRows!==NULL){
    
    $panelydiv.="<tr>";
    
    $panelId = 34;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelId = 31;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
    
    $panelId = 30;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 29;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 28;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 27;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 26;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 25;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 24;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 23;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 22;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 21;
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
    $panelydiv.="<td class='ulicka' colspan='12'>&nbsp;";$panelydiv.="</td>";
    $panelydiv.="</tr>";
    
    // spodni rada
    $panelydiv.="<tr>";
    
    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";
    
    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelId = 40;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelId = 37;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 36;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";
	
    $panelId = 35;
    $panelydiv.="<td class='lagesplatz'>";
    $panelydiv.="<input class='text1' acturl='./saveInfoPanelText.php' id='text1_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text1']."' size='$text1len' maxlength='$text1maxlen'/>";
    $panelydiv.="<br><input class='text2' acturl='./saveInfoPanelText.php' id='text2_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text2']."' size='$text2len' maxlength='$text2maxlen'/>";
    $panelydiv.="<br><input class='text3' acturl='./saveInfoPanelText.php' id='text3_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text3']."' size='$text3len' maxlength='$text3maxlen'/>";
    $panelydiv.="<br><input class='text4' acturl='./saveInfoPanelText.php' id='text4_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text4']."' size='$text4len' maxlength='$text4maxlen'/>";
    $panelydiv.="<br><input class='text5' acturl='./saveInfoPanelText.php' id='text5_".$panelyRows[$panelId-1]['itid']."' type='text' value='".$panelyRows[$panelId-1]['text5']."' size='$text5len' maxlength='$text5maxlen'/>";
    $panelydiv.="</td>";

    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelId = 0;
    $panelydiv.="<td class='noplatz'>";
    $panelydiv.="&nbsp;";
    $panelydiv.="</td>";

    $panelydiv.="</tr>";
}
else{
    $panelydiv.="<tr><td>No panels defined !!!</td></tr>";
}
$panelydiv.="</table>";
$panelydiv.="</div>";
 $value = array('divcontent'=>$panelydiv,'ip'=>$ip,'dt'=>$dt,'id'=>$id);
 
 echo json_encode($value);
