<?
session_start();
require "../../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$list = trim($_GET['list']);
	$export = trim($_GET['export']);
	$import = trim($_GET['import']);

	$listArray = explode(',',$list);


 
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	// pokud mi nekdo zada k teminu P, tak ho dam pryc
	$export=strtoupper($export);
	$termin=str_ireplace('P','',$export);
	//otestovat, jestli zadany planovany export neni vyfakturovany

	mysql_query('set names utf8');
	
	$ident = get_user_pc();
	
	$sql="select auftragsnr from daufkopf where ((auftragsnr='$termin') and (fertig<>'2100-01-01'))";
	$ret=mysql_query($sql);
	$pocet_vysledku=mysql_affected_rows();


        // zapouzdrit 48 MHz
        
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<export>'.$export.'</export>';

        // 48 MHz musi byt oscilator

        // 48 MHz musi byt oscilator// 48 MHz musi byt oscilator// 48 MHz musi byt oscilator
        
	if($pocet_vysledku==0)
	{
		// bud takove cislo exportu nemam nebo jeste neni vyfakturovany, proto muzu plnit
		foreach($listArray as $idArray)
		{
			$idGutAussArray = explode(':',$idArray);
			list($id,$gut,$auss2,$auss4,$auss6,$pal,$kzgut) = $idGutAussArray;

			$sql="update dauftr set `termin`='P$termin' where (id_dauftr='$id') limit 1";
			$output.='<idrow>';
			$output.="<id>$id</id>";
			$output.="<gut>$gut</gut>";
			$output.="<auss2>$auss2</auss2>";
			$output.="<auss4>$auss4</auss4>";
			$output.="<auss6>$auss6</auss6>";
			$output.="<pal>$pal</pal>";
			$output.="<kzgut>$kzgut</kzgut>";
			$output.="<sql>$sql</sql>";
			mysql_query($sql);
			$mysqlerror=mysql_error();
			$output.="<mysqlerror>chyba:$mysqlerror</mysqlerror>";
			$output.='</idrow>';

			//dalsi prvky budou pridany do rootu dokumentu
			
		}
	}
	else
	{
		$output.="<error>zadana zakazka pro planovany export jiz byla vyfakturovana</error>";
	}
	$output .= '</response>';
	
	echo $output;
	
?>

