<?
require_once '../../security.php';
?>
<?
include "../../fns_dotazy.php";
dbConnect();
require("../../libs/Smarty.class.php");
$smarty = new Smarty;

	// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
	if(isset($_SESSION['user'])&&isset($_SESSION['level']))
	{
		$smarty->assign("user",$_SESSION['user']);
		$smarty->assign("level",$_SESSION['level']);
		$smarty->assign("prihlasen",1);
	}


	if(isset($_GET['auftragsnr']))
	{
		mysql_query('set names utf8');
		// vytahnout navrh pro vyplneni exportu
		$auftragsnr=$_GET['auftragsnr'];
		$sql="SELECT dauftr.AuftragsNr, dauftr.`pos-pal-nr` as pal, dauftr.Teil,dauftr.`MehrArb-KZ` as tatkz,";
                $sql.=" dauftr.abgnr, Sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) AS gut_stk,";
		$sql.=" sum(if(auss_typ=2,`auss-Stück`,0)) as auss2,";
		$sql.=" sum(if(auss_typ=4,`auss-Stück`,0)) as auss4,";
		$sql.=" sum(if(auss_typ=6,`auss-Stück`,0)) as auss6,id_dauftr as id,dauftr.kzgut,dauftr.termin,dauftr.`Stück` as im_stk";
		$sql.=" FROM dauftr LEFT JOIN drueck ON (dauftr.abgnr = drueck.TaetNr) ";
		$sql.=" AND (dauftr.`pos-pal-nr` = drueck.`pos-pal-nr`) AND (dauftr.Teil = drueck.Teil) AND (dauftr.AuftragsNr = drueck.AuftragsNr)";
		$sql.=" where (((dauftr.AuftragsNr)='$auftragsnr') and (`auftragsnr-exp` is null))";
		$sql.=" GROUP BY dauftr.AuftragsNr, dauftr.`pos-pal-nr`, dauftr.Teil,dauftr.`MehrArb-KZ`, dauftr.abgnr";
		$sql.=" order by dauftr.AuftragsNr,pal,dauftr.Teil,dauftr.abgnr";
//                echo $sql;
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res))
		{
			$rows[$row['id']]=$row;
		}
		$smarty->assign("fullen",$rows);
		$smarty->assign("sql",$sql);
		$smarty->assign("auftragsnr",$_GET['auftragsnr']);
		$smarty->display('export_fuellen.tpl');
	}
?>
