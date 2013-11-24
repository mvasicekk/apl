<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      Export Tablo
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>

<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="./exporttablo.js"></script>


</head>


{popup_init src="../js/overlib.js"}


<body>

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
        Export Tablo EX: {$export}
        <input id="S212" onClick="location.href='../get_parameters.php?popisky=Export (geplant);Reporttyp,*RA&promenne=termin;reporttyp&values={$export};Kunde,Expediteur&report=S212'" class='reportbutton' type="button"  name="S212" value="S212 - Export Tablo"/>

    </div>

    <div id="formular_telo">
        <div id="exporttable">
            <table>
                <thead>
                    <tr>
                        <th>Platten Nr.</th>
                        <th>ArtCode</th>
                        <th>Imp.Beh</th>
                        <th>Behalter Gew. laut Bestellung Netto</th>
                        <th>Stk Import</th>
                        <th>Export Stk D710</th>
                        <th>Export Stk laut Waage</th>
                        <th>kg/Stk Bestellung</th>
                        <th>kg/Stk (10Stk) Aby</th>
                        <th>Behaelter Gew. IST Abydos</th>
                        <th>IST Gew. kg netto</th>
                        <th>Sollgew. kg/brutto</th>
                        <th>Abydos Waage</th>
                        <th>Behaeltertyp</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$importy key=import item=radky}
                    <tr>
                        <td class="importheader">{$import}</td>
                    </tr>
                    {assign var="old_teil" value="X"}
                    {assign var="poradidilu" value="0"}
                    {assign var="aussSumme" value="0"}
                    {assign var="firstDauftrId" value="0"}
                    {assign var="auss_abywaage_kg_stk10" value="0"}
                    {assign var="auss_stk_laut_waage" value="0"}
                    {assign var="auss_abywaage_behaelter_ist" value="0"}
                    {assign var="auss_abywaage_brutto" value="0"}
                    {assign var="auss_kg_stk_bestellung" value="0"}
                    {assign var="auss_ist_kg_netto" value="0"}
                    {assign var="auss_behaelter_id" value="0"}
                    {assign var="auss_soll_kg_brutto" value="0"}
                    {foreach from=$radky key=poradi item=radek}
                    {if ($old_teil != $radek.teil) && ($poradidilu>0)}
                        <tr  id="platte_{$import}_{$old_teil}_{$firstDauftrId}_auss">
                            <td>AUSSCHUSS</td>
                            <td colspan="4">&nbsp</td>
                            <td id="{$firstDauftrId}_aussSumme" class="number">{$aussSumme}</td>
<!--                            <td id="{$firstDauftrId}_auss_stk_laut_waage" class="number">0</td>-->
<!--                            <td><input  class="entermove" title="" acturl="./aussupdatewaage.php" type="text" size="5" value="{$radek.auss_stk_laut_waage}" id="{$firstDauftrId}_auss_stk_laut_waage"/></td>-->
                            <td><input  class="entermove" title="" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_stk_laut_waage}" id="{$firstDauftrId}_auss_stk_laut_waage"/></td>
                            <td class="number" id="{$firstDauftrId}_auss_kg_stk_bestellung">{$auss_kg_stk_bestellung}</td>
                            <td><input class="entermove" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_abywaage_kg_stk10}" id="{$firstDauftrId}_auss_abywaage_kg_stk10"/></td>
                            <td><input class="entermove" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_abywaage_behaelter_ist}" id="{$firstDauftrId}_auss_abywaage_behaelter_ist"/></td>
                            <td id="{$firstDauftrId}_auss_ist_kg_netto" class="number">{$auss_ist_kg_netto}</td>
                            <td id="{$firstDauftrId}_auss_soll_kg_brutto" class="number">{$auss_soll_kg_brutto}</td>
                            <td><input class="entermove" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_abywaage_brutto}" id="{$firstDauftrId}_auss_abywaage_brutto"/></td>
                            <td>
                                <select class="entermove" id="{$firstDauftrId}_auss_behaeltertyp" acturl="./aussbehaeltertypupdate.php">
                                {html_options values=$behaeltertypenValues output=$behaeltertypenNames selected=$auss_behaelter_id}
                                </select>
                            </td>
                        </tr>
                        <tr  id="summe_{$import}_{$old_teil}_{$firstDauftrId}">
                            <td>Summe</td>
                            <td colspan="2">&nbsp</td>
                            <td id="platte_{$import}_{$old_teil}_kunde_behaelter_bestellung_netto" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_stkimport" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_stkexport" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_stk_laut_waage" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_kg_stk_bestellung"></td>
                            <td id="platte_{$import}_{$old_teil}_abywaage_kg_stk10"></td>
                            <td id="platte_{$import}_{$old_teil}_abywaage_behaelter_ist" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_ist_kg_netto" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_soll_kg_brutto" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_abywaage_brutto"  class="number"></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="teilseparator" colspan="14">&nbsp;</td>
                        </tr>
                        {assign var="newteil" value=1}
                        {assign var="old_teil" value=$radek.teil}
                        {assign var="firstDauftrId" value=$radek.id}
                        {assign var="auss_abywaage_kg_stk10" value=$radek.auss_abywaage_kg_stk10}
                        {assign var="auss_stk_laut_waage" value=$radek.auss_stk_laut_waage}
                        {assign var="auss_abywaage_behaelter_ist" value=$radek.auss_abywaage_behaelter_ist}
                        {assign var="auss_abywaage_brutto" value=$radek.auss_abywaage_brutto}
                        {assign var="auss_ist_kg_netto" value=$auss_abywaage_brutto-$auss_abywaage_behaelter_ist}
                        {assign var="auss_kg_stk_bestellung" value=$radek.kg_stk_bestellung}
                        {assign var="auss_behaelter_id" value=$radek.auss_behaelter_id}
                        {assign var="aussSumme" value="0"}
                    {/if}
                    {if $poradidilu==0}
                        {assign var="old_teil" value=$radek.teil}
                        {assign var="firstDauftrId" value=$radek.id}
                        {assign var="auss_stk_laut_waage" value=$radek.auss_stk_laut_waage}
                        {assign var="auss_abywaage_kg_stk10" value=$radek.auss_abywaage_kg_stk10}
                        {assign var="auss_abywaage_behaelter_ist" value=$radek.auss_abywaage_behaelter_ist}
                        {assign var="auss_abywaage_brutto" value=$radek.auss_abywaage_brutto}
                        {assign var="auss_ist_kg_netto" value=$auss_abywaage_brutto-$auss_abywaage_behaelter_ist}
                        {assign var="auss_kg_stk_bestellung" value=$radek.kg_stk_bestellung}
                        {assign var="auss_behaelter_id" value=$radek.auss_behaelter_id}
                    {/if}
                    <tr {if $poradidilu==0 or $newteil==1}class='firstteil'{assign var="newteil" value=0}{/if} id="platte_{$import}_{$radek.teil}_{$radek.id}">
                        <td title="Platten Nr.">{$radek.platte}</td>
                        <td id="{$radek.id}_teil_{$import}_{$radek.teil}" title="ArtCode">{$radek.teil}</td>
                        <td title="Imp.Beh" class="number">{$radek.pal}</td>
                        <td id="{$radek.id}_kunde_behaelter_bestellung_netto" class="number">{$radek.kunde_behaelter_bestellung_netto}</td>
                        <td id="{$radek.id}_importstk" title="Stk Import" class="number">{$radek.stkimport}</td>
                        <td id="{$radek.id}_exportstk" title="Export Stk 710" class="number">{$radek.stkexp}</td>
<!--                        <td id="{$radek.id}_stk_laut_waage" title="Export Stk laut Waage" class="number">{$radek.stk_laut_waage}</td>-->
                        <td><input  class="entermove" title="Export Stk laut Waage" acturl="./updatewaage.php" type="text" size="5" value="{$radek.stk_laut_waage}" id="{$radek.id}_stk_laut_waage"/></td>
                        <td><input  class="entermove" title="kg/Stk Bestellung" acturl="./updatewaage.php" type="text" size="5" value="{$radek.kg_stk_bestellung}" id="{$radek.id}_kg_stk_bestellung"/></td>
                        <td><input  class="entermove" title="kg/Stk (10Stk) Aby" acturl="./updatewaage.php" type="text" size="5" value="{$radek.abywaage_kg_stk10}" id="{$radek.id}_abywaage_kg_stk10"/></td>
                        <td><input  class="entermove" title="Behaelter Gew. IST Abydos" acturl="./updatewaage.php" type="text" size="5" value="{$radek.abywaage_behaelter_ist}" id="{$radek.id}_abywaage_behaelter_ist"/></td>
                        <td id="{$radek.id}_ist_kg_netto" title="IST Gew. kg netto" class="number">{$radek.ist_kg_netto}</td>
                        <td id="{$radek.id}_soll_kg_brutto" title="Sollgew. kg/brutto" class="number">{$radek.soll_kg_brutto}</td>
                        <td><input class="entermove" title="Abydos Waage" acturl="./updatewaage.php" type="text" size="5" value="{$radek.abywaage_brutto}" id="{$radek.id}_abywaage_brutto"/></td>
                        <td>
                            <select class="entermove" title="Behaeltertyp" id="{$radek.id}_behaeltertyp" acturl="./behaeltertypupdate.php">
                                {html_options values=$behaeltertypenValues output=$behaeltertypenNames selected=$radek.behaelter_id}
                            </select>
                        </td>
                    </tr>
                    {assign var="poradidilu" value=$poradidilu+1}
                    {assign var="aussSumme" value=$aussSumme+$radek.auss2+$radek.auss4+$radek.auss6}
                    {if $radek.ausschussbehaelter != 0}
                        {assign var="firstDauftrId" value=$radek.id}
                    {/if}
                    {/foreach}
                     <tr   id="platte_{$import}_{$radek.teil}_{$firstDauftrId}_auss">
                            <td>AUSSCHUSS</td>
                            <td colspan="4">&nbsp</td>
                            <td id="{$firstDauftrId}_aussSumme" class="number">{$aussSumme}</td>
<!--                            <td id="{$firstDauftrId}_auss_stk_laut_waage" class="number">0</td>-->
                            <td><input  class="entermove" title="" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_stk_laut_waage}" id="{$firstDauftrId}_auss_stk_laut_waage"/></td>
                            <td class="number" id="{$firstDauftrId}_auss_kg_stk_bestellung">{$auss_kg_stk_bestellung}</td>
                            <td><input class="entermove" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_abywaage_kg_stk10}" id="{$firstDauftrId}_auss_abywaage_kg_stk10"/></td>
                            <td><input class="entermove" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_abywaage_behaelter_ist}" id="{$firstDauftrId}_auss_abywaage_behaelter_ist"/></td>
                            <td id="{$firstDauftrId}_auss_ist_kg_netto" class="number">{$auss_ist_kg_netto}</td>
                            <td id="{$firstDauftrId}_auss_soll_kg_brutto" class="number">{$auss_soll_kg_brutto}</td>
                            <td><input class="entermove" acturl="./aussupdatewaage.php" type="text" size="5" value="{$auss_abywaage_brutto}" id="{$firstDauftrId}_auss_abywaage_brutto"/></td>
                            <td>
                                <select class="entermove" id="{$firstDauftrId}_auss_behaeltertyp" acturl="./aussbehaeltertypupdate.php">
                                {html_options values=$behaeltertypenValues output=$behaeltertypenNames selected=$auss_behaelter_id}
                                </select>
                            </td>
                    </tr>
                        <tr  id="summe_{$import}_{$old_teil}_{$firstDauftrId}">
                            <td>Summe</td>
                            <td colspan="2">&nbsp</td>
                            <td id="platte_{$import}_{$old_teil}_kunde_behaelter_bestellung_netto" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_stkimport" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_stkexport" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_stk_laut_waage" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_kg_stk_bestellung"></td>
                            <td id="platte_{$import}_{$old_teil}_abywaage_kg_stk10"></td>
                            <td id="platte_{$import}_{$old_teil}_abywaage_behaelter_ist"  class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_ist_kg_netto" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_soll_kg_brutto" class="number"></td>
                            <td id="platte_{$import}_{$old_teil}_abywaage_brutto"  class="number"></td>
                            <td></td>
                        </tr>

                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
