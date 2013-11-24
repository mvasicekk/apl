var old_values = new Array(10);

EventUtil.addEventHandler(window,"resize",rebuildpage);

function rebuildpage()
{
	// zjistim formatovaci udaje pro formular
	var clientHeight = document.getElementById('export_fullen_form_footer').parentNode.clientHeight;
	var footerHeight = document.getElementById('export_fullen_form_footer').clientHeight;
	var clientWidth = document.getElementById('export_fullen_form_footer').parentNode.clientWidth;
	//var souradniceNode = document.getElementById('souradnice');
	var aplNode = document.getElementById('import_table');
	var aplNode1 = document.getElementById('export_table');
	var offsetTopAplNode = aplNode.offsetTop;
	var scrollAplNode = document.getElementById('scroll_import');
	var scrollAplNode1 = document.getElementById('scroll_export');
	
	aplNode.style.height=clientHeight-offsetTopAplNode-footerHeight-20-65;
	aplNode1.style.height=clientHeight-offsetTopAplNode-footerHeight-20-65;
	vyskaApl=clientHeight-offsetTopAplNode-footerHeight-20-65;
	
	scrollAplNode.style.height=vyskaApl;
	scrollAplNode1.style.height=vyskaApl;
	
	//textSouradnice='clientHeight='+clientHeight+'<br>clientWidth='+clientWidth;
	//textSouradnice+='<br>aplNode='+aplNode+'<br>offsetTopAplNode='+offsetTopAplNode;
	//textSouradnice+='<br>vyskaApl='+vyskaApl;
	
	//souradniceNode.innerHTML=textSouradnice;
}
// naplni mi skryty prvek formulare seznamem id, ktere chci exportovat
// exportovany seznam ma tvar id:gutstk:auss2:auss4:auss6,id:gut:.....

function fillIdlist()
{
	var extable = document.getElementById('extable');
	var rows = extable.getElementsByTagName('tr');
	var e_list = document.getElementById('idlist');

	var list="";
        // tabulka s polozkama pro export
        // 5 je pocet dobr. kusu z drueck

	for(i=1;i<rows.length;i++)
	{
		id = rows.item(i).getAttribute('id').substring(2);
		gut = rows.item(i).getElementsByTagName('td').item(6).firstChild.data;
		auss2 = rows.item(i).getElementsByTagName('td').item(7).firstChild.data;
		auss4 = rows.item(i).getElementsByTagName('td').item(8).firstChild.data;
		auss6 = rows.item(i).getElementsByTagName('td').item(9).firstChild.data;
		pal = rows.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data;
		
		trida = rows.item(i).getAttribute('class');
		if(trida=='highlightrow')
			kzgut = 'G';
		else
			kzgut = 'N'; 
		
		list = list + id + ":" + gut + ":" + auss2 + ":" + auss4 + ":" + auss6 + ":" + pal + ":" + kzgut + ",";
	}

	e_list.value=list;
}

function fillIdListLoeschen()
{
	var loeschtable = document.getElementById('extable');
	var rows = loeschtable.getElementsByTagName('tr');
	var e_list = document.getElementById('idlist');

	var list='';

	for(i=1;i<rows.length;i++)
	{
		auftrag = rows.item(i).getElementsByTagName('td').item(0).firstChild.data;
		pal = rows.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data;
		list = list + auftrag + ":" + pal + ",";
	}

	e_list.value = list;
}


function exportAll()
{
	// vytvorim si pole s radkama tabulky
	var imtable = document.getElementById('imtable');
	var rows = imtable.getElementsByTagName('tr');

	for(i=1;i<rows.length;i++)
	{
		import2export(rows.item(i));
		i=1;
	}

}


function loeschenAll()
{
	// vytvorim si pole s radkama tabulky
	var imtable = document.getElementById('imtable');
	var rows = imtable.getElementsByTagName('tr');

	for(i=1;i<rows.length;i++)
	{
		export2null(rows.item(i));
		i=1;
	}

}

function insertExport(rows_array,i)
{
	var extabulka = document.getElementById('extable');
	var row = document.createElement('tr');
	row.setAttribute('class','even');
	row.setAttribute('onclick','export2import(this);');

	// pokud jde o G cinnost tak radek zvyraznim
	if(rows_array.item(i).getElementsByTagName('td').item(10).hasChildNodes() && rows_array.item(i).getElementsByTagName('td').item(10).firstChild.data=='G')
		row.setAttribute('class','highlightrow');

	// nastavim id kazdemu radku stejne jako u importu
	idimport = rows_array.item(i).getAttribute('id');
	// id je ve tvaru imXXXXX, vezmu si jen cislo
	idexport  ='ex'+idimport.substring(2);
	//alert(idexport);
	row.setAttribute('id',idexport);

	var td_auftragsnr = document.createElement('td');
	td_auftragsnr.innerHTML=rows_array.item(i).getElementsByTagName('td').item(0).firstChild.data;
	td_auftragsnr.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(0).getAttribute('class'));

	var td_pal = document.createElement('td');
	td_pal.innerHTML='<b>'+rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data+'</b>';
	td_pal.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(1).getAttribute('class'));

	var td_teil = document.createElement('td');
	td_teil.innerHTML=rows_array.item(i).getElementsByTagName('td').item(2).firstChild.data;
	td_teil.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(2).getAttribute('class'));

	var td_imstk = document.createElement('td');
	td_imstk.innerHTML=rows_array.item(i).getElementsByTagName('td').item(3).firstChild.data;
	td_imstk.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(3).getAttribute('class'));

	var td_tatkz = document.createElement('td');
	td_tatkz.innerHTML=rows_array.item(i).getElementsByTagName('td').item(4).firstChild.data;
	td_tatkz.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(4).getAttribute('class'));

        var td_abgnr = document.createElement('td');
	td_abgnr.innerHTML=rows_array.item(i).getElementsByTagName('td').item(5).firstChild.data;
	td_abgnr.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(5).getAttribute('class'));

	var td_gutstk = document.createElement('td');
	td_gutstk.innerHTML=rows_array.item(i).getElementsByTagName('td').item(6).firstChild.data;
	td_gutstk.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(6).getAttribute('class'));

	var td_auss2 = document.createElement('td');
	td_auss2.innerHTML=rows_array.item(i).getElementsByTagName('td').item(7).firstChild.data;
	td_auss2.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(7).getAttribute('class'));

	var td_auss4 = document.createElement('td');
	td_auss4.innerHTML=rows_array.item(i).getElementsByTagName('td').item(8).firstChild.data;
	td_auss4.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(8).getAttribute('class'));

	var td_auss6 = document.createElement('td');
	td_auss6.innerHTML=rows_array.item(i).getElementsByTagName('td').item(9).firstChild.data;
	td_auss6.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(9).getAttribute('class'));


	row.appendChild(td_auftragsnr);
	row.appendChild(td_pal);
	row.appendChild(td_teil);
        row.appendChild(td_imstk);
	row.appendChild(td_tatkz);
	row.appendChild(td_abgnr);
	row.appendChild(td_gutstk);
	row.appendChild(td_auss2);
	row.appendChild(td_auss4);
	row.appendChild(td_auss6);
	extabulka.appendChild(row);
}

function insertnullExport(rows_array,i)
{
	var extabulka = document.getElementById('extable');
	var row = document.createElement('tr');
	row.setAttribute('class','even');
	row.setAttribute('onclick','null2export(this);');


	// nastavim id kazdemu radku stejne jako u importu
	idexport = rows_array.item(i).getAttribute('id');
	// id je ve tvaru imXXXXX, vezmu si jen cislo
	idnull  ='nu'+idexport.substring(2);
	//alert(idexport);
	row.setAttribute('id',idnull);


	var td_pal = document.createElement('td');
	td_pal.innerHTML='<b>'+rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data+'</b>';
	td_pal.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(1).getAttribute('class'));
	
	var td_auftragsnr = document.createElement('td');
	td_auftragsnr.innerHTML=rows_array.item(i).getElementsByTagName('td').item(0).firstChild.data;
	td_auftragsnr.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(0).getAttribute('class'));

	var td_teil = document.createElement('td');
	td_teil.innerHTML=rows_array.item(i).getElementsByTagName('td').item(2).firstChild.data;
	td_teil.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(2).getAttribute('class'));

	var td_gutstk = document.createElement('td');
	td_gutstk.innerHTML=rows_array.item(i).getElementsByTagName('td').item(3).firstChild.data;
	td_gutstk.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(3).getAttribute('class'));


	row.appendChild(td_auftragsnr);
	row.appendChild(td_pal);
	row.appendChild(td_teil);
	row.appendChild(td_gutstk);
	extabulka.appendChild(row);
}


function export2null(e)
{
	var id = e.id;

	//alert("element="+e+" ma id="+id);	

	// vytvorim si pole s bunkama tabulky
	var tdarray = e.getElementsByTagName('td');
	var imtabulka = e.parentNode;

	var cislo_palety = tdarray.item(1).firstChild.firstChild.data;

	//alert('cislo palety='+cislo_palety);

	// ze stromu dokumentu vymazu vsechny radky se zjistenym cislem palety
	 var rows_array = e.parentNode.getElementsByTagName('tr');
	 //alert('tabulka ma '+rows_array.length+' radku');

	 // budu prochazet vsechny radky tabulky a pripade, ze radek obsahuje zjistene cislo palety, tak ho vymazu

	 // zacnu od 1, abych preskocil hlavicku tabulky

	 for(i=1;i<rows_array.length;i++)
	 {
		 // <tr><td><b>to chci</b></td></tr>
		//alert(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data);
		if(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data==cislo_palety)
		{
			insertnullExport(rows_array,i);
			//alert('provedu presun palety '+cislo_palety);
			imtabulka.removeChild(rows_array.item(i));
			i=0;
		}

	 }

}



function import2export(e)
{
	var id = e.id;

	//alert("element="+e+" ma id="+id);	

	// vytvorim si pole s bunkama tabulky
	var tdarray = e.getElementsByTagName('td');
	var imtabulka = e.parentNode;

	var cislo_palety = tdarray.item(1).firstChild.firstChild.data;
	var cislo_importu = tdarray.item(0).firstChild.data;
	

	//alert('cislo palety='+cislo_palety);

	// ze stromu dokumentu vymazu vsechny radky se zjistenym cislem palety
	 var rows_array = e.parentNode.getElementsByTagName('tr');
	 //alert('tabulka ma '+rows_array.length+' radku');

	 // budu prochazet vsechny radky tabulky a pripade, ze radek obsahuje zjistene cislo palety, tak ho vymazu

	 // zacnu od 1, abych preskocil hlavicku tabulky

//	 for(i=1;i<rows_array.length;i++)
//	 {
//		 // <tr><td><b>to chci</b></td></tr>
//		alert(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data);
//        // nebudou me zajimat oddelovaci radky
//        var tridaRadku = rows_array.item(i).className;
//        alert('tridaRadku='+tridaRadku);
//        if(tridaRadku != 'oddel_paletu'){
//            if(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data==cislo_palety)
//            {
//                insertExport(rows_array,i);
//                imtabulka.removeChild(rows_array.item(i));
//                i=0;
//            }
//        }
//	 }

     for(i=2;i<rows_array.length;i++)
	 {
        // nebudou me zajimat oddelovaci radky
        // nepujdou projit vsechny radku FZ
        var tridaRadku = rows_array.item(i).className;
        //alert('tridaRadku='+tridaRadku);
        if(tridaRadku != 'oddel_paletu'){
            if((rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data==cislo_palety)&&(rows_array.item(i).getElementsByTagName('td').item(0).firstChild.data==cislo_importu))
            {
                insertExport(rows_array,i);
                imtabulka.removeChild(rows_array.item(i));
                i=0;
            }
        }
	 }
}

function insertImport(rows_array,i)
{
	var imtabulka = document.getElementById('imtable');
	var row = document.createElement('tr');
	row.setAttribute('class','even');
	row.setAttribute('onclick','import2export(this);');

	// pokud jde o G cinnost tak radek zvyraznim
	trida=rows_array.item(i).getAttribute('class');
	// pokud jde o radek s G, tak budu mit v trida=highlightrow

	if(trida=='highlightrow')
		row.setAttribute('class','highlightrow');
	else
		row.setAttribute('class','even');

	// nastavim id kazdemu radku stejne jako u importu
	idexport = rows_array.item(i).getAttribute('id');
	// id je ve tvaru imXXXXX, vezmu si jen cislo
	idimport  ='im'+idexport.substring(2);
	//alert(idexport);
	row.setAttribute('id',idimport);


	var td_auftragsnr = document.createElement('td');
	td_auftragsnr.innerHTML=rows_array.item(i).getElementsByTagName('td').item(0).firstChild.data;
	td_auftragsnr.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(0).getAttribute('class'));

	var td_pal = document.createElement('td');
	td_pal.innerHTML='<b>'+rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data+'</b>';
	td_pal.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(1).getAttribute('class'));
	
	var td_teil = document.createElement('td');
	td_teil.innerHTML=rows_array.item(i).getElementsByTagName('td').item(2).firstChild.data;
	td_teil.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(2).getAttribute('class'));

        var td_imstk = document.createElement('td');
	td_imstk.innerHTML=rows_array.item(i).getElementsByTagName('td').item(3).firstChild.data;
	td_imstk.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(3).getAttribute('class'));

	var td_tatkz = document.createElement('td');
	td_tatkz.innerHTML=rows_array.item(i).getElementsByTagName('td').item(4).firstChild.data;
	td_tatkz.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(4).getAttribute('class'));

        var td_abgnr = document.createElement('td');
	td_abgnr.innerHTML=rows_array.item(i).getElementsByTagName('td').item(5).firstChild.data;
	td_abgnr.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(5).getAttribute('class'));

	var td_gutstk = document.createElement('td');
	td_gutstk.innerHTML=rows_array.item(i).getElementsByTagName('td').item(6).firstChild.data;
	td_gutstk.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(6).getAttribute('class'));

	var td_auss2 = document.createElement('td');
	td_auss2.innerHTML=rows_array.item(i).getElementsByTagName('td').item(7).firstChild.data;
	td_auss2.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(7).getAttribute('class'));

	var td_auss4 = document.createElement('td');
	td_auss4.innerHTML=rows_array.item(i).getElementsByTagName('td').item(8).firstChild.data;
	td_auss4.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(8).getAttribute('class'));

	var td_auss6 = document.createElement('td');
	td_auss6.innerHTML=rows_array.item(i).getElementsByTagName('td').item(9).firstChild.data;
	td_auss6.setAttribute('class',rows_array.item(i).getElementsByTagName('td').item(9).getAttribute('class'));

	var td_kzgut = document.createElement('td')
	if(trida=='highlightrow')
		td_kzgut.innerHTML='G';
	else
		td_kzgut.innerHTML='';
	td_kzgut.setAttribute('class','left');


	row.appendChild(td_auftragsnr);
	row.appendChild(td_pal);
	row.appendChild(td_teil);
        row.appendChild(td_imstk);
	row.appendChild(td_tatkz);
	row.appendChild(td_abgnr);
	row.appendChild(td_gutstk);
	row.appendChild(td_auss2);
	row.appendChild(td_auss4);
	row.appendChild(td_auss6);
	row.appendChild(td_kzgut);
	imtabulka.appendChild(row);
}

function export2import(e)
{
	var id = e.id;

	//alert("element="+e+" ma id="+id);	

	// vytvorim si pole s bunkama tabulky
	var tdarray = e.getElementsByTagName('td');
	var extabulka = e.parentNode;

	var cislo_palety = tdarray.item(1).firstChild.firstChild.data;

	//alert('cislo palety='+cislo_palety);

	// ze stromu dokumentu vymazu vsechny radky se zjistenym cislem palety
	 var rows_array = extabulka.getElementsByTagName('tr');
	 //alert('tabulka ma '+rows_array.length+' radku');

	 // budu prochazet vsechny radky tabulky a pripade, ze radek obsahuje zjistene cislo palety, tak ho vymazu

	 // zacnu od 1, abych preskocil hlavicku tabulky

	 for(i=1;i<rows_array.length;i++)
	 {
		 // <tr><td><b>to chci</b></td></tr>
		//alert(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data);
		if(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data==cislo_palety)
		{
			insertImport(rows_array,i);
			extabulka.removeChild(rows_array.item(i));
			i=0;
		}

	 }

}

function null2export(e)
{
	var id = e.id;

	//alert("element="+e+" ma id="+id);	

	// vytvorim si pole s bunkama tabulky
	var tdarray = e.getElementsByTagName('td');
	var extabulka = e.parentNode;

	var cislo_palety = tdarray.item(1).firstChild.firstChild.data;

	//alert('cislo palety='+cislo_palety);

	// ze stromu dokumentu vymazu vsechny radky se zjistenym cislem palety
	 var rows_array = extabulka.getElementsByTagName('tr');
	 //alert('tabulka ma '+rows_array.length+' radku');

	 // budu prochazet vsechny radky tabulky a pripade, ze radek obsahuje zjistene cislo palety, tak ho vymazu

	 // zacnu od 1, abych preskocil hlavicku tabulky

	 for(i=1;i<rows_array.length;i++)
	 {
		 // <tr><td><b>to chci</b></td></tr>
		//alert(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data);
		if(rows_array.item(i).getElementsByTagName('td').item(1).firstChild.firstChild.data==cislo_palety)
		{
			//insertnullNull(rows_array,i);
			extabulka.removeChild(rows_array.item(i));
			i=0;
		}

	 }

}



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


function edit(xml)
{
	
	//alert(xml);
	//alert(control.id);
	var radekid='edit'+xml.getElementsByTagName('dpos_id').item(0).firstChild.data;
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
	
	for(i=0;i<taetnrarray.length;i++)
	{
		if(taetnrarray.item(i).firstChild.data==taetnrselect_obsah_old)
			taetnrselect_innerHTML+="<option selected>"+taetnrarray.item(i).firstChild.data+"</option>";
		else
			taetnrselect_innerHTML+="<option>"+taetnrarray.item(i).firstChild.data+"</option>";
	}
	
	
	taetnrselect.innerHTML=taetnrselect_innerHTML;
	
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


function encodeControlValue(i)
{
	var control = document.getElementById(i);
	return encodeURI(control.value);
} 



function loeschenRefresh(xml)
{
/*
	var auftragsnrArray = xml.getElementsByTagName('auftragsnr');
	var palArray = xml.getElementsByTagName('pal');
	var sqlArray = xml.getElementsByTagName('sql');
	var mysqlerrorArray = xml.getElementsByTagName('mysqlerror');


	auftragsnrlist='';
	pallist='';
	sqllist='';
	errorlistlist='';

	for(i=0;i<palArray.length-1;i++)
	{
		auftragsnrlist+=auftragsnrArray.item(i).firstChild.data+',';	
		pallist+=palArray.item(i).firstChild.data+',';	
		sqllist+=sqlArray.item(i).firstChild.data+',';	
		errorlist+=mysqlerrorArray.item(i).firstChild.data+',';	
	}
*/
	//alert('\nauftragsnr='+auftragsnrlist+'\npal='+pallist+'\nsql='+sqllist+'errorlist='+errorlist);
	alert("geloescht ! vymazano !");
}

function fullenRefresh(xml)
{

	var idArray = xml.getElementsByTagName('id');

	var gutArray = xml.getElementsByTagName('gut');
	var auss2Array = xml.getElementsByTagName('auss2');
	var auss4Array = xml.getElementsByTagName('auss4');
	var auss6Array = xml.getElementsByTagName('auss6');
	var palArray = xml.getElementsByTagName('pal');
	var sqlArray = xml.getElementsByTagName('sql');
	var kzgutArray = xml.getElementsByTagName('kzgut');
	
	var mysqlerrorArray = xml.getElementsByTagName('mysqlerror');

	var ex='';

	if(xml.getElementsByTagName('export').item(0).hasChildNodes())
		ex = xml.getElementsByTagName('export').item(0).firstChild.data;
	else
	{
		alert('neni zadan export');
		return;
	}

	//alert("delka pole idArray="+idArray.length);
	idlist='';
	gutlist='';
	auss2list='';
	auss4list='';
	auss6list='';
	pallist='';
	sqllist='';
	kzgutlist='';
	errorlist='';

	for(i=0;i<idArray.length-1;i++)
	{
		idlist+=idArray.item(i).firstChild.data+',';	
		gutlist+=gutArray.item(i).firstChild.data+',';	
		auss2list+=auss2Array.item(i).firstChild.data+',';	
		auss4list+=auss4Array.item(i).firstChild.data+',';	
		auss6list+=auss6Array.item(i).firstChild.data+',';	
		pallist+=palArray.item(i).firstChild.data+',';	
		sqllist+=sqlArray.item(i).firstChild.data+',';	
		kzgutlist+=kzgutArray.item(i).firstChild.data+',';
		errorlist+=mysqlerrorArray.item(i).firstChild.data+',';	
	}
	//alert('export='+ex+'\nid='+idlist+'\ngut='+gutlist+'\nauss2='+auss2list+'\nauss4='+auss4list+'\nauss6='+auss6list+'\npal='+pallist+'\nsql='+sqllist+'\nkzgutlist='+kzgutlist+'errorlist='+errorlist);


	var exportE = document.getElementById('export');
	
	var errorarray = xml.getElementsByTagName('error');
	if(errorarray.length>0)
	{
		var textChyby = errorarray.item(0).firstChild.data;
		exportE.style.backgroundColor='red';
		
		// zvyraznim chybu u exportniho cisla
		re=/vyfakturovana/i;
		if(textChyby.match(re))
		{
		}
		
		alert(errorarray.item(0).firstChild.data);
	}
	else
	{
		exportE.style.backgroundColor='';
		alert("gefullt ! vyexportovano !");
		window.history.back();
	}
}
