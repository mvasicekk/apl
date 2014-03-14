<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      DKopf
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../colorbox.css" type="text/css">
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">

<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>

<script type="text/javascript" src="./js/init_controls.js"></script>
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>
var promenne = new Array("kz_druck","taetnr","bez_d","bez_t","vzkd","vzaby","KzGut","bedarf_typ","lager_von","lager_nach");
var onblur_function = new Array("","getDataReturnXml('./validate_taetnr.php?value='+this.value, validate_taetnr);savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);","savevalue(this);");
var editovat = new Array(0,0,1,1,1,1,1,1,0,0);
</script>
<script type = "text/javascript" src = "js_tablegrid.js"></script>
<script type="text/javascript" src="../js/colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="./dkopf.js"></script>




</head>

<body onload="init_level({$level});init_dkopf_form('show');">
{popup_init src="../js/overlib.js"}

<!-- 
<div id='souradnice'>
souradnice
</div>
-->

<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Arbeitsplan pflegen / Sprava pracovniho planu
</div>

<div id="formular_telo">
	<form action="">
	<table cellpadding="1px" class="formulartable" border="0">
    <tr>
		<td>
	
			<label for="kunde">Kunde/zakaznik</label>
			<input onblur="getDataReturnXml('./validate_kunde.php?value='+this.value, validate_kunde);" maxlength='3' size="3" type="text" id="kunde" name="kunde" value="{$kunde_value}"/>
			<input size='45' type='text' class='hidden' id='kunde_failed' value='Falsche Kundennummer / spatne cislo zakaznika' />
	
			<label for="teillang">Originalteilnummer/originalni cislo</label>
			<input maxlength='35' size="35" type="text" id="teillang" name="teillang" value="{$teillang_value}"/>

            <label for="status">Status</label>
            <input acturl="dkopf_update.php" maxlength='3' size="3" type="text" id="status" name="status" value="{$status_value}"/>
		</td>
	</tr>
	<tr>
		<td>
	
			<label for="teil">Teil/Dil</label>
			<input class='disabled_bold' disabled readonly onblur="getDataReturnXml('./validate_teil.php?value='+this.value, validate_teil);" maxlength='10' size="10" type="text" id="teil" name="teil" value="{$teil_value}"/>
			<input size='45' type='text' class='hidden' id='teil_failed' value='Teilnummerfehler' />
	
			<label for="bezeichnung">Bezeichnung/oznaceni</label>
			<input size="40" type="text" id="bezeichnung" name="bezeichnung" value="{$bezeichnung_value}"/>
	
		</td>
	</tr>
	<tr>
		<td>
	
			<label for="gew">Nettogewicht/netto vaha</label>
			<input onblur="js_validate_float(this);" size="6" type="text" id="gew" name="gew" value="{$gew_value}"/>
	
			<label for="brgew">Bruttogewicht/brutto vaha</label>
			<input onblur="js_validate_float(this);" size="6" type="text" id="brgew" name="brgew" value="{$brgew_value}"/>

			<label for="wst">Werkst. / material</label>
			<input size="3" type="text" id="wst" name="wst" value="{$wst_value}"/>

			<label for="fa">FA</label>
			<input size="3" type="text" id="fa" name="fa" value="{$fa_value}"/>

		</td>
	</tr>
	<tr>
		<td>
		<!-- 
			<label for="jb">Jahresbedarf / rocni spotreba</label>
			<input size="6" type="text" id="jb" name="jb" value="{$jb_value}"/>
 		-->
			<label for="vm">Verpackungsmenge / balící předpis</label>
			<input size="6" type="text" id="vm" name="vm" value="{$vm_value}"/>

			<label for="spg">Stk pro Gehänge</label>
			<input size="4" type="text" id="spg" name="spg" value="{$spg_value}"/>
                        
                        <label for="spg">Restmengenverw.</label>
                        <input acturl="dkopf_update.php" maxlength="6" size="5" type="text" id="restmengen_verw" name="restmengen_verw" value="{$restmengen_verw_value}"/>
                </td>
        </tr>
	<tr>
	    <td>
		letzte Reklamationen :
		{foreach from=$letzte_reklamationen_array item=reklamation}
		    <a class='abutton' href='../Reports/S362_pdf.php?report=S362&reklnr={$reklamation.rekl_nr}&reklnr_label=ReklNr&tl_tisk=pdf'>{$reklamation.rekl_nr} ({$reklamation.rekl_datum})</a>&nbsp;
		{/foreach}
	    </td>
	</tr>
	<tr>
		<td>
			<label for="bemerk">Anderungen/Bemerkung / poznamka</label>
			<input maxlength='55' size="30" type="text" id="bemerk" name="bemerk" value="{$bemerk_value}"/>

			<label for="art_guseisen">Art Gusseisen / druh litiny</label>
			<input size="10" type="text" id="art_guseisen" name="art_guseisen" value="{$art_guseisen_value}"/>
		</td>
	</tr>
        <tr>
            <td>
		<label for="preis_stk_gut">Zielpreis gut </label>
                <input acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="preis_stk_gut" value="{$preis_stk_gut_value}"/>

		<label for="preis_stk_auss">Zielpreis auss </label>
                <input acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="preis_stk_auss" value="{$preis_stk_auss_value}"/>

      		<label for="fremdauftr_dkopf">Fremdauftr</label>
                <input acturl="dkopf_update.php" maxlength='50' size="10" type="text" id="fremdauftr_dkopf" value="{$fremdauftr_dkopf_value}"/>

            </td>

        </tr>
        <tr>
            <td>
                <label for="jb_lfd_2">Jahresbedarf (2012)</label>
                <input acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_2" value="{$jb_lfd_2_value}"/>
		
                <label for="jb_lfd_1">Jahresbedarf (2013)</label>
                <input acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_1" value="{$jb_lfd_1_value}"/>
                
                <label for="jb_lfd_j">Jahresbedarf (2014)</label>
                <input acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_j" value="{$jb_lfd_j_value}"/>
            </td>
        </tr>
	<tr>
	    <td>
                <label for="jb_lfd_plus_1">Jahresbedarf (2015)</label>
                <input acturl="dkopf_update.php" maxlength='10' size="6" type="text" id="jb_lfd_plus_1" value="{$jb_lfd_plus_1_value}"/>
	    </td>
	</tr>
	<tr>
	    <td>
		<label for="schwierigkeitsgrad_S11">Schwierigkeitsgrad S11 / obtížnost S11</label>
                <input acturl="dkopf_update.php" maxlength='255' size="25" type="text" id="schwierigkeitsgrad_S11" value="{$schwierigkeitsgrad_S11_value}"/>
	    </td>
	</tr>
	<tr>
	    <td>
		<label for="schwierigkeitsgrad_S51">Schwierigkeitsgrad S51 / obtížnost S51</label>
                <input acturl="dkopf_update.php" maxlength='255' size="25" type="text" id="schwierigkeitsgrad_S51" value="{$schwierigkeitsgrad_S51_value}"/>
	    </td>
	</tr>

	<tr>
	    <td>
		<label for="schwierigkeitsgrad_SO">Schwierigkeitsgrad SO / obtížnost SO</label>
                <input acturl="dkopf_update.php" maxlength='255' size="25" type="text" id="schwierigkeitsgrad_SO" value="{$schwierigkeitsgrad_SO_value}"/>
	    </td>
	</tr>
	<tr>
		<td>
			<fieldset>
			<legend>Musterlager / sklad vzoru</legend>
                        <table>
                            <tr>
				 <td>
				     <input id='showteildoku' type='button' value="TeilDoku" acturl='./showTeilDoku.php' /> <label>( Anzahl Dokumenten : {$pocet_teildoku} )</label>
				 </td>
                                  <td>
<!--				      <label>Ersteller : </label><input type='text' id='ersteller' size='10' maxlength='10'/>-->
				     <input class='' type='button' value='Lagerzettel' onclick="location.href='../get_parameters.php?popisky=Teil;DokuNr&promenne=teil;dokunr&values={$teil_value};{29}&report=D515'" />
                                 </td>

                            </tr>
                        </table>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>
			<fieldset>
			<legend>GDat Anlagen / GDat přílohy</legend>
                        <table>
                            <tr>
				 <td>
				     <input id='show_att_ppa' type='button' value="PPA" acturl='./showTeilAtt.php?att=ppa' />
				 </td>
 				 <td>
				     <input id='show_att_vpa' type='button' value="VPA" acturl='./showTeilAtt.php?att=vpa' />
				 </td>
 				 <td>
				     <input id='show_att_rekl' type='button' value="Reklamation" acturl='./showTeilAtt.php?att=rekl' />
				 </td>
                            </tr>
                        </table>
			</fieldset>
		</td>
	</tr>
	</table>
	</form>
</div>


<div id='apl_table'>
	<div id='scroll_apl'>	
		<table id='apltable' class='apl_table' border='0'>
		<tr class='apl_table_header'>
			<td>druck</td>
			<td>teatnr</td>
			<td>Bezeichnung (Deutsch)</td>
			<td>oznaceni (cesky)</td>
			<td>vzkd</td>
			<td>vzaby</td>
			<td>G</td>
			<td>Bedarf</td>
			<td>l. von</td>
			<td>l. nach</td>
			<td width='60'>&nbsp;</td>
		</tr>
		{foreach from=$dpos item=polozka}
		{if $polozka.KzGut eq "G"}
		<tr id='tr{$polozka.dpos_id}' class='Grow'>
		{else}
		    <!-- otevreni reklamaci z tabulky dreklamation -->
		<tr id='tr{$polozka.dpos_id}' bgcolor='{cycle values="#eeeeee,#dddddd"}'>
		{/if}
		
			{if $level gte 9}
				<td onmouseover="this.style.cursor='pointer';" onclick="getDataReturnText('./toggle_kz_druck.php?dpos_id={$polozka.dpos_id}', toggle_kz_druck);" id='druck{$polozka.dpos_id}' {if $polozka.kz_druck==0} bgcolor='grey'{else} bgcolor='red'{/if}>&nbsp;</td>
			{else}
				<td onmouseover="this.style.cursor='pointer';" id='druck{$polozka.dpos_id}' {if $polozka.kz_druck==0} bgcolor='grey'{else} bgcolor='red'{/if}>&nbsp;</td>
			{/if}
			
			<td id='td_select_taetnr{$polozka.dpos_id}' align='right'>{$polozka.taetnr}</td>
			<td>{$polozka.bez_d}</td>
			<td>{$polozka.bez_t}</td>
			<td align='right'>
				{if $level gte 9}
					{$polozka.vzkd|string_format:"%.4f"}
				{else}
					l9
				{/if}
			</td>
			<td align='right'>{$polozka.vzaby|string_format:"%.4f"}</td>
			<td>{$polozka.KzGut|string_format:"%2s"}</td>
			<td>{$polozka.bedarf_typ|string_format:"%2s"}</td>
			
			<td id='td_select_lager_von{$polozka.dpos_id}'>{$polozka.lager_von}</td>
			<td id='td_select_lager_nach{$polozka.dpos_id}'>{$polozka.lager_nach}</td>
			
			{if $level gte 9}
				<td onmouseover="this.style.cursor='pointer';" id='tdedit{$polozka.dpos_id}'><a id='edit{$polozka.dpos_id}' onclick="getDataReturnXml('./edit_dpos_row.php?dpos_id={$polozka.dpos_id}', edit);" href='#'>edit</a></td>
			{else}
				<td onmouseover="this.style.cursor='pointer';" id='tdedit{$polozka.dpos_id}'><a id='edit{$polozka.dpos_id}' href='#'>l9</a></td>
			{/if}
		</tr>
		{/foreach}
		</table>
	</div>
</div>

<div id='dposnew'>
<form action="">
	<table>
		<tr>
			<td>
				<label for="newtatnr">TatNr/operace</label>
			</td>
			<td>
				<input onblur="getDataReturnXml('./validate_newtaetnr.php?value='+this.value+'&kunde='+document.getElementById('kunde').value+'&teil='+document.getElementById('teil').value, validate_newtaetnr);" maxlength='5' size="5" type="text" id="newtatnr" name="newtatnr" value=""/>
				<input type='text' class='hidden' id='newtatnr_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<label for="newbez_d">Bezeichnung (Deutsch)</label>
			</td>
			<td>
				<input onblur="" maxlength='30' size="30" type="text" id="newbez_d" name="newbez_d" value=""/>
				<input type='text' class='hidden' id='newbez_d_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<label for="newbez_t">oznaceni (cesky)</label>
			</td>
			<td>
				<input onblur="" maxlength='30' size="30" type="text" id="newbez_t" name="newbez_t" value=""/>
				<input type='text' class='hidden' id='newbez_t_failed' value='tatnrfehler' />
			</td>
		</tr>
		
		<tr>
			<td>
				<label for="newvzkd">VzKd</label>
			</td>
			<td>
				<input onblur="js_validate_float(this);" maxlength='10' size="10" type="text" id="newvzkd" name="newvzkd" value=""/>
				<input type='text' class='hidden' id='newvzkd_failed' value='tatnrfehler' />
			</td>
		</tr>

		<tr>
			<td>
				<label for="newvzaby">VzAby</label>
			</td>
			<td>
				<input onblur="js_validate_float(this);" maxlength='10' size="10" type="text" id="newvzaby" name="newvzaby" value=""/>
				<input type='text' class='hidden' id='newvzaby_failed' value='tatnrfehler' />
			</td>
		</tr>

		<tr>
			<td>
				<label for="newkzgut">KzGut</label>
			</td>
			<td>
				<input onblur="" maxlength='1' size="1" type="text" id="newkzgut" value=""/>
				<input type='text' class='hidden' id='newkagut_failed' value='tatnrfehler' />
			</td>
		</tr>
		
		<tr>
			<td>
				<label for="newbedarf">Bedarf</label>
			</td>
			<td>
				<input onblur="" maxlength='3' size="3" type="text" id="newbedarf" value=""/>
				<input type='text' class='hidden' id='newbedarf_failed' value='tatnrfehler' />
			</td>
		</tr>

		<tr>
			<td>
				<label for="newlagervon">Lager von</label>
			</td>
			<td>
				<select id="newlagervon">
					{html_options options=$lagry selected=$lager_selected}
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<label for="newlagernach">Lager nach</label>
			</td>
			<td>
				<select id="newlagernach">
					{html_options options=$lagry selected=$lager_selected}
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<input type='button' value='speichern / ulozit' onclick='insertposition();'/>
			</td>
			<td>
				<input type='button' value='abbrechen / zrusit' onclick='insertpositionCancel();' />
			</td>
		</tr>

	</table>
</form>
</div>

{if $pocet_priloh gt 0}
<div id='attach_table'>
<div id='scroll_attach'>	
		<table class='apl_table' border='0'>
		<tr class='apl_table_header'>
			<td>TYP</td>
			<td>PATH</td>
			<td>STAMP</td>
		</tr>
		{foreach from=$dattach item=polozka}
		<tr id='tr{$polozka.dpos_id}' bgcolor='{cycle values="#eeeeee,#dddddd"}'>
			<td id='td{$polozka.id_attachment}'>{$polozka.attachment_typ}</td>
			<td>
			<a href='{$polozka.attachment_path}'>
				{if $polozka.attachment_typ eq "FOTO" }
					<img src='{$polozka.attachment_path}' width='60' border='0'>
				{else}
					{$polozka.beschreibung}
				{/if}
			</a>
			</td>
			<td width='60'>{$polozka.stamp}</td>
		</tr>
		{/foreach}
		</table>
</div>
</div>
{/if}



<div id='dkopf_form_footer'>
<form action="">
<table width='100%' border='0' cellspacing='0'>
<tr>
	<td>
		<input class='formularbutton' type='button' value='teil suchen / hledat dil' onclick="document.location.href='teilsuchen.php';"/>
	</td>
	<td>
		<input class='formularbutton' id='position_neu' type='button' value='Neue Position / nova operace' onclick="positionneu();"/>
	</td>
	<td>
		<input class='formularbutton' id='teil_neu' disabled='disabled' type='button' value='teil neu / novy dil' onclick="document.location.href='teilsuchen.php';"/>
	</td>
	<td>
		<input class='formularbutton' id='teil_edit' disabled type='button' value='Teil aendern' onclick="document.location.href='teilnraendern.php?teil={$teil_value}';"/>
	</td>
</tr>
<tr>
	<td>
		<input class='formularbutton' id='info_D510' type='button' value='Info APL D510' onClick="location.href='../get_parameters.php?popisky=Teil&promenne=teil&values={$teil_value}&report=D510'"/>
	</td>
	<td>
		<input id='teil_save' class='formularbutton' type='button' value='Aenderungen speichern' 
		onclick="getDataReturnXml('./save_dkopf.php?teil='+encodeControlValue('teil')
												+'&kunde='+encodeControlValue('kunde')
												+'&teillang='+encodeControlValue('teillang')
												+'&status='+encodeControlValue('status')
												+'&bezeichnung='+encodeControlValue('bezeichnung')
												+'&gew='+encodeControlValue('gew')
												+'&brgew='+encodeControlValue('brgew')
												+'&wst='+encodeControlValue('wst')
												+'&fa='+encodeControlValue('fa')
												+'&vm='+encodeControlValue('vm')
                                                                                                +'&spg='+encodeControlValue('spg')
//												+'&reklamation='+encodeControlValue('reklamation')
//												+'&letzte_reklamation='+encodeControlValue('letzte_reklamation')
												+'&bemerk='+encodeControlValue('bemerk')
												+'&art_guseisen='+encodeControlValue('art_guseisen')
//												+'&muster_vom='+encodeControlValue('muster_vom')
//												+'&muster_platz='+encodeControlValue('muster_platz')
//												+'&muster_vorher_vom='+encodeControlValue('muster_vorher_vom')
//												+'&muster_freigabe1_vom='+encodeControlValue('muster_freigabe1_vom')
//												+'&muster_freigabe1='+encodeSelectControlValue('muster_freigabe1')
//												+'&muster_freigabe2_vom='+encodeControlValue('muster_freigabe2_vom')
//												+'&muster_freigabe2='+encodeSelectControlValue('muster_freigabe2')
												, saverefresh);"/>
	</td>
	<td>
		<input id='lager_zugang' class='formularbutton' disabled='true' type='button' value='Lager1 Zugang / vlozeni do skladusss' onclick="rebuildpage();"/>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
	</td>

</tr>
</table>
</form>
</div>

</body>
</html>
