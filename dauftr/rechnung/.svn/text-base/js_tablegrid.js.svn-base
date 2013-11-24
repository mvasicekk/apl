var old_values = new Array(10);

function canceledit(control)
{
	var radek=document.getElementById(control.id).parentNode.parentNode.parentNode;
	// odbarvim radek
	radek.style.backgroundColor='';
	// td element s odkazy edit, save, cancel
	var tdarray=radek.getElementsByTagName('td');
	var e_operace = tdarray.item(tdarray.length-1);
	// odstranim vsechny childs v bunce
	while(e_operace.hasChildNodes())
	{
		//alert(e_operace.firstChild);
		e_operace.removeChild(e_operace.firstChild);
	}
	// vlozim tam odkaz edit
	var e_edit = document.createElement('a');
	e_edit.setAttribute('href','#');
	e_edit.setAttribute('id',control.id);
	dauftrid=control.id.substring(4,control.id.length);
	e_edit.setAttribute('onclick',"getDataReturnXml('./edit_dauftr_row.php?dauftr_id='+dauftrid, edit);");
	var e_edit_text = document.createTextNode('edit');
	e_edit.appendChild(e_edit_text);
	e_operace.appendChild(e_edit);
	
	
	
	// projdu vsechny input boxy
	var inputarray=radek.getElementsByTagName('input');
	pocet_inputu=inputarray.length;
	for(i=0;i<pocet_inputu;i++)
	{
			obsah_bunky=inputarray.item(0).getAttribute('value');
			//obsah_bunky=old_values[i];
			// nahradim input box pouze jeho hodnotou
			var obsah_bunky_textNode = document.createTextNode(obsah_bunky);
			//alert(inputarray.item(0).nodeName);
			inputarray.item(0).parentNode.replaceChild(obsah_bunky_textNode,inputarray.item(0));
			//alert(obsah_bunky_textNode+i);
		
	}
	
	// projdu vsechny selecty
	var selectarray=radek.getElementsByTagName('select');
	pocet_selectu=selectarray.length;
	for(i=0;i<pocet_selectu;i++)
	{
		jmeno_selectu=selectarray.item(0).getAttribute('name');
		optionarray=selectarray.item(0).getElementsByTagName('option');
		selectedvalue=optionarray.item(selectarray.item(0).selectedIndex).firstChild.data;
		var obsah_bunky_textNode = document.createTextNode(selectedvalue);
		// pripojim k parametrum jmenu inputboxu a hodnotu v nem obsazenou
		selectarray.item(0).parentNode.replaceChild(obsah_bunky_textNode,selectarray.item(0));
	}
}


function savevalue(control)
{
	//alert(control.value+"   id="+control.id);
	var e_input = document.getElementById(control.id);
	e_input.setAttribute('value',control.value);
	//control.value=control.value;
}




function save(control)
{
	var radek=document.getElementById(control.id).parentNode.parentNode.parentNode;
	// odbarvim radek
	radek.style.backgroundColor='';
	var parametry='';
	
	// td element s odkazy edit, save, cancel
	var tdarray=radek.getElementsByTagName('td');
	var e_operace = tdarray.item(tdarray.length-1);
	// odstranim vsechny childs v bunce
	while(e_operace.hasChildNodes())
	{
		//alert(e_operace.firstChild);
		e_operace.removeChild(e_operace.firstChild);
	}
	// vlozim tam odkaz edit
	var e_edit = document.createElement('a');
	e_edit.setAttribute('href','#');
	e_edit.setAttribute('id',control.id);
	dauftrid=control.id.substring(4,control.id.length);
	e_edit.setAttribute('onclick',"getDataReturnXml('./edit_dauftr_row.php?dauftr_id='+dauftrid, edit);");
	var e_edit_text = document.createTextNode('edit');
	e_edit.appendChild(e_edit_text);
	e_operace.appendChild(e_edit);
	
	// projdu vsechny input boxy
	var inputarray=radek.getElementsByTagName('input');
	pocet_inputu=inputarray.length;
	for(i=0;i<pocet_inputu;i++)
	{
		obsah_bunky=inputarray.item(0).getAttribute('value');
		//obsah_bunky=old_values[i];
		// nahradim input box pouze jeho hodnotou
		var obsah_bunky_textNode = document.createTextNode(obsah_bunky);
		// pripojim k parametrum jmenu inputboxu a hodnotu v nem obsazenou
		parametry+=inputarray.item(0).getAttribute('name')+"="+obsah_bunky+"&";
		inputarray.item(0).parentNode.replaceChild(obsah_bunky_textNode,inputarray.item(0));
		//alert(obsah_bunky_textNode+i);
	}
	
	
	// projdu vsechny selecty
	var selectarray=radek.getElementsByTagName('select');
	pocet_selectu=selectarray.length;
	
	for(i=0;i<pocet_selectu;i++)
	{
		jmeno_selectu=selectarray.item(0).getAttribute('name');
		optionarray=selectarray.item(0).getElementsByTagName('option');
		selectedvalue=optionarray.item(selectarray.item(0).selectedIndex).firstChild.data;
		var obsah_bunky_textNode = document.createTextNode(selectedvalue);
		// pripojim k parametrum jmenu inputboxu a hodnotu v nem obsazenou
		parametry+=selectarray.item(0).getAttribute('name')+"="+selectedvalue+"&";
		selectarray.item(0).parentNode.replaceChild(obsah_bunky_textNode,selectarray.item(0));
	}
	
	
	//alert(encodeURI(parametry));
	parametry=encodeURI(parametry);
	// odeberu jeden znak & z konce retezce
	//parametry=parametry.substring(0,parametry.length-1);
	parametry+="id="+control.id.substring(4,control.id.length);
	
	// pomoci AJAX updatnu radek v DAUFTR
	getDataReturnXml('./update_dauftr.php?'+parametry, update_dauftr)
	
	//alert(parametry);
}


function edit(xml)
{
	
	//alert(xml);
	//alert(control.id);
	var radekid='edit'+xml.getElementsByTagName('dauftr_id').item(0).firstChild.data;
	// posunout se z odkazu az na radkovy element, tj. o dve urovne nahoru
	//alert(radekid);
	var radek = document.getElementById(radekid).parentNode.parentNode;
	
	// zvyraznim radek barvou pozadi - je editovan
	radek.style.backgroundColor='yellow';
	//alert(radek);
	var bunky_array = radek.getElementsByTagName('td');
	for(i=0;i<promenne.length;i++)
	{
		obsah_bunky=bunky_array.item(i).innerHTML;
		//obsah bunky ulozim do old_values pro pripad cancel
		old_values[i]=obsah_bunky;
		var policko = document.createElement('input');
		policko.setAttribute('type','text');
		policko.setAttribute('size','5');
		policko.setAttribute('value',obsah_bunky);
		policko.setAttribute('name',promenne[i]);
		policko.setAttribute('id',promenne[i]);
		policko.setAttribute('onblur',onblur_function[i]);
		policko.setAttribute('class','edit_dpos');
		if(editovat[i])
		{
			if(bunky_array.item(i).hasChildNodes())
			{
				// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
				bunky_array.item(i).replaceChild(policko,bunky_array.item(i).firstChild);
			}
			else
			{
				// jinak tam pouze vlozim inputbox
				bunky_array.item(i).appendChild(policko);
			}
		}
	}
	
	
	// rucne musim pridat selecty pro pole u kterych chci vybirat hodnoty ze seznamu

	dauftr_id=radekid.substring(4,radekid.length);
	// odkazu se na seznam povolenych hodnot pro sklady
	var teilarray = xml.getElementsByTagName('teil');

	/////////////////////////////////////////////////////////////////////////////////////////////////
	// select pro teil
	/////////////////////////////////////////////////////////////////////////////////////////////////
	var td_select_teil = document.getElementById('td_select_teil'+dauftr_id);
	//td_select_lager_von.style.backgroundColor='red';
	var select_teil = document.createElement('select');
	select_teil.setAttribute('class','edit_dpos');
	select_teil.setAttribute('name','teil');
	select_teil.setAttribute('id','teil');
	
	select_teil_obsah_old=old_values[0];
	//alert('old_values[8]='+trim(lager_select_von_obsah_old));
	// prvni bude prazdnej
	select_teil_innerHTML="<option> </option>";
	
	for(i=0;i<teilarray.length;i++)
	{
		if(teilarray.item(i).firstChild.data==select_teil_obsah_old)
			select_teil_innerHTML+="<option selected>"+teilarray.item(i).firstChild.data+"</option>";
		else
			select_teil_innerHTML+="<option>"+teilarray.item(i).firstChild.data+"</option>";
	}
	
	
	select_teil.innerHTML=select_teil_innerHTML;
	
	if(td_select_teil.hasChildNodes())
	{
		// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
		td_select_teil.replaceChild(select_teil,td_select_teil.firstChild);
	}
	else
	{
		// jinak tam pouze vlozim inputbox
		td_select_teil.appendChild(select_teil);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////////////////////////////
	// select pro abgnr
	/////////////////////////////////////////////////////////////////////////////////////////////////
	var abgnrarray = xml.getElementsByTagName('taetnr');
	var td_select_abgnr = document.getElementById('td_select_abgnr'+dauftr_id);
	//td_select_lager_von.style.backgroundColor='red';
	var select_abgnr = document.createElement('select');
	select_abgnr.setAttribute('class','edit_dpos');
	select_abgnr.setAttribute('name','abgnr');
	select_abgnr.setAttribute('id','abgnr'+dauftr_id);
	select_abgnr.setAttribute('onchange',onblur_function[5]);
	
	select_abgnr_obsah_old=old_values[5];
	//alert('old_values[8]='+trim(lager_select_von_obsah_old));
	// prvni bude prazdnej
	select_abgnr_innerHTML="<option> </option>";
	
	for(i=0;i<abgnrarray.length;i++)
	{
		if(abgnrarray.item(i).firstChild.data==select_abgnr_obsah_old)
			select_abgnr_innerHTML+="<option selected>"+abgnrarray.item(i).firstChild.data+"</option>";
		else
			select_abgnr_innerHTML+="<option>"+abgnrarray.item(i).firstChild.data+"</option>";
	}
	
	
	select_abgnr.innerHTML=select_abgnr_innerHTML;
	
	if(td_select_abgnr.hasChildNodes())
	{
		// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
		td_select_abgnr.replaceChild(select_abgnr,td_select_abgnr.firstChild);
	}
	else
	{
		// jinak tam pouze vlozim inputbox
		td_select_abgnr.appendChild(select_abgnr);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////////////////////////////
	// select pro mehrarb_kz
	/////////////////////////////////////////////////////////////////////////////////////////////////
	var mehrarb_kzarray = xml.getElementsByTagName('dtaetkz');
	var td_select_mehrarb_kz = document.getElementById('td_select_mehrarb_kz'+dauftr_id);
	//td_select_lager_von.style.backgroundColor='red';
	var select_mehrarb_kz = document.createElement('select');
	select_mehrarb_kz.setAttribute('class','edit_dpos');
	select_mehrarb_kz.setAttribute('name','mehrarb_kz');
	select_mehrarb_kz.setAttribute('id','mehrarb_kz'+dauftr_id);
	select_mehrarb_kz.setAttribute('onchange',onblur_function[4]);
	
	select_mehrarb_kz_obsah_old=old_values[4];
	//alert('old_values[8]='+trim(lager_select_von_obsah_old));
	// prvni bude prazdnej
	select_mehrarb_kz_innerHTML="<option> </option>";
	
	for(i=0;i<mehrarb_kzarray.length;i++)
	{
		if(mehrarb_kzarray.item(i).firstChild.data==select_mehrarb_kz_obsah_old)
			select_mehrarb_kz_innerHTML+="<option selected>"+mehrarb_kzarray.item(i).firstChild.data+"</option>";
		else
			select_mehrarb_kz_innerHTML+="<option>"+mehrarb_kzarray.item(i).firstChild.data+"</option>";
	}
	
	
	select_mehrarb_kz.innerHTML=select_mehrarb_kz_innerHTML;
	
	if(td_select_mehrarb_kz.hasChildNodes())
	{
		// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
		td_select_mehrarb_kz.replaceChild(select_mehrarb_kz,td_select_mehrarb_kz.firstChild);
	}
	else
	{
		// jinak tam pouze vlozim inputbox
		td_select_mehrarb_kz.appendChild(select_mehrarb_kz);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////


	// na konci tabulky zmenim edit na save a cancel
	var list_item_save = document.createElement('li');
	var list_item_cancel = document.createElement('li');
	
	var e_save = document.createElement('a');
	e_save.setAttribute('href','#');
	e_save.setAttribute('id',radekid);
	e_save.setAttribute('onclick','save(this);');
	var e_save_text = document.createTextNode('save');
	e_save.appendChild(e_save_text);
	
	var e_cancel = document.createElement('a');
	e_cancel.setAttribute('href','#');
	e_cancel.setAttribute('id',radekid);
	e_cancel.setAttribute('onclick','canceledit(this);');
	var e_cancel_text = document.createTextNode('cancel');
	e_cancel.appendChild(e_cancel_text);
	
	list_item_save.appendChild(e_save);
	list_item_cancel.appendChild(e_cancel);
	
	bunky_array.item(promenne.length).replaceChild(list_item_save,bunky_array.item(promenne.length).firstChild);
	bunky_array.item(promenne.length).appendChild(list_item_cancel);
}