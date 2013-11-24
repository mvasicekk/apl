<?

require_once '../db.php';

$id = $_POST['id'];
$kunde = $_POST['value'];
$behaelterNr = $_POST['behnr'];
$datum = $_POST['datum'];

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$apl = AplDB::getInstance();

$invDivContent = '';

$datumDB = $apl->make_DB_datum($datum);
if (strlen($datum) == 0)
    $datumDB = NULL;

$kundeInfoArray = $apl->getKundeInfoArray($kunde);
if (strlen(trim($behaelterNr)) > 0) {
    $artikelArray = $apl->getEinkArtikelArray($behaelterNr);
}

if (($kundeInfoArray != NULL) && ($artikelArray != NULL)) {
    // vytahnu pocty kusu v inventure
    $stkArray = $apl->getBehaelterInventurStArray($behaelterNr, $kunde, $datumDB);
}

if (($kundeInfoArray != NULL) && ($artikelArray != NULL)) {
    // vytahnu pocty kusu v inventure
    $invArray = $apl->getBehaelterInventurRows($behaelterNr, $kunde);
    
    if ($invArray !== NULL) {
        $behBewTableContent = "<table id='behtablecontent' class='posledni_table'>";
        $behBewTableContent.= '<tr class="posledni_table_header">';
        $behBewTableContent.= '<th>datum</th>';
        $behBewTableContent.= '<th>zustand_id</th>';
        $behBewTableContent.= '<th>inhalt_id</th>';
        $behBewTableContent.= '<th>platz</th>';
        $behBewTableContent.= '<th>stk</th>';
        $behBewTableContent.= '<th>&nbsp;</th>';
        $behBewTableContent.= '</tr>';
        $radek = 0;
        foreach ($invArray as $bewegung) {
            if ($radek % 2 == 0)
                $behBewTableContent.="<tr class='sudy'>";
            else
                $behBewTableContent.="<tr class='lichy'>";
            $behBewTableContent.="<td style='text-align:left;'>&nbsp;" . $bewegung['datumF'] . "</td>";
            $behBewTableContent.="<td style='text-align:left;'>&nbsp;" . $bewegung['zustand_id'] . "</td>";
            $behBewTableContent.="<td style='text-align:left;'>&nbsp;" . $bewegung['inhalt_id'] . "</td>";
            $behBewTableContent.="<td style='text-align:left;'>&nbsp;" . $bewegung['platz_id'] . "</td>";
            $behBewTableContent.="<td style='text-align:right;'>&nbsp;" . $bewegung['stk'] . "</td>";
            $behBewTableContent.="<td style='text-align:center;'>" . "<input id='delinv_" . $bewegung['id'] . "' type='button' value='-' acturl='./delBehInv.php'/>" . "</td>";
            $behBewTableContent.="</tr>";
            $radek++;
        }
        $behBewTableContent.= "</table>";
    }
}

$invDivContent = $behBewTableContent;

echo json_encode(array(
    'id' => $id,
    'kunde' => $kunde,
    'kundeInfoArray' => $kundeInfoArray,
    'artikelArray' => $artikelArray,
    'stkArray' => $stkArray
    , 'invArray' => $invArray
    , 'datumDB' => $datumDB
    , 'divContent' => $invDivContent
));
?>
