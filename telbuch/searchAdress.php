<?
require_once '../db.php';

    $id = $_POST['id'];
    $search = $_POST['value'];

    $apl = AplDB::getInstance();

    $adressenArray = NULL;
    $adressenCount = 0;

    if(strlen(trim($search))>1){
        $adressenArray = $apl->getAdressen($search);
        $adressenCount = count($adressenArray);
    }

    
    // vytvoreni XML soubroru podle poli DB
    // vzber potrebnych poli z DB
    // pripravit obsah divu pro zobrazeni prehledu behaelteru a poctu kusu
    if($adressenArray!==NULL){
        $behBewTableContent = "<table id='adressentablecontent' class='posledni_table'>";
        $behBewTableContent.= '<tr class="posledni_table_header">';
        $behBewTableContent.= '<th>firma</th>';
        $behBewTableContent.= '<th>ansprechpartner</th>';
        $behBewTableContent.= '<th>name</th>';
        $behBewTableContent.= '<th>telefon</th>';
        $behBewTableContent.= '<th>telefonprivat</th>';
        $behBewTableContent.= '<th>fax</th>';
        $behBewTableContent.= '<th>handy</th>';
        $behBewTableContent.= '<th>email</th>';
        $behBewTableContent.= '<th>ort, strasse</th>';
//        $behBewTableContent.= '<th>ort, strasse repeat</th>';
        $behBewTableContent.= '<th colspan="2">&nbsp;</th>';
        $behBewTableContent.= '</tr>';
        $radek=0;
        foreach($adressenArray as $bewegung){
            if($radek%2==0)
                $behBewTableContent.="<tr id='adressrow_".$bewegung['adresy_id']."' class='sudy'>";
            else
                $behBewTableContent.="<tr id='adressrow_".$bewegung['adresy_id']."' class='lichy'>";
            $behBewTableContent.="<td>".$bewegung['firma']."</td>";
            $behBewTableContent.="<td>".$bewegung['ansprechpartner']."</td>";
            $behBewTableContent.="<td>".$bewegung['name']."</td>";
            $behBewTableContent.="<td>".$bewegung['telefon']."</td>";
            $behBewTableContent.="<td>".$bewegung['telefonprivat']."</td>";
            $behBewTableContent.="<td>".$bewegung['fax']."</td>";
            $behBewTableContent.="<td>".$bewegung['handy']."</td>";
            $behBewTableContent.="<td>".$bewegung['email']."</td>";
            
            if(strlen(trim($bewegung['strasse']))>0)
                $behBewTableContent.="<td>".$bewegung['ort'].', '.$bewegung['strasse']."</td>";
            else
                $behBewTableContent.="<td>".$bewegung['ort']."</td>";
            
//            $behBewTableContent.="<td>REP.".$bewegung['ort']."</td>";
            $behBewTableContent.="<td style='text-align:center;'>";
	    $behBewTableContent.="<input id='editadress_".$bewegung['adresy_id']."' type='button' value='...' acturl='./editAdress.php'/>";
	    $behBewTableContent.="</td>";
	    $behBewTableContent.="<td style='text-align:center;'>";
	    $behBewTableContent.="<input id='deladress_".$bewegung['adresy_id']."' type='button' value='-' acturl='./delAdress.php'/>";
	    $behBewTableContent.="</td>";
            
            $behBewTableContent.="</tr>";
            $radek++;
        }
        $behBewTableContent.= "</table>";
    }
    echo json_encode(array(
                            'id'=>$id,
                            'adressenCount'=>$adressenCount,
                            'content'=>$behBewTableContent,
        ));
?>
