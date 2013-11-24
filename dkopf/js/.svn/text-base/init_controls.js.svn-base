
// pole s hodnotama levelu pro enabled a display

var controls_levels = Array(
								'position_neu',9,0,
								'teil_neu',10,0,
								'teil_edit',10,0,
								'info_D510',9,0,
								'teil_save',9,0,
								'lager_zugang',10,0,
                                                                'teil_edit',99,99
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
