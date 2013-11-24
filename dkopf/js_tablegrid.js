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
	dposid=control.id.substring(4,control.id.length);
	e_edit.setAttribute('onclick',"getDataReturnXml('./edit_dpos_row.php?dpos_id='+dposid, edit);");
	var e_edit_text = document.createTextNode('edit');
	e_edit.appendChild(e_edit_text);
	e_operace.appendChild(e_edit);
	// prekodovani do cp1250
	
	
	
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
	dposid=control.id.substring(4,control.id.length);
	e_edit.setAttribute('onclick',"getDataReturnXml('./edit_dpos_row.php?dpos_id='+dposid, edit);");
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
	
	// pomoci AJAX updatnu radek v DPOS
	getDataReturnXml('./update_dpos.php?'+parametry, update_dpos)
	
	//alert(parametry);
}

function deletedpos(control)
{
	var radek=document.getElementById(control.id).parentNode.parentNode.parentNode;
	
	// dotaz, jestli to myslim vazne ?
	odpoved = confirm("Wirklich diese Position loeschen ? / opravdu smazat tuto pozici ?");
	if(odpoved)
	{
		//alert('id radku='+radek.id);
		// odstranim radek z dokumentu
		//alert('index radku='+radek.rowIndex);
		// zjistim si odkaz na tabulku, ve ktere je muj radek
		var tabulkaid = radek.parentNode.parentNode.id;
		var tabulka = document.getElementById(tabulkaid);
		//alert('id tabulky = '+tabulkaid);
		// a smaznu radek
		tabulka.deleteRow(radek.rowIndex);

		//parametry=parametry.substring(0,parametry.length-1);
		parametry = '';
		parametry+="id="+control.id.substring(4,control.id.length);
		parametry=encodeURI(parametry);
		//alert('parametry = '+parametry);
		// pomoci AJAX updatnu radek v DPOS
		getDataReturnXml('./delete_dpos.php?'+parametry, update_dpos);
		return;
	}
	else
		return;
}


function edit(xml)
{
	
	//alert(xml);
	//alert(control.id);
	var radekid='edit'+xml.getElementsByTagName('dpos_id').item(0).firstChild.data;
	// posunout se z odkazu az na radkovy element, tj. o dve urovne nahoru
	//alert(radekid);
	var radek = document.getElementById(radekid).parentNode.parentNode;
	
	// zvyraznim radek barvou pozadi - je editovan
	radek.style.backgroundColor='red';
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
		policko.setAttribute('value',trim(obsah_bunky));
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
	dpos_id=radekid.substring(4,radekid.length);
	// odkazu se na seznam povolenych hodnot pro sklady
	var lagerarray = xml.getElementsByTagName('lagernr');

	/////////////////////////////////////////////////////////////////////////////////////////////////
	// select pro lager_von
	/////////////////////////////////////////////////////////////////////////////////////////////////
	var td_select_lager_von = document.getElementById('td_select_lager_von'+dpos_id);
	//td_select_lager_von.style.backgroundColor='red';
	var lagerselect_von = document.createElement('select');
	lagerselect_von.setAttribute('class','edit_dpos');
	lagerselect_von.setAttribute('name','lager_von');
	lagerselect_von.setAttribute('id','lager_von');
	
	lager_select_von_obsah_old=old_values[8];
	//alert('old_values[8]='+trim(lager_select_von_obsah_old));
	// prvni bude prazdnej
	lagerselect_innerHTML="<option> </option>";
	
	for(i=0;i<lagerarray.length;i++)
	{
		if(lagerarray.item(i).firstChild.data==lager_select_von_obsah_old)
			lagerselect_innerHTML+="<option selected>"+lagerarray.item(i).firstChild.data+"</option>";
		else
			lagerselect_innerHTML+="<option>"+lagerarray.item(i).firstChild.data+"</option>";
	}
	
	
	lagerselect_von.innerHTML=lagerselect_innerHTML;
	
	if(td_select_lager_von.hasChildNodes())
	{
		// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
		td_select_lager_von.replaceChild(lagerselect_von,td_select_lager_von.firstChild);
	}
	else
	{
		// jinak tam pouze vlozim inputbox
		td_select_lager_von.appendChild(lagerselect_von);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////////////////////////////
	// select pro lager_nach
	/////////////////////////////////////////////////////////////////////////////////////////////////
	var td_select_lager_nach = document.getElementById('td_select_lager_nach'+dpos_id);
	//td_select_lager_von.style.backgroundColor='red';
	var lagerselect_nach = document.createElement('select');
	lagerselect_nach.setAttribute('class','edit_dpos');
	lagerselect_nach.setAttribute('name','lager_nach');
	lagerselect_nach.setAttribute('id','lager_nach');
	
	lager_select_nach_obsah_old=old_values[9];
	
	// prvni bude prazdnej
	lagerselect_innerHTML="<option> </option>";
	
	for(i=0;i<lagerarray.length;i++)
	{
		if(lagerarray.item(i).firstChild.data==lager_select_nach_obsah_old)
			lagerselect_innerHTML+="<option selected>"+lagerarray.item(i).firstChild.data+"</option>";
		else
			lagerselect_innerHTML+="<option>"+lagerarray.item(i).firstChild.data+"</option>";
	}
	
	
	lagerselect_nach.innerHTML=lagerselect_innerHTML;
	
	if(td_select_lager_nach.hasChildNodes())
	{
		// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
		td_select_lager_nach.replaceChild(lagerselect_nach,td_select_lager_nach.firstChild);
	}
	else
	{
		// jinak tam pouze vlozim inputbox
		td_select_lager_nach.appendChild(lagerselect_nach);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////////////////////////////
	// select pro taetnr
	/////////////////////////////////////////////////////////////////////////////////////////////////
	// odkazu se na seznam povolenych hodnot pro sklady
	var taetnrarray = xml.getElementsByTagName('taetnr');
	var td_select_taetnr = document.getElementById('td_select_taetnr'+dpos_id);
	//td_select_lager_von.style.backgroundColor='red';
	var taetnrselect = document.createElement('select');
	taetnrselect.setAttribute('class','edit_dpos');
	taetnrselect.setAttribute('name','taetnr');
	taetnrselect.setAttribute('id','taetnr');
	
	taetnrselect_obsah_old=old_values[1];
	
	taetnrselectH=''; 
	
	for(i=0;i<taetnrarray.length;i++)
	{
		if(taetnrarray.item(i).firstChild.data==taetnrselect_obsah_old)
			taetnrselectH += "<option selected>"+taetnrarray.item(i).firstChild.data+"</option>";
		else
			taetnrselectH += "<option>"+taetnrarray.item(i).firstChild.data+"</option>";
	}
	
	
	taetnrselect.innerHTML=taetnrselectH;
	
	if(td_select_taetnr.hasChildNodes())
	{
		// pokud pole tabulky obsahuje dajaka textova data, tak je vymenim za input box
		td_select_taetnr.replaceChild(taetnrselect,td_select_taetnr.firstChild);
	}
	else
	{
		// jinak tam pouze vlozim inputbox
		td_select_taetnr.appendChild(taetnrselect);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////


	// na konci tabulky zmenim edit na save a cancel
	var list_item_save = document.createElement('li');
	var list_item_cancel = document.createElement('li');
	var list_item_delete = document.createElement('li');
	
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

	var e_delete = document.createElement('a');
	e_delete.setAttribute('href','#');
	e_delete.setAttribute('id',radekid);
	e_delete.setAttribute('onclick','deletedpos(this);');
	var e_delete_text = document.createTextNode('delete');
	e_delete.appendChild(e_delete_text);
	
	list_item_save.appendChild(e_save);
	list_item_cancel.appendChild(e_cancel);
	list_item_delete.appendChild(e_delete);
	
	bunky_array.item(promenne.length).replaceChild(list_item_save,bunky_array.item(promenne.length).firstChild);
	bunky_array.item(promenne.length).appendChild(list_item_cancel);
	bunky_array.item(promenne.length).appendChild(list_item_delete);
}