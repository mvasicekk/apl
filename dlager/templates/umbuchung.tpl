<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      DLager - Umbuchung
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../colorbox.css" type="text/css">
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">

<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<script type="text/javascript" src="../js/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="./dlager.js"></script>




</head>

<body>

<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
DLager - Umbuchung
</div>

<div id="formular_telo">
	<form action="">
	    <table>
	    <tr>
		<td>
		<label for="teil">Teil</label>
		<input class='entermove' maxlength='10' size="10" type="text" id="teil" name="teil" value=""/>
		</td>
	    </tr>

	    <tr>
		<td>
		<label for="auftrag_import">Import - Auftragsnr</label>
		<input class='entermove' maxlength='6' size="6" type="text" id="auftrag_import" name="auftrag_import" value=""/>
		</td>
		<td>
		<label for="pal_import">Import - Palette</label>
		<input class='entermove' maxlength='4' size="4" type="text" id="pal_import" name="pal_import" value=""/>
		</td>
	    </tr>
	    
   	    <tr>
		<td>
		<label for="gut_stk">Gut - Stk</label>
		<input class='entermove' maxlength='4' size="4" type="text" id="gut_stk" name="gut_stk" value=""/>
		</td>
		<td>
		<label for="auss_stk">Auss - Stk</label>
		<input class='entermove' maxlength='4' size="4" type="text" id="auss_stk" name="auss_stk" value=""/>
		</td>
	    </tr>

	    <tr>
		<td>
		<label for="lager_von">Lager - Von</label>
		<input class='entermove' maxlength='4' size="4" type="text" id="lager_von" name="lager_von" value=""/>
		</td>
		<td>
		<label for="lager_nach">Lager - Nach</label>
		<input class='entermove' maxlength='4' size="4" type="text" id="lager_nach" name="lager_nach" value=""/>
		</td>
	    </tr>
	    
	    <tr>
		<td>
		<input class='entermove submit' acturl='./umbuchunggo.php' type="button" id='umbuchunggo' value='Enter' />
		</td>
	    </tr>
	    </table>
	</form>
</div>

</body>
</html>
