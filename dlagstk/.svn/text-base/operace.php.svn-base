<?php
mysql_connect('172.16.1.111', 'root', 'nuredv');
mysql_select_db('apl');
mysql_query('SET CHARACTER SET CP1250');

$sqlTeil = "select * from dlagerstk where Teil='".$_GET['cislo']."';";
$resTeil = mysql_query($sqlTeil) or die(mysql_error());


$D = 0;
$Ra = 0;
$T = 0; 
$P = 0; 
$Rb = 0;
$K = 0;
$Q = 0;
$F = 0; 
$E = 0; 
$X = 0; 
$Rc = 0;
$A2 = 0;
$A4 = 0;
$A6 = 0; 
$B2 = 0;
$B4 = 0;
$B6 = 0;
$C4 = 0;
$XX = 0;
$XY = 0;
$l8V = 0;
$l9V = 0;
$l0S = 0;


while($teil = mysql_fetch_array($resTeil)){
Switch ($teil["lager"]){
    case "0D": $D = $teil["stk"]; break;
//    case "0S": $l0S = $teil["stk"]; break;
    case "1R": $Ra = $teil["stk"]; break;
    case "2T": $T = $teil["stk"]; break;
    case "3P": $P = $teil["stk"]; break;
    case "4R": $Rb = $teil["stk"]; break;
    case "5K": $K = $teil["stk"]; break;
    case "5Q": $Q = $teil["stk"]; break;
    case "6F": $F = $teil["stk"]; break;
    case "8E": $E = $teil["stk"]; break;
	case "8V": $l8V = $teil["stk"]; break;
    case "8X": $X = $teil["stk"]; break;
    case "9R": $Rc = $teil["stk"]; break;
	case "9V": $l9V = $teil["stk"]; break;
    case "A2": $A2 = $teil["stk"]; break;
    case "A4": $A4 = $teil["stk"]; break;
    case "A6": $A6 = $teil["stk"]; break;
    case "B2": $B2 = $teil["stk"]; break;
    case "B4": $B4 = $teil["stk"]; break;
    case "B6": $B6 = $teil["stk"]; break;
    case "C4": $C4 = $teil["stk"]; break;
    case "XX": $XX = $teil["stk"]; break;
    case "XY": $XY = $teil["stk"]; break;
  }
$datum = $teil["datum_inventur"];
}

echo "vypis('$D','$l0S', '$Ra', '$T', '$P', '$Rb', '$K', '$Q', '$F', '$E','$l8V', '$X', '$Rc','$l9V', '$A2', '$A4','$A6', '$B2', '$B4', '$B6', '$C4', '$XX', '$XY', '$datum');";
?>          
            
