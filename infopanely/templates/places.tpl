<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<title>
	    Infopanely
	</title>
	<!--    <link rel="stylesheet" href="./styl.css" type="text/css" media="screen and (max-width:1000px)">-->
	<link rel="stylesheet" href="./styl.css" type="text/css">
    </head>

    <body>
	<div class='places'>
	    <a href='../index.php' class='placebutton'>APL</a>
	    {foreach from=$places item=place}
		<a href='./panels.php?place_id={$place.id}&amp;place={$place.place}' class='placebutton'>&nbsp;{$place.place}</a>
	    {/foreach}
    </div>
</body>

</html>
