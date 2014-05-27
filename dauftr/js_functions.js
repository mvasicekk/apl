// JavaScript Document

EventUtil.addEventHandler(window,"resize",rebuildpage);


function rebuildpage()
{
	// zjistim formatovaci udaje pro formular
	var clientHeight = document.getElementById('dauftr_form_footer').parentNode.clientHeight;
	var footerHeight = document.getElementById('dauftr_form_footer').clientHeight;
	var clientWidth = document.getElementById('dauftr_form_footer').parentNode.clientWidth;
	var souradniceNode = document.getElementById('souradnice');
	var aplNode = document.getElementById('dauftr_table');
	var offsetTopAplNode = aplNode.offsetTop;
	var scrollAplNode = document.getElementById('scroll_apl');
	
	//aplNode.style.height=clientHeight-offsetTopAplNode-footerHeight-20;
	vyskaApl=clientHeight-offsetTopAplNode-footerHeight-20;
	
	scrollAplNode.style.height=vyskaApl;
	
	textSouradnice='clientHeight='+clientHeight+'<br>clientWidth='+clientWidth;
	textSouradnice+='<br>aplNode='+aplNode+'<br>offsetTopAplNode='+offsetTopAplNode;
	textSouradnice+='<br>vyskaApl='+vyskaApl;
	
	//souradniceNode.innerHTML=textSouradnice;
}

// Removes leading whitespaces
function LTrim( value ) {
	
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	
}

// Removes ending whitespaces
function RTrim( value ) {
	
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	
}

// Removes leading and ending whitespaces
function trim( value ) {
	
	return LTrim(RTrim(value));
	
}



function refreshdatum(text)
{
	//alert(text);
	
	// control id je posladni cast textu pred znakem >
	var controlid=text.substring(text.lastIndexOf('>')+1,text.length);
	// hodnota pro zobrazeni v textboxu je od zacatku retezce, po znamenko >
	var hodnota=text.substring(0,text.indexOf('>'));
	// objekt s obsahem ke zmene
	var control = document.getElementById(controlid);
	
	if(hodnota=="ERROR")
	{
		control.style.backgroundColor='red';
		control.value=hodnota;
	}
	else
	{
		control.style.backgroundColor='';
		control.value=hodnota;
	}
}

/*
function validatedatum()
{
	var datum = document.getElementById('Datum');
	//alert(datum.value);	
}
*/

function refreshteil(text)
{
	var teil = document.getElementById('teil');
	var teilbez = document.getElementById('teilbez');
	var kunde = document.getElementById('kunde');
	
	if(text=="NOTEIL")
	{
		teil.value=text;
		teilbez.value="";
		kunde.value="";
	}
	else
	{
		
		//teil.value=text;
		teil.value=text.substring(0,text.indexOf(';'));
		teilbez.value=text.substring(text.indexOf(';')+1,text.lastIndexOf(';'));
		kunde.value=text.substring(text.lastIndexOf(';')+1);
		teil.select();
	}	
}	

function refreshteillang(text)
{
	var teillang = document.getElementById('teillang');
	var teilbez = document.getElementById('teilbez');
	var kunde = document.getElementById('kunde');
	
	if(text=="NOTEIL")
	{
		teillang.value=text;
		teilbez.value="";
		kunde.value="";
	}
	else
	{
		// rozkouskovat retezec na polozky teillang,teilbez,kunde podle stredniku
		teillang.value=text.substring(0,text.indexOf(';'));
		teilbez.value=text.substring(text.indexOf(';')+1,text.lastIndexOf(';'));
		kunde.value=text.substring(text.lastIndexOf(';')+1);
	}	
}	



function pissuggest(xml)
{
	//var foot = document.getElementById('form_footer_tlacitka_reporty');
	var foot = document.getElementById('suggest');
	var scroll = document.getElementById('scroll');
	var kw = document.getElementById('auftragsnr');

	var div ="";
	
	

	auftragsnrArray = xml.getElementsByTagName('auftragsnr');
	kundeArray =xml.getElementsByTagName('kunde');
	bestellnrArray = xml.getElementsByTagName('bestellnr');
	aufdatArray = xml.getElementsByTagName('aufdat');
	fertigArray = xml.getElementsByTagName('fertig');
	ausliefer_datumArray = xml.getElementsByTagName('ausliefer_datum');
	
	//alert(auftragsnrArray.length);
	
// vytvorim tabulku s vysledkama
	var div="<table id='vysledky'>";
	// pokud mam nejake vysledky, zobrazim hlavicku tabulky
	if(auftragsnrArray.length>0)
	{
		div+="<tr class='result_table_header'><td>Kunde</td><td>Auftragsnr</td><td>Bestellnr</td><td>Auftragsdatum</td><td>Rechnung am</td><td>ausgeliefert am</td></tr>";
	}
	for(i=0;i<auftragsnrArray.length;i++)
	{
		auftragsnr=auftragsnrArray.item(i).firstChild.data;
		var fertig = fertigArray.item(i).firstChild.data;
		
		// oznacim radky s hotovou fakturou
		if(fertig == "2100-01-01")
			rowclass='';
		else
			rowclass='fertigrow';
			
		div+="<tr class='"+rowclass+"' id='tr"+i+"' onclick='handleOnClickSuggest(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>";
		div+="<td>"+kundeArray.item(i).firstChild.data+"</td>";
		div+="<td id='a"+i+"'>"+auftragsnrArray.item(i).firstChild.data+"</td>";
		if(bestellnrArray.item(i).hasChildNodes())
			div+="<td>"+bestellnrArray.item(i).firstChild.data+"</td>";
		else
			div+="<td> </td>";
			
		div+="<td>"+aufdatArray.item(i).firstChild.data+"</td>";
		div+="<td>"+fertigArray.item(i).firstChild.data+"</td>";
		
		if(ausliefer_datumArray.item(i).hasChildNodes())
			div+="<td>"+ausliefer_datumArray.item(i).firstChild.data+"</td>";
		else
			div+="<td> </td>";
		div+="<tr>";
	}
	div+="</table>";
	
	if(foot)
		foot.innerHTML=div;
	
	if(auftragsnrArray.length>0)
	{
		scroll.style.left=getRealPos(kw,"x")+'px';//kw.style.left;
		scroll.style.top=getRealPos(kw,"y")+'px';//kw.style.top;
		//bez.value=getRealPos(kw,"x")+'px'+";"+getRealPos(kw,"y")+'px';
		scroll.style.visibility = "visible";
		scroll.style.height="200px";
		scroll.scrollTop=0;
		autocompleteKeyword();
	}
	else
	{
		scroll.style.visibility = "hidden";
		scroll.style.height="0";
	}
	
	// pokud nemam zadne vysledky a uzivatel zadal 6znaku, povolim tlacitko pro vytvoreni nove zakazky
	var neu_button=document.getElementById('auftragsnrneu');
	var kundeBox = document.getElementById('kunde');	
	
	//alert(kw.value.length);
		
	if((kw.value.length==6)&&(auftragsnrArray.length==0))
	{
		neu_button.className='showbutton';
		kundeBox.className = 'showbutton';
		// TODO: navrhnout hodnotu pole s cislem zakaznika
		// cislo zakazky podelim 1000 a vemu celou cast
		var prvniTriCisla = kw.value.substring(0,3);
		cislo = parseInt(prvniTriCisla);
		//var cislo = parseInt(kw.value)/1000;
		// test jestli je cislo delitelne 10
		// upravu budu provadet u zakaznika 140
		zbytek=cislo%10;
		if((zbytek>0)&&((cislo-zbytek)==140))
		{
			kundeBox.value=cislo-zbytek;
		}
		else
		{
                    // pomoc pri zadavani cisla zakaznika
                    if(cislo==358)  cislo=355;
                    if(cislo==350)  cislo=355;
		    if(cislo==352)  cislo=355;
                    if(cislo==106)  cislo=107;
                    if(cislo==131)  cislo=130;
		    if(cislo==132)  cislo=130;
		    if(cislo==112)  cislo=111;
		    if(cislo==113)  cislo=111;
		    if(cislo==198)  cislo=195;
                    kundeBox.value = cislo;
		}
	}
	else
	{
		neu_button.className='hidden';
		kundeBox.className = 'hidden';
	}
}

function handleOnClickSuggest(oTr)
{
	
	updateKeywordValue(oTr);
	var oKeyword = document.getElementById("auftragsnr");
	
	document.location.href='dauftr.php?auftragsnr='+oKeyword.value;
}


function getRealPos(ele,dir)
{
	pos=0;
	(dir=="x") ? pos = ele.offsetLeft : pos = ele.offsetTop;
	tempEle = ele.offsetParent;
	while(tempEle != null)
	{
		pos += (dir=="x") ? tempEle.offsetLeft : tempEle.offsetTop;
		tempEle = tempEle.offsetParent;
	}
	return pos;
}

function handleOnMouseOver(oTr)
{
	//deselectAll();
	oTr.className="highlightrow";
	position = oTr.id.substring(2,oTr.id.length);
}

function handleOnMouseOut(oTr)
{
	// zjistit obsah 5 sloupce
	var obsahPolicka = oTr.childNodes[4].firstChild.nodeValue;
	//alert(obsahPolicka);
	if(obsahPolicka=='2100-01-01')
		oTr.className="";
	else
		oTr.className="fertigrow";
	position = -1;
}

function refresh_muster_vom(text)
{
	var datum = document.getElementById('muster_vom');
	if(text=="ERROR")
	{
		//alert('Fehler bei Datumeingabe / chyba pri zadani datumu');
		datum.value=text;
		datum.style.backgroundColor="red";
		//datum.focus();
		//datum.select();
	}
	else
	{
		datum.value=text;
		datum.style.backgroundColor="";
	}	
}	

function refreshvon()
{
	var von = document.getElementById('Von');
	var hodiny,minuty;
	if(von.value.length==4)
	{
		hodiny=von.value.substr(0,2);
		minuty=von.value.substr(2,2);
		von.value=hodiny+":"+minuty;
	}	
}

function refreshbis()
{
	var von = document.getElementById('Bis');
	var hodiny,minuty;
	if(von.value.length==4)
	{
		hodiny=von.value.substr(0,2);
		minuty=von.value.substr(2,2);
		von.value=hodiny+":"+minuty;
	}	
}

function pisjmeno(text)
{
	var jmeno = document.getElementById('persName');
	var pers = document.getElementById('PersNr');
	var schicht=document.getElementById('Schicht');

	// test na existenci osobniho cisla
	// pokud ne, vratim se na pole s osobnim cislem
	if((text=="nopersnr"))
	{
		alert('PersNr nichtgefunden');
		pers.focus();
		pers.select();
	}
	else
	{
		jmeno.value=text.substring(0,text.indexOf(';'));
		schicht.value=text.substring(text.indexOf(';')+1);
		schicht.select();
	}
}

function pauza(){

refreshbis();
var von = document.getElementById("Von");
var bis = document.getElementById("Bis");
var vonh= von.value.substr(0,2);
var vonm= von.value.substr(3,2);
var bish= bis.value.substr(0,2);
var bism= bis.value.substr(3,2);
var vonsc = new Date(0,0,0,vonh,vonm,0);
var bissc = new Date(0,0,0,bish,bism,0);
var time = bissc.getTime() - vonsc.getTime();
var cas = Math.round((time / (60*60*1000))*10)/10;
var cas1 = time / (60*60*1000);
var pauza = Math.round((cas1 / 17)*10)/10 ;
void (document.getElementById('pause1').value = pauza);
void (document.getElementById('stunden').value = cas);
}


function zjistiId(element,pole){
for(i=0;i<pole.length; i++){
if(pole[i]== element){return i;}
}
}


function checkCR() {
	
	//alert('checkCR');
	var oEvent = EventUtil.getEvent();
	
	// ktere policko mi poslalo event
	var element =  oEvent.target.id;
	var policko = oEvent.target;
	
	// pokud to byl auftragsnr budu testovat dat
	if(element=='auftragsnr')
	{
		//alert('je to z auftragsnr'+element);
		// zjistim kolik znaku uz mam v textovem poli
		hodnota = policko.value;
		if(hodnota.length==6)
		{
			//alert('hodnota v policku='+hodnota);
			// mam v tabulce pod polem jeden radek ?
			// zkusim ziskat tabulku
			var tabulka = document.getElementById('vysledky');
			if(tabulka)
			{
				//alert('mam tabulku:'+tabulka);
				var radek=tabulka.rows[1];
				if(radek)
				{
					//alert('mam radek:'+radek);
					//oEvent.preventDefault();
					handleOnClickSuggest(radek);
				}
			}
		}
	}
	else
	{
    	var pole = new Array("auftragsnr","teil","pal","beh","stk","nachlager","weiter");
    
    	if (oEvent.keyCode == 13)
    	{
			var i = zjistiId(element, pole) +1;
			document.getElementById(pole[i]).focus();
			oEvent.preventDefault();
	  	}
    	else
    	{
      		return true;
    	}
    } 
}
  
EventUtil.addEventHandler(document, "keydown", checkCR);
EventUtil.addEventHandler(window,"onload",init);



function beforeSubmit(){
//alert("Pr�v� odes�l�te data!")
}

function init()
{
	// retrieve the input control for the keyword
	var oKeyword = document.getElementById("keyword");
	// prevent browser from starting the autofill function
	oKeyword.setAttribute("autocomplete", "off");
}

/* function that hides the layer containing the suggestions */
function hideSuggestions()
{
var oScroll = document.getElementById("scroll");
oScroll.style.visibility = "hidden";
oScroll.style.height="0px";
}

function autocompleteKeyword()
{
	var oKeyword = document.getElementById("auftragsnr");
	position = 0;
	deselectAll();
	
	start=oKeyword.value.length;
	document.getElementById("tr0").className="highlightrow";	
	
	// nebudu upravovat obsah textboxu s vyhledavanym textem
	//updateKeywordValue(document.getElementById("tr0"));
	//selectRange(oKeyword,start,oKeyword.value.length);
}

function updateKeywordValue(oTr)
{
	var oKeyword=document.getElementById("auftragsnr");
	
	slovo=document.getElementById("a"+oTr.id.substring(2,oTr.id.length)).childNodes[0].data;
	//slovo="a"+oTr.id.substring(2,oTr.id.length);
	oKeyword.value=slovo;
}

/* function that selects a range in the text object passed as parameter */
function selectRange(oText, start, length)
{
// check to see if in IE or FF
if (oText.createTextRange)
{
//IE
var oRange = oText.createTextRange();
oRange.moveStart("character", start);
oRange.moveEnd("character", length - oText.value.length);
oRange.select();
}
else
// FF
if (oText.setSelectionRange)
{
oText.setSelectionRange(start, length);
}
oText.focus();
}

function deselectAll()
{
	
}


function init_dauftr_form(rezim)
{
	if(rezim=='show')
	{
		// pouze rezim pro prohlizeni, pripadne editaci nekterych parametru
		var teil = document.getElementById("auftragsnr");
		var kunde = document.getElementById("kunde");
		// teil bude readonly
		teil.disabled=true;
		
		rebuildpage();
		// nastavim fokus na kunde
		//kunde.focus();
		
	}
}

function validate_kunde(xml)
{
	var kdnrarray = xml.getElementsByTagName('kundenr');
	var kunde = document.getElementById("kunde");
	var failed = document.getElementById("kunde_failed");
	
	kdnr=kdnrarray.item(0).firstChild.data;
	
	if(kdnr.substring(0,5)=="ERROR")
	{
		kunde.style.backgroundColor='red';
		failed.className='error';
	}
	else
	{
		kunde.style.backgroundColor='';
		failed.className='hidden';
	}
	
}

function validate_teil(xml)
{
	var teilnrarray = xml.getElementsByTagName('teilnr');
	var teil = document.getElementById("teil");
	var failed = document.getElementById("teil_failed");
	var error_descriptionarray = xml.getElementsByTagName('teilbez');
	
	teilnr=teilnrarray.item(0).firstChild.data;
	error_description = error_descriptionarray.item(0).firstChild.data;
	
	
	if(teilnr.substring(0,5)=="ERROR")
	{
		teil.style.backgroundColor='red';
		failed.className='error';
		failed.value=error_description;
	}
	else
	{
		teil.style.backgroundColor='';
		failed.className='hidden';
	}
	
}


function js_validate_float(control)
{

	var hodnota = control.value
	
	re = /,/
	novahodnota=hodnota.replace(re,".");
	
	floatvalue = parseFloat(novahodnota);
	
	if(!isNaN(floatvalue)&&(floatvalue>=0))
	{
		control.value=floatvalue;
		control.style.backgroundColor='';
	}
	else
	{
		//chyba validace
		control.style.backgroundColor='red';
		//failed.className='error';
		//failed.value=error_description;
	}
}

function prepoctiPreis2VzKd(){
	//alert("prepoctipreis onblur");
	var preis = document.getElementById("preis");
	var hodnota = preis.value;
	re = /,/
	novahodnota=hodnota.replace(re,".");
	floatvalue = parseFloat(novahodnota);
	if(!isNaN(floatvalue)&&(floatvalue!=0))
	{
		var minpreis = document.getElementById("minpreis");
		var vzkd = document.getElementById("vzkd");
		preis.value=floatvalue;
		vzkdvalue = Math.round((floatvalue/parseFloat(minpreis.value))*10000)/10000;
		//vzkd.firstChild.nodeValue=vzkdvalue;
		vzkd.value=vzkdvalue;
		preis.style.backgroundColor='';
	}
	else
	{
		preis.style.backgroundColor='red';
	}
}

function prepoctiVzKd2Preis(){
	//alert("prepoctipreis onblur");
	var vzkd = document.getElementById("vzkd");
	
	var hodnota = vzkd.value;
	re = /,/
	novahodnota=hodnota.replace(re,".");
	floatvalue = parseFloat(novahodnota);
	//alert("floatvalue="+floatvalue);
	if(!isNaN(floatvalue)&&(floatvalue!=0))
	{
		//alert("prepocet, vzkd="+vzkd);
		var minpreis = document.getElementById("minpreis");
		var preis = document.getElementById("preis");
		vzkd.value=floatvalue;
		preisvalue = Math.round((floatvalue*parseFloat(minpreis.value))*10000)/10000;
		//vzkd.firstChild.nodeValue=vzkdvalue;
		preis.value=preisvalue;
		vzkd.style.backgroundColor='';
	}
	else
	{
		vzkd.style.backgroundColor='red';
	}
}


function js_validate_jn(control)
{
	var hodnota=control.value
	if((hodnota.toUpperCase()=='J')||(hodnota.toUpperCase()=='N'))
	{
		control.value=hodnota.toUpperCase();
		control.style.backgroundColor='';
	}
	else
	{
		control.style.backgroundColor='red';
		control.value='';
	}
}


function toggle_kz_druck(text)
{
	
	var id=text.substr(0,text.indexOf(":"));
	var barva=text.substr(text.indexOf(":")+1);
	//alert('druck'+id);
	var control = document.getElementById('druck'+eval(id));
	
	control.style.backgroundColor=barva;
	
}


function validate_taetnr(xml)
{
	var taetnrarray = xml.getElementsByTagName('taetnr');
	var e_taetnr = document.getElementById("taetnr");
	var e_bez_d = document.getElementById("bez_d");
	var e_bez_t = document.getElementById("bez_t");
	var bez_t = xml.getElementsByTagName("bez_t");
	var bez_d = xml.getElementsByTagName("bez_d");
	
	var failed = document.getElementById("kunde_failed");
	
	taetnr=taetnrarray.item(0).firstChild.data;
	
	if(taetnr.substring(0,5)=="ERROR")
	{
		e_taetnr.style.backgroundColor='red';
		//failed.className='error';
	}
	else
	{
		e_taetnr.style.backgroundColor='';
		bez_d_value=bez_d.item(0).firstChild.data;
		bez_t_value=bez_t.item(0).firstChild.data;
		e_taetnr.value=taetnr;
		e_bez_t.value=bez_t_value;
		e_bez_d.value=bez_d_value;
		
		failed.className='hidden';
	}
	
}



function update_dauftr(xml)
{
	var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	//var sql_delete = xml.getElementsByTagName('sql_delete').item(0).firstChild.data;
	var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
    var inventurDatumArray = xml.getElementsByTagName('invdatum');
	
//	alert('inventurDatumArray='+inventurDatumArray);
//    alert('inventurDatumArray.length='+inventurDatumArray.length);
//    alert('inventurDatumArray.item(0)='+inventurDatumArray.item(0));
//    alert('inventurDatumArray.item(0).firstChild='+inventurDatumArray.item(0).nodeValue);


    
	if(mysqlerrorarray.item(0).hasChildNodes())
	{
		mysqlerror = mysqlerrorarray.item(0).firstChild.data;
		alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
	}

    if(inventurDatumArray.item(0).hasChildNodes()){
        invDatum = inventurDatumArray.item(0).firstChild.data;
        dauftrStamp = xml.getElementsByTagName('dauftrstamp').item(0).firstChild.data;
        timeBeachten = xml.getElementsByTagName('timebeachten').item(0).firstChild.data;
        if(timeBeachten>0)
            alert('Diese Position wurde vor der Lagerinventur erstellt !!'
                    +'\n\nInventurdatum: '+invDatum
                    +'\nPositiondatum: '+dauftrStamp);
    }
	//alert('sql_delete='+sql_delete);
	window.location.reload();
}



function saverefresh(xml)
{
	var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	
	
	if(mysqlerrorarray.item(0).hasChildNodes())
	{
		mysqlerror = mysqlerrorarray.item(0).firstChild.data;
		alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
	}

	location.href='./dauftr.php?auftragsnr='+xml.getElementsByTagName('auftragsnr').item(0).firstChild.nodeValue;
	//nove = window.open('');
	//nove.document.writeln('sql='+sql);
	//nove.document.writeln('affected rows='+affectedrows);
	
	//window.location.reload();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}



function encodeControlValue(i)
{
	var control = document.getElementById(i);
	return encodeURIComponent(control.value);
}


function encodeSelectControlValue(i)
{
	var control = document.getElementById(i);
	var optionarray = control.getElementsByTagName('option');
	var	selectedvalue=optionarray.item(control.selectedIndex).getAttribute('value');
	return encodeURIComponent(selectedvalue);
}


function validate_abgnr(xml)
{
	//var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	//var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	//var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	var dauftr_id=xml.getElementsByTagName('dauftr_id').item(0).firstChild.data;

	alert(dauftr_id);
	//window.location.reload();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}

function new_auftrag(xml)
{
	var auftragsnr = xml.getElementsByTagName('auftragsnr').item(0).firstChild.nodeValue;
	var kunde = xml.getElementsByTagName('kunde').item(0).firstChild.nodeValue;
	var mysqlerror = xml.getElementsByTagName('mysqlerror').item(0);

	if(mysqlerror.hasChildNodes())
	{
		alert(mysqlerror.firstChild.nodeValue);
	}
	else
	{
		//alert("zakazka vytvorena :"+"\nauftragsnr="+auftragsnr+"\nkunde="+kunde);
		location.href='./dauftr.php?auftragsnr='+auftragsnr;
	}
}

/**
 * callback pro smazani faktury
 * 
 */
 
 var delrechnung = {
 	success :	function(o){
 		var domDocument = o.responseXML;
		var runArray = domDocument.getElementsByTagName('run');
		var runValue=0;
		
		var auftragsnrArray = domDocument.getElementsByTagName('auftragsnr');
		var auftragsnrValue=0;
		
		var hasrechnungArray = domDocument.getElementsByTagName('hasrechnung');
		var hasrechnungValue = 0;
		var odpoved = 0;
		
		if(runArray.length>0)
		{
			runValue = runArray.item(0).firstChild.nodeValue;	
		}
		
		if(auftragsnrArray.length>0)
		{
			auftragsnrValue = auftragsnrArray.item(0).firstChild.nodeValue;	
		}

		//alert('runValue='+runValue);
		
		// pokud byl pozadavek spusten poprve tak jen zjistoval, jestli uz faktura pro danou zakazku existuje
		// pokud tam je, tak se zeptam, jestli opravdu znovu exportovat danou fakturu.
		if(runValue==1)
		{
			if(hasrechnungArray.length>0)
			{
				hasrechnungValue = hasrechnungArray.item(0).firstChild.nodeValue;
				//alert('exportiertValue='+exportiertValue);
				if(hasrechnungValue>0)
				{
					// faktura existuje, zeptam se jestli opravdu budu mazat
					// zeptam se, zda chci znovu
					odpoved = confirm("Opravdu smazat fakturu ?");
				}
				else
					odpoved=0;
			}
		}
		else
		{
			// jsem tu podruhe uz po smazani dat
			alert('Faktura vymazana');			
			odpoved=0;
			window.location.reload();
		}
			
		// pokud mam kladnou odpoved, tak zavolam skript pro export jeste jednou a ted uz bude opravdu 
		// vlkadat data do tabulky drechbew
		//alert('odpoved='+odpoved);
		if(odpoved)
			YAHOO.util.Connect.asyncRequest('GET','./delrechnung.php?auftragsnr='+auftragsnrValue+'&run=2', delrechnung);
 		
 	}
 }
 
 
/**
 * callback pro export faktury
 */

var exportdrech = {
	success:	function(o){
		var domDocument = o.responseXML;
		var runArray = domDocument.getElementsByTagName('run');
		var runValue=0;
		
		var auftragsnrArray = domDocument.getElementsByTagName('auftragsnr');
		var auftragsnrValue=0;
		
		var exportiertArray = domDocument.getElementsByTagName('exportiert');
		var exportiertValue = 0;
		var odpoved = 0;
		
		if(runArray.length>0)
		{
			runValue = runArray.item(0).firstChild.nodeValue;	
		}
		
		if(auftragsnrArray.length>0)
		{
			auftragsnrValue = auftragsnrArray.item(0).firstChild.nodeValue;	
		}

		//alert('runValue='+runValue);
		
		// pokud byl export spusten poprve tak jen zjistoval, jestli uz vybrane cislo faktury v exportni tabulce neni
		// pokud tam je, tak se zeptam, jestli opravdu znovu exportovat danou fakturu.
		if(runValue==1)
		{
			if(exportiertArray.length>0)
			{
				exportiertValue = exportiertArray.item(0).firstChild.nodeValue;
				//alert('exportiertValue='+exportiertValue);
				if(exportiertValue>0)
				{
					// uz jsem jednou exportoval
					// zeptam se, zda chci znovu
					odpoved = confirm("Tato faktura uz byla exportovana, exportovat znovu ?");
				}
				else
					odpoved=1;
			}
		}
		else
		{
			// jsem tu podruhe uz po vlozeni dat
			alert('data ulozena do tabulky drechbew');			
			odpoved=0;
		}
			
		// pokud mam kladnou odpoved, tak zavolam skript pro export jeste jednou a ted uz bude opravdu 
		// vlkadat data do tabulky drechbew
		//alert('odpoved='+odpoved);
		if(odpoved)
			YAHOO.util.Connect.asyncRequest('GET','./exportdrech.php?auftragsnr='+auftragsnrValue+'&run=2', exportdrech);
	}
}
//----------------------------------------------------------------------

var preisupdate = {
	success:	function(o){
		var domDocument = o.responseXML;
		
		/*
		var auftragsnrArray = domDocument.getElementsByTagName('auftragsnr');
		var auftragsnrValue=0;
		*/
		
		var levelArray = domDocument.getElementsByTagName('level');
		var levelValue=0;
		
		var id_dauftrArray = domDocument.getElementsByTagName('id_dauftr');
		var id_dauftrValue=0;
		
		/*
		var dauftrpozicArray = domDocument.getElementsByTagName('dauftrpozic');
		var dauftrpozicValue=0;
		
		var drueckpozicArray = domDocument.getElementsByTagName('drueckpozic');
		var drueckpozicValue=0;

		if(auftragsnrArray.length>0)
		{
			auftragsnrValue = auftragsnrArray.item(0).firstChild.nodeValue
		}
		*/
		
		if(levelArray.length>0)
		{
			levelValue = levelArray.item(0).firstChild.nodeValue
		}
		if(id_dauftrArray.length>0)
		{
			id_dauftrValue = id_dauftrArray.item(0).firstChild.nodeValue
		}
		/*
		if(dauftrpozicArray.length>0)
		{
			dauftrpozicValue = dauftrpozicArray.item(0).firstChild.nodeValue
		}
		if(drueckpozicArray.length>0)
		{
			drueckpozicValue = drueckpozicArray.item(0).firstChild.nodeValue
		}
		*/
		
		//alert("auftragsns="+auftragsnrValue+"\nlevel="+levelValue+"\nid_dauftr="+id_dauftrValue+"\ndauftrpozic="+dauftrpozicValue+"\ndrueckpozic="+drueckpozicValue);
		location.href='./preisupdateformular.php?id_dauftr='+id_dauftrValue+'&level='+levelValue;		
		//odpoved = confirm("Tato faktura uz byla exportovana, exportovat znovu ?");
		//if(odpoved)
		//	YAHOO.util.Connect.asyncRequest('GET','./exportdrech.php?auftragsnr='+auftragsnrValue+'&run=2', exportdrech);
	}
}
//----------------------------------------------------------------------

//----------------------------------------------------------------------

var gopreisupdate = {
	success:	function(o){
		var domDocument = o.responseXML;
		
		var vsechnypaletyArray = domDocument.getElementsByTagName('vsechnypalety');
		var vsechnypaletyValue=0;
		
		var aplsaveArray = domDocument.getElementsByTagName('aplsave');
		var aplsaveValue=0;
		
		var id_dauftrArray = domDocument.getElementsByTagName('id_dauftr');
		var id_dauftrValue=0;
		
		var preisArray = domDocument.getElementsByTagName('preis');
		var preisValue=0;
		
		var vzkdArray = domDocument.getElementsByTagName('vzkd');
		var vzkdValue=0;
		
		var vzabyArray = domDocument.getElementsByTagName('vzaby');
		var vzabyValue=0;
		
		var updateddauftrArray = domDocument.getElementsByTagName('updateddauftr');
		var updateddauftrValue=0;

		var updateddrueckArray = domDocument.getElementsByTagName('updateddrueck');
		var updateddrueckValue=0;

		var updateddposArray = domDocument.getElementsByTagName('updateddpos');
		var updateddposValue=0;

		var auftragsnrArray = domDocument.getElementsByTagName('auftragsnr');
		var auftragsnrValue=0;
		
		
		if(updateddposArray.length>0)
		{
			updateddposValue = updateddposArray.item(0).firstChild.nodeValue
		}
		
		
		if(aplsaveArray.length>0)
		{
			aplsaveValue = aplsaveArray.item(0).firstChild.nodeValue
		}
		
		if(vsechnypaletyArray.length>0)
		{
			vsechnypaletyValue = vsechnypaletyArray.item(0).firstChild.nodeValue
		}
		
		if(id_dauftrArray.length>0)
		{
			id_dauftrValue = id_dauftrArray.item(0).firstChild.nodeValue
		}

		if(preisArray.length>0)
		{
			preisValue = preisArray.item(0).firstChild.nodeValue
		}

		if(vzkdArray.length>0)
		{
			vzkdValue = vzkdArray.item(0).firstChild.nodeValue
		}

		if(vzabyArray.length>0)
		{
			vzabyValue = vzabyArray.item(0).firstChild.nodeValue
		}

		if(updateddauftrArray.length>0)
		{
			updateddauftrValue = updateddauftrArray.item(0).firstChild.nodeValue
		}

		if(updateddrueckArray.length>0)
		{
			updateddrueckValue = updateddrueckArray.item(0).firstChild.nodeValue
		}

		if(auftragsnrArray.length>0)
		{
			auftragsnrValue = auftragsnrArray.item(0).firstChild.nodeValue
		}
		
		alert("\nid_dauftr="+id_dauftrValue+"\nvsechnypalety="+vsechnypaletyValue+
		"\naplsave="+aplsaveValue+
		"\npreis="+preisValue+
		"\nvzkd="+vzkdValue+
		"\nvzaby="+vzabyValue+
		"\nupdateddpos="+updateddposValue+
		"\nupdateddauftr="+updateddauftrValue+
		"\nupdateddrueck="+updateddrueckValue
		);
		
		location.href='./dauftr.php?auftragsnr='+auftragsnrValue;		
		//odpoved = confirm("Tato faktura uz byla exportovana, exportovat znovu ?");
		//if(odpoved)
		//	YAHOO.util.Connect.asyncRequest('GET','./exportdrech.php?auftragsnr='+auftragsnrValue+'&run=2', exportdrech);
	}
}
//----------------------------------------------------------------------

function vsechnypalety_onclick()
{
	var vsechnypalety = document.getElementById("vsechnypalety");
	var checked = vsechnypalety.checked;
	if(checked){
		// budu upravovat vsechny nevyexportovane palety pro dany dil
		var pozicdauftrall = document.getElementById("pozicdauftrall");
		var pozicdrueckall = document.getElementById("pozicdrueckall");
		var pocetdauftr = document.getElementById("pocetdauftr");
		var pocetdrueck = document.getElementById("pocetdrueck");
		pocetdauftr.value=pozicdauftrall.value;
		pocetdrueck.value=pozicdrueckall.value;
	}
	else
	{
		var pozicdauftr = document.getElementById("pozicdauftr");
		var pozicdrueck = document.getElementById("pozicdrueck");
		var pocetdauftr = document.getElementById("pocetdauftr");
		var pocetdrueck = document.getElementById("pocetdrueck");
		pocetdauftr.value=pozicdauftr.value;
		pocetdrueck.value=pozicdrueck.value;
	}
	//alert("checked="+checked);
}