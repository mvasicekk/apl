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
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="./panel.js"></script>
</head>

<body>
    <div class='panel'>
    <table class='paneltable'>
	<tr>
	    <td>
		<a href='./panels.php?place_id={$place_id}&place={$place}' class='placebutton' />infopanely ({$place})</a>
	    </td>
	</tr>
<!--	<tr>
	    <td>
		{$table.id}
	    </td>
	</tr>-->
	<tr>
	    <td>
		<input maxlength="12" acturl='../dauftr/saveInfoPanelText.php' class='text1' type="text" value='{$table.text1}' id='text1_{$table.id}' />
	    </td>
	</tr>
		<tr>
	    <td>
		<input maxlength="12" acturl='../dauftr/saveInfoPanelText.php' class='text2' type="text" value='{$table.text2}' id='text2_{$table.id}' />
	    </td>
	</tr>
	<tr>
	    <td>
		<input maxlength="8" acturl='../dauftr/saveInfoPanelText.php' class='text3' type="text" value='{$table.text3}' id='text3_{$table.id}' />
	    </td>
	</tr>
	<tr>
	    <td>
		<input maxlength="12" acturl='../dauftr/saveInfoPanelText.php' class='text4' type="text" value='{$table.text4}' id='text4_{$table.id}' />
	    </td>
	</tr>
	<tr>
	    <td>
		<input maxlength="16" acturl='../dauftr/saveInfoPanelText.php' class='text5' type="text" value='{$table.text5}' id='text5_{$table.id}' />
	    </td>
	</tr>

    </table>
    </div>
</body>

</html>
