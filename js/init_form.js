
// pole s hodnotama levelu pro enabled a display

var controls_levels = Array(
								'dpers',10,0,
								'daufkopf',9,0,
								'dzeitedata',9,0,
                                                                'dzeit',9,0,
                                                                'anwesenheitplan',10,0,
								'perskarte',10,0,
								'dksd',9,0,
								'dkopf',9,0,
								'cmr',10,0,
								'drueck',2,0,
								'drueckmehr',9,0,
								'lagerbew',9,0,
								'lagerstk',9,0,
                                                                'behlagerbew',9,0,
								'behlagerinv',9,0,
								'berichte',9,0,
								'gfberichte',8,0,
                                                                'repeingabe',1,0,
								'showquery',9,0,
                                                                'phpexcel',9,0
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
//	var usercontrol = document.getElementById('username');
//	//alert('usercontrol='+usercontrol);
//	if(usercontrol)
//		usercontrol.focus();
		
}