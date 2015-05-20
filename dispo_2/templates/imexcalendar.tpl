<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />          
    <title>
      ImExCalendar
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<!--jQuery dependencies-->
<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />
<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<script type = "text/javascript" src = "../js/jquery.floatThead.js"></script>
	
<script type="text/javascript" src="./imex.js"></script>
</head>

<body>
{*    calendar*}
    <div>
	<table class="imextable">
	    <thead>
	    <tr>
		<th class="kundeheader">Dat. / KD</th>
		{foreach from=$kundenArray item=kunde key=kundenr}
		<th class="kundeheader">
		    {$kundenr}
		</th>
		{/foreach}
	    </tr>
	    </thead>
	    <tbody>
	{foreach from=$calendarArray item=tag key=tagdatum}
	    <tr id="tag_{$tag.datum}" class="{$tag.tagname} {$tag.dnes}">
		<th id="tagheader_{$tag.datum}" class="datumheader">
		    {$tag.datum}&nbsp;{$tag.tagname}
		    {foreach from=$lkwDatumArray[$tagdatum] item=lkw}<div title="{$lkw.id}" id="lkw_{$lkw.id}" class="lkw lkwdraggable lkw_{$lkw.id}">{$lkw.lkw_kz}/{$lkw.imexstr}</div>{/foreach}
		</th>
		
		{foreach from=$kundenArray item=kunde key=kundenr}
		    <td id="tag_{$tag.datum}_{$kundenr}" class="kundeTagBox droppable">{foreach from=$importeDatumArray[$tagdatum][$kundenr] item=import}<div title="BestellNr:{$import.bestellnr}" id="im_{$import.import}" class="importnr {$import.draggable}">Im<b>{$import.import}</b>/{$import.im_soll_time}<br>IM:vzkd&nbsp;{$import.vzkdsoll_import}</div>{/foreach}
			{if count($importeDatumArray[$tagdatum][$kundenr])>0}<br>{/if}
			{foreach from=$exporteDatumArray[$tagdatum][$kundenr] item=export}<div title="{$export.zielort}" id="ex_{$export.export}" class="selectable exportnr {$export.draggable} {$export.auslief} {$export.fertig}">Ex<b>{$export.export}</b>/{$export.exporttime}<br>{$export.zielort}<br>Rest:<b>{$export.vzkdrest}</b></div>{/foreach}
		    </td>
		{/foreach}
	    </tr>
	{/foreach}
	</tbody>
	</table>
    </div>
</body>
</html>
