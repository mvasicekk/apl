<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>
      ImExCalendar
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">
<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
{*<script type="text/javascript" src="./fixed_table_rc.js"></script>*}
<script type="text/javascript" src="./imex.js"></script>
{*<link rel="stylesheet" href="./fixed_table_rc.css" type="text/css">*}
</head>

<body>
{*    <div>
	kundeVon : {$kundeVon}&nbsp;
	kundeBis : {$kundeBis}&nbsp;
	datumVon : {$datumVon}&nbsp;
	datumBis : {$datumBis}
    </div>*}
{*    calendar*}
    <div>
	<table class="imextable">
	    <tr>
		<th class="kundeheader">&nbsp;</th>
		{foreach from=$kundenArray item=kunde key=kundenr}
		<th class="kundeheader">
		    {$kundenr}
		</th>
		{/foreach}
	    </tr>
	{foreach from=$calendarArray item=tag key=tagdatum}
	    <tr id="tag_{$tag.datum}" class="{$tag.tagname} {$tag.dnes}">
		<th class="datumheader">
		    {$tag.datum}&nbsp;{$tag.tagname}
		</th>
		
		{foreach from=$kundenArray item=kunde key=kundenr}
		    <td id="tag_{$tag.datum}_{$kundenr}" class="kundeTagBox droppable">{foreach from=$importeDatumArray[$tagdatum][$kundenr] item=import}<div id="im_{$import.import}" class="importnr {$import.draggable}">Im<b>{$import.import}</b>/{$import.im_soll_time}<br>IM:vzkd&nbsp;{$import.vzkdsoll_import}</div>{/foreach}
			{if count($importeDatumArray[$tagdatum][$kundenr])>0}<br>{/if}
			{foreach from=$exporteDatumArray[$tagdatum][$kundenr] item=export}<div title="{$export.zielort}" id="ex_{$export.export}" class="selectable exportnr {$export.draggable} {$export.auslief} {$export.fertig}">Ex<b>{$export.export}</b>/{$export.exporttime}<br>{$export.zielort}</div>{/foreach}
		    </td>
		{/foreach}
	    </tr>
	{/foreach}
	</table>
    </div>
</body>
</html>
