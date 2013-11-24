<?
require_once '../db.php';

    $id = $_POST['id'];

    $apl = AplDB::getInstance();

    
    $adressId = substr($id, strpos($id, '_')+1);
    
    $ar = $apl->getAdressArray($adressId);
    $aIk = $apl->getAdresyInKategorien($adressId);
    $aikString = "";
    $aikArray = array();
    if($aIk!==NULL){
	foreach ($aIk as $a){
	    array_push($aikArray, $a['kategorie']);
	}
	$aikString = implode(",", $aikArray);
    }
    
    $katStrMaxLength = 50;
    $tecky = strlen($aikString)>$katStrMaxLength?'...':'';
    $aikString = "&nbsp;".substr($aikString, 0, $katStrMaxLength).$tecky;
    
    $formDiv = "<div id='editform'>";
    $formDiv.= "<form>";
    
    $formDiv.="<fieldset>";
    $formDiv.="<legend>1</legend>";
    $formDiv.="<label for='suchbegriff'>Suchbegriff</label>";
    $formDiv.= "<input  id='suchbegriff' name='suchbegriff' type='text' value='".$ar['suchbegriff']."' maxlength='255' size='10'/>";
    
    $formDiv.="<label for='code'>Code</label>";
    $formDiv.= "<input id='code' name='code' type='text' value='".$ar['code']."' maxlength='2' size='2'/>";
    
    $formDiv.= "<input acturl='./showKategorien.php?id=".$adressId."' id='kategorien' type='button' value='Kategorien'/><span id='katseznam'>$aikString</span>";
    $formDiv.="</fieldset>";
    
    $formDiv.="<fieldset>";
    $formDiv.="<legend>Firma</legend>";
    $formDiv.="<label for='firma'>Firma</label>";
    $formDiv.= "<input id='firma' name='firma' type='text' value='".$ar['firma']."' maxlength='255' size='30'/>";
    
    $formDiv.="<label for='ansprechpartner'>Ansprechpartner</label>";
    $formDiv.= "<input id='ansprechpartner' name='ansprechpartner' type='text' value='".$ar['ansprechpartner']."' maxlength='255' size='40'/>";
    $formDiv.="</fieldset>";
    
    $formDiv.="<fieldset>";
    $formDiv.="<legend>Kontakt</legend>";
    $formDiv.="<div>";
    $formDiv.="<label for='name'>Name</label>";
    $formDiv.= "<input id='name' name='name' type='text' value='".$ar['name']."' maxlength='255' size='40'/>";
    
    $formDiv.="<label for='vorname'>Vorname</label>";
    $formDiv.= "<input id='vorname' name='vorname' type='text' value='".$ar['vorname']."' maxlength='255' size='40'/>";
    $formDiv.="</div>";
    $formDiv.="<div>";
    $formDiv.="<label for='funktion'>Funktion</label>";
    $formDiv.= "<input id='funktion' name='funktion' type='text' value='".$ar['funktion']."' maxlength='255' size='30'/>";
    $formDiv.="<label for='geboren1'>geboren</label>";
    $formDiv.= "<input class='datepicker' id='geboren1' name='geboren1' type='text' value='".$ar['geboren1']."' maxlength='10' size='10'/>";
    $formDiv.="</div>";
    $formDiv.="<div>";
    $formDiv.="<label for='telefon'>Telefon</label>";
    $formDiv.= "<input id='telefon' name='telefon' type='text' value='".$ar['telefon']."' maxlength='255' size='32'/>";
    
    $formDiv.="<label for='telefonprivat'>Telefon privat</label>";
    $formDiv.= "<input id='telefonprivat' name='telefonprivat' type='text' value='".$ar['telefonprivat']."' maxlength='255' size='32'/>";
    
    $formDiv.="<label for='fax'>Fax</label>";
    $formDiv.= "<input id='fax' name='fax' type='text' value='".$ar['fax']."' maxlength='255' size='32'/>";
    
    $formDiv.="<label for='handy'>Handy</label>";
    $formDiv.= "<input id='handy' name='handy' type='text' value='".$ar['handy']."' maxlength='255' size='32'/>";
    
    $formDiv.="<label for='email'>Email</label>";
    $formDiv.= "<input id='email' name='email' type='text' value='".$ar['email']."' maxlength='50' size='40'/>";

    $formDiv.="</div>";
    $formDiv.="<div>";
    $formDiv.="<label for='strasse'>Strasse</label>";
    $formDiv.= "<input id='strasse' name='strasse' type='text' value='".$ar['strasse']."' maxlength='255' size='30'/>";
    
    $formDiv.="<label for='ort'>Ort</label>";
    $formDiv.= "<input id='ort' name='ort' type='text' value='".$ar['ort']."' maxlength='255' size='30'/>";
    
    $formDiv.="<label for='plz'>PLZ</label>";
    $formDiv.= "<input id='plz' name='plz' type='text' value='".$ar['plz']."' maxlength='50' size='10'/>";
    $formDiv.="</div>";
    $formDiv.="</fieldset>";
    
    $formDiv.="<fieldset>";
    $formDiv.="<legend>Sonst</legend>";
    $formDiv.="<label for='sonstiges'>Sonstiges</label>";
    $formDiv.= "<input id='sonstiges' name='sonstiges' type='text' value='".$ar['sonstiges']."' maxlength='255' size='50'/>";
    
    $formDiv.="<label for='vyber'>Auswahl</label>";
    $formDiv.= "<input id='vyber' name='vyber' type='checkbox' value='' maxlength='' size=''/>";
    $formDiv.="</fieldset>";
    
    
    $formDiv.= "<input id='save_".$adressId."' type='button' acturl='./saveAdress.php' value='Speichern'/>";
    $formDiv.= "<input id='savenew_".$adressId."' type='button' acturl='./savenewAdress.php' value='Speichern als Neu'/>";
    $formDiv.= "<input id='abbr' type='button' value='Abbrechen'/>";
    
    $formDiv.= "</form>";
    $formDiv.= "</div>";
    
    echo json_encode(array(
                            'id'=>$id,
			    'adressId'=>$adressId,
			    'formDiv'=>$formDiv,
    ));
?>
