<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Exportrechnung erstellen / vytvoreni faktury podle exportu
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../../styldesign.css" type="text/css">
<script type="text/javascript" src="../../js/detect.js"></script>
<script type="text/javascript" src="../../js/eventutil.js"></script>
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script charset="windows-1250" src="init_form.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../../js/ajaxgold.js"></script>
<script type = "text/javascript" src = "../../js/jquery.js"></script>
<script type = "text/javascript" src = "./js_func_new.js"></script>

</head>

<body onLoad="init_form('show');init_level({$level});">
<!--{popup_init src="../js/overlib.js"}-->
  
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
Exportrechnung erstellen / vytvoreni faktury podle exportu
</div>

<div id="formular_telo">
	<table cellpadding="5px" class="formulartable" border="1">
	    	<tr>
			<td>
			Exportrechnung erstellen: {$auftragsnr_value} 
			</td>
			<td>
			Auslieferdatum:<input onblur="getDataReturnText('./validate_datum.php?what=datum&value='+this.value+'&controlid='+this.id, refreshdatum)" type='text' name='auslieferdatum' id='auslieferdatum' size='10' maxlength='10' value='{$ausliefer_value}'/>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div id="scroll">
				<table id='rechnung_table'>
				<tr id='rechnung_table_hlavicka'>
					<td align='left'>Teil</td>
					<td align='right'>Pal</td>
					<td align='left'>Taetigkeit</td>
					<td align='right'>Importstk</td>
					<td align='right'>Exportstk</td>
					<td align='right'>Preis</td>
					<td align='right'>GesPreis</td>
					<td align='right'>Ausschuss</td>
					<td align='left'>Bestell</td>
					<td align='left'>Pos</td>
					<td align='right'>Ex-Im</td>
					<td align='left'>vom Auftrag</td>
					<td align='left'>Taetigkeit</td>
				</tr>

				{foreach from=$dauftr item=polozka}
					<tr class='rechnung_table_position' id="row_{$polozka.id}_{$polozka.abgnr}">
						<td align='left'>{$polozka.teil}</td>
						<td align='right'>{$polozka.pal}</td>
						<td align='left'>{$polozka.tatkz}</td>
						<td align='right'>{$polozka.importstk}</td>
						<td align='right'>{$polozka.exportstk}</td>
						<td align='right'>{$polozka.preis|string_format:"%.4f"}</td>
						<td align='right'>{$polozka.gespreis|string_format:"%.4f"}</td>
						<td align='right'>{$polozka.auss}</td>
						<td align='left'>{$polozka.fremdauftr}</td>
						<td align='left'>{$polozka.fremdpos}</td>
						<td align='right'>{$polozka.diff}</td>
						<td align='left'>{$polozka.auftragsnr}</td>
{*						u fracht bude editovatelny*}
						<td align='left'>
						    <input readonly type="text" id="text1_{$polozka.id}" acturl='./updateDrechField.php' value="{$polozka.text}" style="width: 100%;" maxlength="100"/>
						</td>
					</tr>
				{/foreach}
				</table>
				</div>
			</td>
		</tr>
	</table>
</div>

<div id='rechnung_aby_teilen_form'>
<form action="">
	<table>
		<tr>
			<td>
				<label>markieren Taetigkeiten fuer TaetNr:</label>
			</td>
			<td>
				von :
                                <input type="text" value="2001" size="4" maxlength="6" id="abgnr_von"/>
                                &nbsp;bis:
                                <input type="text" value="9999" size="4" maxlength="6"  id="abgnr_bis"/>
			</td>
		</tr>
		<tr>
			<td>
                            <label>Rechnungsnummer fuer <strong>markierte Positionen</strong> :</label>
			</td>
			<td>
				<input readonly="readonly" maxlength='8' size="8" type="text" id="rechnr_ma" value="{$ma_rechnr}"/>
			</td>
		</tr>
		<tr>
			<td>
				<label>Rechnungsnummer fuer nicht markierte Positionen :</label>
			</td>
			<td>
				<input readonly="readonly" maxlength='8' size="8" type="text" id="rechnr_regular" value="{$auftragsnr_value}"/>
			</td>
		</tr>
<!--		<tr>
                    <td>drucken :</td>
                    <td><input type="radio" name="druck_typ" checked="checked" value="ma" />&nbsp;markierte Positionen</td>
		</tr>-->
<!--      		<tr>
                    <td>&nbsp;</td>
                    <td><input type="radio" name="druck_typ" value="regular"/>&nbsp;nicht markierte Positionen</td>
		</tr>-->
                <tr>
                    <td><input type="button" id="bt_markieren" value="markieren"/></td>
                    <td><input acturl="rechnungteilen_aby.php" type="button" id="bt_markieren_run" value="TEILEN !"/></td>
                    <td><input type="button" id="bt_abbrechen" value="abbrechen"/></td>
                    <input type="hidden" id="flag_teilen" value="0" />
                </tr>
	</table>
</form>
</div>

<div id='form_footer'>
<table border='1'>
	<tr>
            <td>Rechnungspreis:</td>
            <td>&nbsp;</td>
            <td align='right'>{$rechnung_gesamt_preis|string_format:"%.4f"}</td>
            <input type="hidden" id="hat_ma_rechnung" value="{$hat_MARechnung}"/>
            <td id="td_rechnung_drueck_auswahl">
                MA RechnungNr : {$ma_rechnr}&nbsp;
                druecken : <input type="radio" name="dt" value="ma"/>&nbsp;MA Rechnung
                &nbsp;<input type="radio" name="dt" value="regular"/>&nbsp;ohne MA Rechnung
                &nbsp;<input type="radio" checked="checked" name="dt" value="voll"/>&nbsp;volle Rechnung
            </td>
	</tr>
	<tr>
            <td>Drueckpreis:</td>
            <td>&nbsp;</td>
            <td align='right'>{$drueck_gesamt_preis|string_format:"%.4f"}</td>
            <td>&nbsp;</td>
	</tr>
	<tr>
            <td>Differenz DRUECK-Rechnung:</td>
            <td>&nbsp;</td>
            <td align='right'>{$drueck_rechnung_differenz|string_format:"%.4f"}</td>
            <td>&nbsp;</td>
	</tr>
        
</table>
<div id='tlacitka_reporty'>
    <table>
        <tr>
            <td>
	{if $hasrechnung eq 0}
		<input class='formularbutton' type='button' value='Rechnung berechnen' onclick="this.disabled=true;getDataReturnXml('./rechnung_erfassen.php?auftragsnr={$auftragsnr_value}&auslieferdatum='+encodeControlValue('auslieferdatum'),rechnung_berechnen);" id='dorechnung'/>
	{else}
		<input class='formularbutton' disabled type='button' value='Rechnung berechnen' onclick="getDataReturnXml('./rechnung_erfassen.php?auftragsnr={$auftragsnr_value}&auslieferdatum='+encodeControlValue('auslieferdatum'),rechnung_berechnen);" id='dorechnung'/>
	{/if}
            </td>
            <td>
	<input type='hidden' name='hatmarech' id='hatmarech' value='{$hat_MARechnung}' />
	
	<input class='' type='button' value='D740 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.;XMA RechNr;DT;hat MA Rech;PDF Password,password&promenne=auftragsnr;ma_rechnr;dt;hatma;pdfpass&values={$auftragsnr_value};{$ma_rechnr};'+$('input[name=dt]:checked').val()+';{$hat_MARechnung}&report=D740'" id='D740'/>
{*	<input class='' type='button' value='D760 kurz' onclick="location.href='../../Reports/D760_pdf.php?report=D760&auftragsnr={$auftragsnr_value}&auftragsnr_label=Rech Nr'+'&ma_rechnr={$ma_rechnr}'+'&dt='+$('input[name=dt]:checked').val()+'&hatma={$hat_MARechnung}'" id='D760'/>*}
        </td>
        <td>
	<input class='' type='button' value='D750 normal' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$auftragsnr_value}&report=D750'" id='D750'/>
        </td>
<!--        <input class='' type='button' value='D770 normal' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$auftragsnr_value}&report=D770'" id='D770'/>-->
<td>
        <input class='' type='button' value='D760 kurz' onclick="location.href='../../Reports/D760_pdf.php?report=D760&auftragsnr={$auftragsnr_value}&auftragsnr_label=Rech Nr'+'&ma_rechnr={$ma_rechnr}'+'&dt='+$('input[name=dt]:checked').val()+'&hatma={$hat_MARechnung}'" id='D760'/>
</td>
        <td>
        <input class='' disabled="disabled" type='button' value='D770 normal' onclick="location.href='../../Reports/D770_pdf.php?report=D770&auftragsnr={$auftragsnr_value}&auftragsnr_label=Rech Nr&abgnr_von='+$('#abgnr_von').val()+'&abgnr_bis='+$('#abgnr_bis').val()+'&flag_teilen='+$('#flag_teilen').val()+'&druck_typ='+$('input[@name=druck_typ]:checked').val()+'&rechnr_regular='+$('#rechnr_regular').val()+'&rechnr_ma='+$('#rechnr_ma').val()" id='D770'/>
        </td>
        <td>
{*	<input class='' type='button' value='D742 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.;Typ,*RA&promenne=auftragsnr;typ&values={$auftragsnr_value};normal,Summe Teil,nur Summe Teil&report=D742'" id='D742'/>*}
	<input class='' type='button' value='D742 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.;Typ,*RA;XMA RechNr;DT;hat MA Rech&promenne=auftragsnr;typ;ma_rechnr;dt;hatma&values={$auftragsnr_value};normal,Summe Teil,nur Summe Teil;{$ma_rechnr};'+$('input[name=dt]:checked').val()+';{$hat_MARechnung}&report=D742'" id='D742'/>
        </td>
        <td>
	<input class='' type='button' value='D752 normal' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$auftragsnr_value}&report=D752'" id='D752'/>
            </td>
        <td>
	<input class='' type='button' value='D792 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$auftragsnr_value}&report=D792'" id='D792'/>
        </td>

        <td>
	<input class='' type='button' value='D794 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.;Typ,*RA&promenne=auftragsnr;typ&values={$auftragsnr_value};Gute Teile,Ausschuss,Mehrarbeit&report=D794'" id='D794'/>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
	<input class='' type='button' value='Umrechnung' onclick="location.href='./rechumrechwdh.php'" id='rechumrechwdh'/>
        </td>
        <td>
        <input class='' {if $hasrechnung eq 0 or $hat_MARechnung neq 0}disabled='disabled'{/if} type='button' value='Rechnung teilen' id='bt_rechnung_teilen'/>
        </td>
        <td>
	<input class='' type='button' value='Ende' onclick="document.location.href='../dauftr.php?auftragsnr={$auftragsnr_value}';"/>
        </td>
    </tr>
    </table>
</div>
</div>
</body>
</html>
