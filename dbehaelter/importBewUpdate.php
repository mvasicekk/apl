<?
require_once '../db.php';

    $id = $_POST['id'];
    $import = $_POST['value'];

    $apl = AplDB::getInstance();

    $auftragArray = $apl->getAuftragInfoArray($import);

    $bewegungsArray = $apl->getBehBewFuerImEx($import,0);

    $kunde = $auftragArray[0]['kunde'];

    // pripravit obsah divu pro zobrazeni prehledu behaelteru a poctu kusu

    if($bewegungsArray!==NULL){
        $behBewTableContent = "<table id='behtablecontent' class='posledni_table'>";
        $behBewTableContent.= '<tr class="posledni_table_header">';
        $behBewTableContent.= '<th>behaelternr</th>';
        $behBewTableContent.= '<th>beh text</th>';
        $behBewTableContent.= '<th>zustand_id</th>';
        $behBewTableContent.= '<th>inhalt_id</th>';
        $behBewTableContent.= '<th>stk</th>';
        $behBewTableContent.= '<th>datum</th>';
        $behBewTableContent.= '<th>&nbsp;</th>';
        $behBewTableContent.= '</tr>';
        $radek=0;
        foreach($bewegungsArray as $bewegung){
            if($radek%2==0)
                $behBewTableContent.="<tr class='sudy'>";
            else
                $behBewTableContent.="<tr class='lichy'>";
            $behBewTableContent.="<td>".$bewegung['behaelternr']."</td>";
            $behBewTableContent.="<td>".$bewegung['behtext']."</td>";
            $behBewTableContent.="<td>".$bewegung['zustand_id']."-".$bewegung['zustandtext']."</td>";
            $behBewTableContent.="<td>".$bewegung['inhalt_id']."-".$bewegung['inhalttext']."</td>";
            $behBewTableContent.="<td style='text-align:right;'>".$bewegung['stk']."</td>";
            $behBewTableContent.="<td style='text-align:right;'>&nbsp;".$bewegung['datum']."</td>";
            $behBewTableContent.="<td style='text-align:center;'>"."<input id='delbehbew_".$bewegung['id']."' type='button' value='-' acturl='./delBehBew.php'/>"."</td>";
            $behBewTableContent.="</tr>";
            $radek++;
        }
        $behBewTableContent.= "</table>";
    }
    echo json_encode(array(
                            'id'=>$id,
                            'auftragArray'=>$auftragArray,
                            'bewegungsArray'=>$bewegungsArray,
                            'kunde'=>$kunde,
                            'behtablecontent'=>$behBewTableContent,
        ));

?>
