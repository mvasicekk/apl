<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>
      Infopanely
    </title>
<!--    <link rel="stylesheet" href="./styl.css" type="text/css" media="screen and (min-width:1000px)">-->
	<link rel="stylesheet" href="./styl.css" type="text/css">
</head>

<body>
    <div class='panels'>
    <table class='panelstable'>
	<tr>
	    <td colspan="3">
		<a href='./places.php' class='placebutton' />místa ({$place})</a>
	    </td>
	</tr>
	<tr>
	{foreach from=$panels item=panel name=panely}
	    {if $smarty.foreach.panely.index % 3 == 0}
		</tr>
		<tr>
	    {/if}	    
	    <td>
		<a href='./panel.php?table_id={$panel.id}&place_id={$place_id}&place={$place}' class='panelbutton' />{$panel.text1}</a>
	    </td>
	{/foreach}
	</tr>
    </table>
    </div>
</body>

</html>
