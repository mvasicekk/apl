
// pole s hodnotama levelu pro enabled a display

var controls_levels = Array(
                                'rechumrechwdh',10,10
							);
							



function init_level(level)
{
	// projedu pole id, u kterych chci ovlivnit zobrazeni podle levelu
	for(i=0;i<controls_levels.length/3;i++)
	{
		//alert('id='+controls_levels[3*i]+' enable level='+controls_levels[3*i+1]+' display level='+controls_levels[3*i+2]);
		id=controls_levels[3*i];
		enable_level=controls_levels[3*i+1];
		display_level=controls_levels[3*i+2];
		
		var control=document.getElementById(id);
		
		//alert('id='+id);
		if(control)
		{
			// nastaveni enabled disabled
			if(level>=enable_level)
				control.disabled=false;
			else
				control.disabled=true;
				
				
			// nastaveni stylu display
			if(level>=display_level)
				control.style.visibility='visible';
			else
				control.style.visibility='hidden';
		}
		
	}
	
	// nastavim fokus na username pokud ho teda mam
	var usercontrol = document.getElementById('username');
	//alert('usercontrol='+usercontrol);
	if(usercontrol)
		usercontrol.focus();
		
}