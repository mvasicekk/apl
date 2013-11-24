// JavaScript Document

//EventUtil.addEventHandler(document, "keypress", checkCR);
//EventUtil.addEventHandler(window,"load",init);
//EventUtil.addEventHandler(window,"resize",rebuildpage);


function rebuildpage()
{
	// zjistim formatovaci udaje pro formular
	var clientHeight = document.getElementById('dkopf_form_footer').parentNode.clientHeight;
	var footerHeight = document.getElementById('dkopf_form_footer').clientHeight;
	var clientWidth = document.getElementById('dkopf_form_footer').parentNode.clientWidth;
	var souradniceNode = document.getElementById('souradnice');
	var aplNode = document.getElementById('apl_table');
	var offsetTopAplNode = aplNode.offsetTop;
	var scrollAplNode = document.getElementById('scroll_apl');
	// pokud mam tabulku s obrazkama zkratim vysku apl u vysku tabulky s obrazkama + mezeru
	var attachmentNode = document.getElementById('attach_table');
	
	//aplNode.style.height=clientHeight-offsetTopAplNode-footerHeight-20;
	vyskaApl=clientHeight-offsetTopAplNode-footerHeight-20;
	
	if(attachmentNode)
	{
	 	vyskaApl=vyskaApl-document.getElementById('attach_table').clientHeight-10;
	}
	
	scrollAplNode.style.height=vyskaApl;
	
	// debugovaci informace do rohu okna
		
	textSouradnice='clientHeight='+clientHeight+'<br>clientWidth='+clientWidth;
	textSouradnice+='<br>aplNode='+aplNode+'<br>offsetTopAplNode='+offsetTopAplNode;
	textSouradnice+='<br>vyskaApl='+vyskaApl;
	
	if(attachmentNode)
	{
		vyskaAttachTable=document.getElementById('attach_table').clientHeight
		textSouradnice+='<br>attachmentNode='+attachmentNode+'<br>vyskaAttachTable='+vyskaAttachTable;
	}
	
	
	souradniceNode.innerHTML=textSouradnice;
	
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
	var kw = document.getElementById('kunde');

	var div ="";

	kdArray = xml.getElementsByTagName('kd');	
	
	// vytvorim tabulku s vysledkama
	var div="<table>";
	// pokud mam nejake vysledky, zobrazim hlavicku tabulky
	if(kdArray.length>0)
	{
		div+="<tr class='result_table_header'>";
		kdNodes = kdArray.item(0).childNodes;
		for(j=0;j<kdNodes.length;j++)
		{
			div+="<td>"+kdNodes.item(j).nodeName+"</td>";
		}
	}
	for(i=0;i<kdArray.length;i++)
	{
		kdNodes = kdArray.item(i).childNodes;
		kundeNummerArray=kdArray.item(i).getElementsByTagName("kunde");
		
		kundeNummer=kundeNummerArray.item(0).firstChild.data;
		//alert(kundeNummer);
		
		div+="<tr id='tr"+kundeNummer+"' onclick='handleOnClickSuggest(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>";
		for(j=0;j<kdNodes.length;j++)
		{
			if(kdNodes.item(j).childNodes.length>0)
				div+="<td>"+kdNodes.item(j).firstChild.data+"</td>";
			else
				div+="<td></td>";
		}
		div+="</tr>";
	}
	div+="</table>";
	foot.innerHTML=div;
	if(kdArray.length>0)
	{
		scroll.style.left=getRealPos(kw,"x")+'px';//kw.style.left;
		scroll.style.top=getRealPos(kw,"y")+'px';//kw.style.top;
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
	
	// pokud nemam zadne vysledky a uzivatel zadal 3znaky, povolim tlacitko pro vytvoreni noveho zakaznika
	var neu_button=document.getElementById('kundeneu');
	
	//alert(kw.value.length);
		
	if((kw.value.length==3)&&(kdArray.length==0))
	{
		neu_button.className='showbutton';
	}
	else
	{
		neu_button.className='hidden';
	}
}

function handleOnClickSuggest(oTr)
{
	
	updateKeywordValue(oTr);
	var oKeyword = document.getElementById("kunde");
	
	document.location.href='dksd.php?kunde='+oKeyword.value;
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
	oTr.className="";
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
	
	var oEvent = EventUtil.getEvent();
	
    var pole = new Array("auftragsnr","teil","pal","beh","stk","nachlager","weiter");
    var element =  oEvent.target.id;
    if (oEvent.keyCode == 13) {
		var i = zjistiId(element, pole) +1;
		document.getElementById(pole[i]).focus();
		oEvent.preventDefault();
	  }
    else{
      return true;
    } 
	
}
  
function beforeSubmit(){
//alert("Právì odesíláte data!")
}

function init()
{
	// retrieve the input control for the keyword
	//var oKeyword = document.getElementById("keyword");
	// prevent browser from starting the autofill function
	//oKeyword.setAttribute("autocomplete", "off");
	//rebuildpage();
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
	var oKeyword = document.getElementById("kunde");
	position = 0;
	deselectAll();
	
	start=oKeyword.value.length;
	//document.getElementById("tr0").className="highlightrow";	
	
	// nebudu upravovat obsah textboxu s vyhledavanym textem
	//updateKeywordValue(document.getElementById("tr0"));
	//selectRange(oKeyword,start,oKeyword.value.length);
}

function updateKeywordValue(oTr)
{
	var oKeyword=document.getElementById("kunde");
	
	kunde=oTr.id.substring(2,oTr.id.length);
	//alert(kunde);
	slovo=kunde;//document.getElementById(kunde).childNodes[0].data;
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


function init_dkopf_form(rezim)
{
	if(rezim=='show')
	{
		// pouze rezim pro prohlizeni, pripadne editaci nekterych parametru
		var teil = document.getElementById("teil");
		var kunde = document.getElementById("kunde");
		// teil bude readonly
		teil.disabled=true;
		// nastavim fokus na kunde
		kunde.focus();
		
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

function validatename1(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	// kontrola nenulove delky
	if(control.value.length==0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='name1 niecht leer lassen !';
	}
	else
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
}

/*
function validateico(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	// kontrola nenulove delky
	if(control.value.length==0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='name1 niecht leer lassen !';
	}
	else
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
}
*/


function validatestrasse(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	// kontrola nenulove delky
	if(control.value.length==0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='Strasse nicht leer lassen !';
	}
	else
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
}

function validateplz(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	// kontrola nenulove delky
	if(control.value.length==0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='PLZ nicht leer lassen !';
	}
	else
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
}

function validaterechanschr(xml)
{
	var errorArray = xml.getElementsByTagName('error');
	var id = xml.getElementsByTagName('id').item(0).firstChild.data;

	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	var errorText = '';
	
	// alert('validaterechanschr');
	// pokud mam nejake policka error
	if(errorArray.length>0)
	{
		// pokud mam nejaky obsah v policku error
		if(errorArray.item(0).hasChildNodes())
		{
			errorText = errorArray.item(0).firstChild.data;
		}
	}
	
	if(errorText.length>0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value=errorText;
	}
	else
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
}

function validatepreismin(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	js_validate_float(control);
	
	// pokud projde validace na float tak udelam prepocet na preisvzh = cena za hodinu
	// hodnota by mela byt vetsi nez nula
	if(control.value<0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='Minutenpreis < 0 ???';
		control.focus();
		control.select();
	}
	else
	{
		preisvzh = control.value * 60;
		preisvzh.toFixed(4);
		control.style.backgroundColor='';
		failed.className='hidden';
		preisvzhControl = document.getElementById('preisvzh');
		preisvzhControl.value = preisvzh;
	}

}



function validatepreisvzh(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);
	
	js_validate_float(control);
	
	// pokud projde validace na float tak udelam prepocet na preismin = cena za hodinu
	// hodnota by mela byt vetsi nez nula
	if(control.value<0)
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='Stundenpreis < 0 ???';
		control.focus();
		control.select();
	}
	else
	{
		preisvzh = control.value / 60
		control.style.backgroundColor='';
		failed.className='hidden';
		preisvzhControl = document.getElementById('preismin');
		preisvzhControl.value = preisvzh;
	}

}

function validatezahnlungziel(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);

	intvalue = parseInt(control.value)
	if(!isNaN(intvalue)&&(intvalue>=0))
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
	else
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='Zahlungsziel Fehler !!!';
	}	
}

function validatepreis_runden(id)
{
	var control = document.getElementById(id);
	var idfailed = id+"_failed";
	var failed = document.getElementById(idfailed);

	intvalue = parseInt(control.value)
	if(!isNaN(intvalue)&&(intvalue>=0))
	{
		control.style.backgroundColor='';
		failed.className='hidden';
	}
	else
	{
		control.style.backgroundColor='red';
		failed.className='error';
		failed.value='Rundstellen Fehler !!!';
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



function update_dpos(xml)
{
	var sql = xml.getElementsByTagName('sql').item(0).firstChild.data;
	var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	
	
	if(mysqlerrorarray.item(0).hasChildNodes())
	{
		mysqlerror = mysqlerrorarray.item(0).firstChild.data;
		alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
	}
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
	//nove = window.open('');
	//nove.document.writeln('sql='+sql);
	//nove.document.writeln('affected rows='+affectedrows);
	
	window.location.reload();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}



function encodeControlValue(i)
{
	var control = document.getElementById(i);
	//alert('control='+i+' value='+encodeURIComponent(control.value));
	return encodeURIComponent(control.value);
}




function encodeSelectControlValue(i)
{
	var control = document.getElementById(i);
	var optionarray = control.getElementsByTagName('option');
	var	selectedvalue=optionarray.item(control.selectedIndex).getAttribute('value');
	return encodeURIComponent(selectedvalue);
}


function new_kunde(xml)
{
	var kunde = xml.getElementsByTagName('kunde').item(0).firstChild.nodeValue;
	var mysqlerror = xml.getElementsByTagName('mysqlerror').item(0);

	if(mysqlerror.hasChildNodes())
	{
		alert(mysqlerror.firstChild.nodeValue);
	}
	else
	{
		//alert("zakazka vytvorena :"+"\nauftragsnr="+auftragsnr+"\nkunde="+kunde);
		location.href='./dksd.php?kunde='+kunde;
	}
}


var validatekndnummer = {
	success:	function(o){
		var domDocument = o.responseXML;
		
		var idArray = domDocument.getElementsByTagName('id');
		
				
		var id = idArray.item(0).firstChild.nodeValue
		
		var inputbox = document.getElementById(id);
		
		id = id + 'name';
		
		//alert('id='+id);
		var kndnameArray = domDocument.getElementsByTagName('kndname');
		var kndnameValue=0;
		
		if(kndnameArray.length>0)
		{
			kndnameValue = kndnameArray.item(0).firstChild.nodeValue
			if(kndnameValue=="ERROR"){
				// chyba v cisle zakaznika
				document.getElementById(id).innerHTML = 'KND existiert nicht !';
				inputbox.style.backgroundColor='red';
				inputbox.focus();
				inputbox.select();		
			}
			else{
				document.getElementById(id).innerHTML = kndnameValue;
				inputbox.style.backgroundColor='';
			}
		}
}
}
