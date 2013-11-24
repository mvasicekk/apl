<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Info Tablo
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="./infoaby.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>

<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="./infoaby.js"></script>


</head>

<body style='background-color: black;'>

    <table class='infotable'>
        <tr>
            <th colspan="4">
		Heute / dnes: {$datumdnes}
            </th>
	    <th colspan="4">
		Gestern / včera: {$datumvcera}
            </th>
        </tr>
	<tr>
	    <td class="rowcaption">PG1</td>
	    <td>{$abyinfo.dnes.pg1_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg1_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg1_verb|string_format:"%d"}</td>
	    <td class="rowcaption">PG1</td>
	    <td>{$abyinfo.vcera.pg1_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg1_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg1_verb|string_format:"%d"}</td>
	</tr>
	<tr>
	    <td class="rowcaption">PG3</td>
	    <td>{$abyinfo.dnes.pg3_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg3_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg3_verb|string_format:"%d"}</td>
	    <td class="rowcaption">PG3</td>
	    <td>{$abyinfo.vcera.pg3_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg3_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg3_verb|string_format:"%d"}</td>
	</tr>
	<tr>
	    <td class="rowcaption">PG4</td>
	    <td>{$abyinfo.dnes.pg4_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg4_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg4_verb|string_format:"%d"}</td>
	    <td class="rowcaption">PG4</td>
	    <td>{$abyinfo.vcera.pg4_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg4_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg4_verb|string_format:"%d"}</td>
	</tr>
	<tr>
	    <td class="rowcaption">PG9</td>
	    <td>{$abyinfo.dnes.pg9_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg9_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.pg9_verb|string_format:"%d"}</td>
	    <td class="rowcaption">PG9</td>
	    <td>{$abyinfo.vcera.pg9_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg9_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.pg9_verb|string_format:"%d"}</td>
	</tr>
	
	<tr>
	    <td class="rowcaption">Sum</td>
	    <td>{$abyinfo.dnes.celkem_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.celkem_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.dnes.celkem_verb|string_format:"%d"}</td>
	    <td class="rowcaption">Sum</td>
	    <td>{$abyinfo.vcera.celkem_vzkd|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.celkem_vzaby|string_format:"%d"}</td>
	    <td>{$abyinfo.vcera.celkem_verb|string_format:"%d"}</td>
	</tr>
	
	<tr>
	    <th colspan="8">akt. Monat / aktuální měsíc</th>
	</tr>
	<tr>
	    <td  class="rowcaption" colspan="2">PG1</td>
	    <td colspan="2">{$abyinfo.mesic.pg1.vzkd|string_format:"%d"}</td>
	    <td colspan="2">{$abyinfo.mesic.pg1.vzaby|string_format:"%d"}</td>
	    <td colspan="2">{$abyinfo.mesic.pg1.verb|string_format:"%d"}</td>
	</tr>

    </table>

</body>
</html>
