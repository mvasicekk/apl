<!DOCTYPE html> 
<html> 
	<head> 
	<meta charset="UTF-8" />
	<title>Tartan Builder</title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="./js/jquerymobile/jquery.mobile-1.4.4.min.css" />
	<link rel="stylesheet" href="./css/tartans.css" />
	<script src="./js/jquery-1.11.1.min.js"></script>
	<script src="./js/jquerymobile/jquery.mobile-1.4.4.min.js"></script>
</head> 
<body> 

<div data-role="page">

	<div data-role="header" data-position="fixed">
	  <a href="index.html" rel="prev" data-icon="back">Back</a>
		<h1>Tartan Builder</h1>
	</div><!-- /header -->

	<div data-role="content">	
	    <form id="tartanator_form">
		<ul data-role="listview" id="tartanator_form_list">
		    <li data-role="list-divider">Tell us about your tartan</li>
		    <li data-role="fieldcontain">
			<label for="tartan_name">Tartan name</label>
			<input type="text" name="name" id="tartan_name" placeholder="Tartan Name" />
		    </li>
		    <li data-role="fieldcontain">
			<label for="tartan_info">Tartan Info</label>
			<textarea cols="40" rows="8" name="tartan_info" id="tartan_info"
				  placeholder="optional tartan info or description"></textarea>
		    </li>
		    <li data-role="list-divider">Build your colors</li>
		    <?php for($i=0;$i<6;$i++): ?>
		    <li class="colorset">
			<div data-role="fieldcontain" class="color-input">
			    <label class="select" for="color-<?php print $i; ?>">Color</label>
			    <select name="color[]" id="color-<?php print $i; ?>">
				<option value="">Select a Color</option>
				<option value="#000000">Black</option>
				<option value="#ffffff">White</option>
			    </select>
			</div>
		    </li>
		    <?php endfor; ?>
		</ul>
	    </form>
	</div><!-- /content -->

	    <div data-role="footer" data-position="fixed">
		<div data-role="navbar">
		    <ul>
			<li><a href="index.html" data-icon="info">About Us</a></li>
			<li><a href="findevent.html" data-icon="star">Find An Event</a></li>
			<li><a href="tartans.html" data-icon="grid" class="ui-btn-active">Popular Tartans</a></li>
		    </ul>
		</div>
	    </div>

</div><!-- /page -->

</body>
</html>