<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Positionen schnell erfassen / rychle zadani zakazky
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../../js/detect.js"></script>
<script type="text/javascript" src="../../js/eventutil.js"></script>
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../../js/ajaxgold.js"></script>
<script type="text/javascript">
var pole = new Array(	"teil",
							"pal_nr",
							"stk_pro_pal",
							"pal_erst",
							"increment",
							"fremdauftr",
							"fremdpos",
							"exgeplannt",
							"pos_erstellen"
);
</script>
</head>

<body onLoad="init_form('show');">
{popup_init src="../js/overlib.js"}
  
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>


<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Positionen schnell erfassen / rychle zadani pozic zakazky
</div>

<div id="formular_telo">
<table cellpadding="5px" class="formulartable" border="1">
	<form autocomplete='off' method="post" action='' name="" onsubmit="">
    <tr>
		<td width='400'>
			<table class='dauftr_table' cellpadding='5'>
				<tr>
					<td colspan='4'>
						<!-- cislo dilu -->
						<label for="teil"><b><u>T</u></b>eil</label>
						<input accesskey='t' onblur="getDataReturnXml('./validate_teil.php?&controlid='+this.id+'&value='+this.value+'&kunde='+{$kunde_value}+'&auftragsnr='+{$auftragsnr_value}+'&minpreis='+{$minpreis_value}, validate_teil);" onkeyup="getDataReturnXml('./suggest.php?keyword='+this.value+'&kunde='+{$kunde_value}, pissuggest);" maxlength='10' size="10" type="text" id="teil" name="teil" value="{$teil_value}" />
                        <input id="status" type="text" size="40" value="" style="visibility:hidden;background-color:red;font-size:large;font-weight:bold;color:black;" />
					</td>
				</tr>
                <tr>
                    <td>
                        <label for="bezeichnung">Teilbez.</label>
                    </td>

                    <td colspan="3">
                        <input type="text"  size="50" value="" id="bezeichnung" style="border:none;background-color:lightyellow;visibility:hidden;"/>
                    </td>
                </tr>
		
                <tr>
                    <td>
                        <label for="rest">Restmenge verw.</label>
                    </td>

                    <td colspan="3">
                        <input type="text"  size="6" value="" id="rest" style="border:none;background-color:lightyellow;visibility:hidden;"/>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="brgewicht">BrGewicht [kg]</label>
                    </td>
                    <td>
                        <input size="5" type="text" id="brgewicht" style="text-align:right;border:none;background-color:lightyellow;visibility:hidden;" />
                    </td>
                    <td>
                        <label for="netgewicht">NetGewicht [kg]</label>
                    </td>
                    <td>
                        <input onfocus="this.select();"  onblur="js_validate_float(this);" size="5" type="text" id="netgewicht" style="text-align:right;border:none;background-color:lightyellow;visibility:hidden;" />
                    </td>
                </tr>
				<tr>
					<td>
						<!-- cislo palety -->
						<label for="pal_nr">Palettenstueckzahl / pocet palet</label>
					</td>
					<td>
						<input onfocus="this.select();"  onblur="js_validate_pal_nr(this);" maxlength='4' size="4" type="text" id="pal_nr" name="pal_nr" value="0"/>
					</td>
					<td>
						<label for="fremdauftr">fremdauftr</label>
					</td>
					<td>
						<!-- cislo cizi zakazky -->
						<input maxlength='20' size="15" type="text" id="fremdauftr" name="fremdauftr" value="{$fremdauftr_value}"/>
					</td>

				</tr>
				<tr>
					<td>
						<label for="stk_pro_pal">Stueck pro Palette / kusu na palete</label>
					</td>
					<td>

						<input onfocus="this.select();" onblur="js_validate_stk_pro_pal(this);" maxlength='4' size="4" type="text" id="stk_pro_pal" name="stk_pro_pal" value="1"/>
					</td>
					<td>
						<label for="fremdpos">fremdpos</label>
					</td>
					<td>

						<input maxlength='10' size="10" type="text" id="fremdpos" name="fremdpos" value="{$fremdpos_value}"/>
					</td>

				</tr>
				<tr>
					<td>
						<label for="pal_erst">erste Palettenummer / cislo prvni palety</label>
					</td>
					<td>
						<input onfocus="this.select();" onblur="getDataReturnXml('./validate_pal.php?&controlid='+this.id+'&value='+this.value+'&auftragsnr='+{$auftragsnr_value}, validate_palnr);" maxlength='4' size="4" type="text" id="pal_erst" name="pal_erst" value="10"/>
					</td>
					<td>
						<label disabled for="fremdausauftrag">fremd aus Auftrag</label>
					</td>
					<td>
						<input disabled maxlength='10' size="10" type="text" id="fremdausauftrag" name="fremdausauftrag" value="{$fremdausauftrag_value}"/>
					</td>

				</tr>
				<tr>
					<td>
						<label for="increment">Increment / prirustek</label>
					</td>
					<td>
						<input onfocus="this.select();" maxlength='4' size="4" type="text" id="increment" name="increment" value="10"/>
					</td>
					<td>
						<label for="exgeplannt">Ex geplant mit</label>
					</td>
					<td>
						<input maxlength='9' size="9" type="text" id="exgeplannt" name="exgeplannt" value="{$exgeplannt_value}"/>
					</td>

				</tr>

				<tr>
					<td colspan='2'>
						<input class='formularbutton' onclick="fillParamList();getDataReturnXml('./erfassen.php?'+encodeControlValue('paramlist')+'&auftragsnr='+encodeControlValue('auftragsnr')+'&kunde='+encodeControlValue('kunde')+'&minpreis='+encodeControlValue('minpreis'),erfassenRefresh);" type="button" id="pos_erstellen" name="pos_erstellen" value="Positionen erstellen"/>
						<!--<input onclick="fillParamList();location.href='./erfassen.php?'+encodeControlValue('paramlist');" type="button" id="pos_erstellen" name="pos_erstellen" value="Positionen erstellen"/> -->
						<input type='hidden' id='paramlist' name='paramlist' size='30'/>
						<input type='hidden' id='auftragsnr' value='{$auftragsnr_value}' name='auftragsnr' size='30'/>
						<input type='hidden' id='kunde' value='{$kunde_value}' name='kunde' size='30'/>
						<input type='hidden' id='minpreis' value='{$minpreis_value}' name='minpreis' size='30'/>
					</td>
					<td colspan='2'>
						<input class='formularEndbutton' type="button" id="ende" name="ende" value="Ende / konec" onclick="location.href='../dauftr.php?auftragsnr={$auftragsnr_value}';"/>
					</td>

				</tr>

			</table>
		</td>
		
		<td>
		<div id="scroll">
			<div id="suggest">
			</div>
		</div>
		</td>
	</tr>
	</form>
</table>
</body>
</html>
