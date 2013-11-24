
// JavaScript Document

var drueck = {};

EventUtil.addEventHandler(window,"resize",rebuildpage);
EventUtil.addEventHandler(document, "keypress", checkCR);

$(document).ready(function(){

//$('input#persnr').css({'background-color':'green'});
$('input#oeall').hide();
$('input#pg').hide();
$('input#kunde').hide();
$('input#oerabgnr').hide();
$('input#oerpersnr').hide();
$('input#oesel').hide();
$('#palinfodiv').hide();
$('#stornodiv').hide();
$('#pal').blur(palOnBlur);

});

//------------------------------------------------------------------------------
/**
 *
 */

function palOnBlur(event){
    var id = $(this).attr('id');
    var acturl = $(this).attr('acturl');
    var auftragsnr = $('#auftragsnr').val();
    var pal = $('#pal').val();
    
    if(auftragsnr!=999999){
        $.post(acturl,
        {
	    id:id,
	    auftragsnr:auftragsnr,
	    pal:pal
	},
	function(data){
    	palOnBlurUpdate(data);
        },
        'json'
        );
	    
	obsahDiv = "send Ajax  request : ";
	obsahDiv+= "auftragsnr="+auftragsnr+" ";
	obsahDiv+= "pal="+pal+"";
    }
}


/**
 *
 */

function abgnrfilterClick1(event){
    
    var id = $(this).attr('id');
    var acturl = $(this).attr('acturl');
    var auftragsnr = $('#auftragsnr').val();
    var pal = $('#pal').val();
    var posPodtrzitko = id.indexOf('_', 0);
    var abgnr=id.substr(posPodtrzitko+1);
    
    filterparam = auftragsnr+';;'+pal+';'+abgnr+';;';
    
    getDataReturnXml('./refreshwhere.php?filterparam='+filterparam,refreshwhere1);
}



/**
 *
 */

function palOnBlurUpdate(data){
    $('#palinfodiv').html(data.obsahDiv);
    var aBtOffset = $('#arbeitsmittelausgabe').offset();
    var aBtWidth = $('#arbeitsmittelausgabe').outerWidth();
    $('#palinfodiv').css({"left":aBtOffset.left+aBtWidth+5+"px","top":aBtOffset.top-100+"px"});
    $('td[id^=abgnrfilter_]').click(abgnrfilterClick1);
    $('#palinfodiv').show();
}
//------------------------------------------------------------------------------

function init_level(level,controls_levels)
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
}

function rebuildpage()
{
	// zjistim formatovaci udaje pro formular
//	var clientHeight = document.getElementById('storno_form_footer').parentNode.clientHeight;
//	var footerHeight = document.getElementById('storno_form_footer').clientHeight;
//	var clientWidth = document.getElementById('storno_form_footer').parentNode.clientWidth;
//	//var souradniceNode = document.getElementById('souradnice');
//	var aplNode = document.getElementById('drueck_table');
//	var offsetTopAplNode = aplNode.offsetTop;
//	var scrollAplNode = document.getElementById('scroll_apl');
//
//	//aplNode.style.height=clientHeight-offsetTopAplNode-footerHeight-20;
//	vyskaApl=clientHeight-offsetTopAplNode-footerHeight-20;
//
//	scrollAplNode.style.height=vyskaApl;
//
//	textSouradnice='clientHeight='+clientHeight+'<br>clientWidth='+clientWidth;
//	textSouradnice+='<br>aplNode='+aplNode+'<br>offsetTopAplNode='+offsetTopAplNode;
//	textSouradnice+='<br>vyskaApl='+vyskaApl;
	
	//souradniceNode.innerHTML=textSouradnice;
}

drueck.pole = new Array(	"auftragsnr",
							"pal",
							"datum",
							"mehr",
							"tat1",
							"tat2",
							"tat3",
							"tat4",
							"tat5",
							"tat6",
							"persnr",
							"schicht",
							"stk",
							"auss_stk",
							"auss_art",
							"auss_typ",
							"von",
							"bis",
							"pause",
                                                        "oeselect",
							"neu");


function insertAmBewControlsToPole(){
    // hack, pokud uz tam policko amnr najdu pridani neprovedu

    if(drueck.pole.indexOf('amnr', 0)==-1)
        nove_pole = drueck.pole.splice(10, 0, "amnr", "ausstk","rueckstk");
//    alert(pole.join(','));
//    pole = nove_pole;
}

function removeAmBewControlsFromPole(){
    nove_pole = drueck.pole.splice(9, 3);
//    pole = nove_pole;
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
		var neuButton = document.getElementById('neu').disabled=true;
	}
	else
	{
		control.style.backgroundColor='';
		control.value=hodnota;
		var neuButton = document.getElementById('neu').disabled=false;
	}
}


function spocti_vykon()
{
	var stk_value=document.getElementById('stk').value*1;
	var auss_stk_value=document.getElementById('auss_stk').value*1;
	var vzaby_pro_stk_value=document.getElementById('vzaby_pro_stk').value*1;
	var e_sumvzaby=document.getElementById('sumvzaby');
	var verb_value=document.getElementById('verb').value*1;
	var e_sumverb=document.getElementById('sumverb');
	var e_leist_procent=document.getElementById('leist_procent');
	var e_auss_typ=document.getElementById('auss_typ');
	var e_auss_stk=document.getElementById('auss_stk');

	// vypoctu sumu casu abydos pro dany pocet kusu a cas
	
	if(e_auss_typ.value==4)
		summinuten = (stk_value+e_auss_stk.value*1) * vzaby_pro_stk_value;
	else
		summinuten = stk_value * vzaby_pro_stk_value;
		
	e_sumvzaby.value=Math.round(summinuten);
	
	if(e_sumvzaby.value!=0)
		e_sumvzaby.style.backgroundColor='yellow';
	else
		e_sumvzaby.style.backgroundColor='';

	if(verb_value!=0)
	{
		leistung_procent=100*summinuten/verb_value;
		
		e_leist_procent.value=Math.round(leistung_procent);
		
		e_sumverb.value=Math.round(verb_value);
		
		e_sumverb.style.backgroundColor='yellow';
		e_leist_procent.style.backgroundColor='#ff0000';
		e_leist_procent.style.color='#000000';
		e_leist_procent.style.fontSize='20px';
		e_leist_procent.style.fontWeight='bold';
		
	}
	else
	{
		leistung_procent=0;
		e_sumverb.style.backgroundColor='';
		e_leist_procent.style.backgroundColor='0';
	}
}


function spocti_verb()
{
	var von = document.getElementById('von');
	var bis = document.getElementById("bis");
	var pause = document.getElementById("pause");
	
	var vonh= von.value.substr(0,2);
	var vonm= von.value.substr(3,2);
	var bish= bis.value.substr(0,2);
	var bism= bis.value.substr(3,2);
	var vonsc = new Date(0,0,0,vonh,vonm,0);
	var bissc = new Date(0,0,0,bish,bism,0);
	// vypocitam cas v sekundach
	var time = (bissc.getTime() - vonsc.getTime())/60/1000;
	return time-pause.value*1;
}

function refreshtime(jaky)
{
	var von = document.getElementById('von');
	var bis = document.getElementById("bis");
	
	var hodiny,minuty;
	
	if(jaky=='von')
		e=von;
	else
		e=bis;
		
	if(e.value.length>=4)
	{
		if(e.value.length==4)
		{
			hodiny=e.value.substr(0,2);
			minuty=e.value.substr(2,2);
			e.value=hodiny+":"+minuty;
		}
	
		var cas = spocti_verb();
	
		if(cas<0)
		{
			bis.style.backgroundColor='red';
			von.style.backgroundColor='red';
			var neuButton = document.getElementById('neu').disabled=true;
		}
		else
		{
			bis.style.backgroundColor='';
			von.style.backgroundColor='';
			var neuButton = document.getElementById('neu').disabled=false;
		}
		document.getElementById('verb').value = cas;
		spocti_vykon();
	}
	else
	{
		e.style.backgroundColor='red';
		e.value='ERROR';
		var neuButton = document.getElementById('neu').disabled=true;
		e.select();
		e.focus();
	}
}

function refreshpause()
{
	cas=spocti_verb();
	document.getElementById('verb').value=cas;
	
	if(cas<0)
	{
		document.getElementById('pause').style.backgroundColor='red';
	}
	else
	{
		document.getElementById('pause').style.backgroundColor='';
	}
	
	spocti_vykon();
}



function zjistiId(element){
for(i=0;i<drueck.pole.length; i++){
if(drueck.pole[i]== element){return i;}
}
}


function checkCR() {
	
	var oEvent = EventUtil.getEvent();
	
	//alert("checkCR:oEvent.keyCode="+oEvent.keyCode);			
    var element =  oEvent.target.id;

    if (oEvent.keyCode == 13)
    {
		var i = zjistiId(element) +1;
		if(i<drueck.pole.length)
			document.getElementById(drueck.pole[i]).focus();
		//oEvent.preventDefault();
	}
    else
    {
    	if (oEvent.keyCode == 27) 
    	{
			//alert('ESC stisknuto');
			window.location.reload();
			//oEvent.preventDefault();
	  	}
    	else
    	{
      			return true;
    	}
    }
}
  




function init_drueck_form(rezim)
{
	var auftragsnr = document.getElementById('auftragsnr');
	auftragsnr.focus();
	auftragsnr.select();
	//auftragsnr.value='';
	
	var datum = document.getElementById('datum');
	datum_value = new Date();
	
	mesic=datum_value.getMonth()+1;
	datum_value = datum_value.getDate()+'.'+mesic+'.'+datum_value.getFullYear();
	datum.value = datum_value;

	var mehr = document.getElementById('mehr');
	mehr.value='0';
	
	var pal = document.getElementById('pal');
	//pal.value='0';
	
	// policka s operacema
	for(i=1;i<7;i++)
	{
		policko=document.getElementById('tat'+i);
		policko_abymin=document.getElementById('tat'+i+'_abymin');
		policko_kdmin=document.getElementById('tat'+i+'_kdmin');
		policko.value='0';
		policko_abymin.value='0';
		policko_kdmin.value='0';
	}
	
	
	element = document.getElementById('teilbez');
	element.value='';
	
	element = document.getElementById('tatbez');
	element.value='';
	
	element = document.getElementById('sumvzaby');
	element.value='0';
	

	element = document.getElementById('sumverb');
	element.value='0';
	
	element = document.getElementById('leist_procent');
	element.value='0';

	element = document.getElementById('persname');
	element.value='';
	
	element = document.getElementById('stk');
	element.value='0';
	
	element = document.getElementById('auss_stk');
	element.value='0';
	
	element = document.getElementById('auss_art');
	element.value='0';
	
	element = document.getElementById('auss_typ');
	element.value='0';

	element = document.getElementById('ausstk');
	element.value='0';

	element = document.getElementById('rueckstk');
	element.value='0';

	element = document.getElementById('vzaby_pro_stk');
	element.value='0';
	
	element = document.getElementById('von');
	element.value='00:00';
	element = document.getElementById('bis');
	element.value='00:00';
	element = document.getElementById('pause');
	element.value='0';
	element = document.getElementById('verb');
	element.value='0';






	
	if(rezim=='show')
	{
		// pouze rezim pro prohlizeni, pripadne editaci nekterych parametru
		//var teil = document.getElementById("auftragsnr");
		//var kunde = document.getElementById("kunde");
		// teil bude readonly
		//teil.disabled=true;
		// nastavim fokus na kunde
		//kunde.focus();
		
	}

//        insertAmBewControlsToPole();
}

function init_drueck_form_edit(rezim)
{
	var auftragsnr = document.getElementById('auftragsnr');
	auftragsnr.focus();
	auftragsnr.select();
	//auftragsnr.value='';
	
	var datum = document.getElementById('datum');
	datum_value = new Date();
	
	mesic=datum_value.getMonth()+1;
	datum_value = datum_value.getDate()+'.'+mesic+'.'+datum_value.getFullYear();
	//datum.value = datum_value;

	var mehr = document.getElementById('mehr');
	//mehr.value='0';
	
	var pal = document.getElementById('pal');
	//pal.value='0';
	
	// policka s operacema
	/*
	for(i=1;i<7;i++)
	{
		policko=document.getElementById('tat'+i);
		policko_abymin=document.getElementById('tat'+i+'_abymin');
		policko_kdmin=document.getElementById('tat'+i+'_kdmin');
		policko.value='0';
		policko_abymin.value='0';
		policko_kdmin.value='0';
	}
	*/
	
	
	element = document.getElementById('teilbez');
	element.value='';
	
	element = document.getElementById('tatbez');
	element.value='';
	
	element = document.getElementById('sumvzaby');
	//element.value='0';
	

	element = document.getElementById('sumverb');
	//element.value='0';
	
	element = document.getElementById('leist_procent');
	//element.value='0';

	element = document.getElementById('persname');
	element.value='';
	
	element = document.getElementById('stk');
	//element.value='0';
	
	element = document.getElementById('auss_stk');
	//element.value='0';
	
	element = document.getElementById('auss_art');
	//element.value='0';
	
	element = document.getElementById('auss_typ');
	//element.value='0';

	element = document.getElementById('vzaby_pro_stk');
	//element.value='0';
	
	element = document.getElementById('von');
	//element.value='00:00';
	element = document.getElementById('bis');
	//element.value='00:00';
	element = document.getElementById('pause');
	//element.value='0';
	element = document.getElementById('verb');
	//element.value='0';
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




function saverefresh(xml)
{
	//alert('saverefresh');
	var sqlarray = xml.getElementsByTagName('sql');
	//var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	//var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	var serieinsertu_array = xml.getElementsByTagName('lager_serieinsertu');
	var auftragsnr_oldArray = xml.getElementsByTagName('auftragsnr_old');
	
	
	
	if(serieinsertu_array.item(0).hasChildNodes())
	{
		var sqlinsert_array = xml.getElementsByTagName('lager_sqlinsert');
		var affected_rows_array = xml.getElementsByTagName('affected_rows');
		var mysqlerror_array = xml.getElementsByTagName('mysqlerror');

		for(i=0;i<sqlinsert_array.length;i++)
		{
			//alert(sqlinsert_array.item(i).firstChild.data);
			if(mysqlerror_array.item(0).hasChildNodes())
				alert(mysqlerror_array.item(i).firstChild.data);
		}
	}
	
	
	//nove = window.open('');
	
	/*
	for(i=0;i<sqlarray.length;i++)
	{
		sql=sqlarray.item(i).firstChild.data;
		//alert(sql);
		//nove.document.write('sql='+sql);
		//nove.document.write('<br>');
	}
	*/
	
	window.location.reload();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}

function saverefreshedit(xml)
{
	//alert('saverefreshedit');
	var sqlarray = xml.getElementsByTagName('sql');
	//var affectedrows = xml.getElementsByTagName('affectedrows').item(0).firstChild.data;
	//var mysqlerrorarray = xml.getElementsByTagName('mysqlerror');
	var serieinsertu_array = xml.getElementsByTagName('lager_serieinsertu');
	//var auftragsnr_oldArray = xml.getElementsByTagName('auftragsnr_old');
	
	
	
	if(serieinsertu_array.item(0).hasChildNodes())
	{
		var sqlinsert_array = xml.getElementsByTagName('sqlinsert');
		var affected_rows_array = xml.getElementsByTagName('affected_rows');
		var mysqlerror_array = xml.getElementsByTagName('mysqlerror');

		for(i=0;i<sqlinsert_array.length;i++)
		{
			//alert(sqlinsert_array.item(i).firstChild.data);
			if(mysqlerror_array.item(0).hasChildNodes())
				alert(mysqlerror_array.item(i).firstChild.data);
		}
	}

	var stornoid = xml.getElementsByTagName('stornoid').item(0).firstChild.data;	
	//alert('stornoid='+stornoid);
	
	window.history.back();
	
	//alert('sql='+sql+' affected rows='+affectedrows+' mysqlerror='+mysqlerror);
		
}


function encodeControlValue(i)
{
	var control = document.getElementById(i);
	return encodeURI(control.value);
}


function encodeSelectControlValue(i)
{
	var control = document.getElementById(i);
	var optionarray = control.getElementsByTagName('option');
	var	selectedvalue=optionarray.item(control.selectedIndex).getAttribute('value');
	return encodeURI(selectedvalue);
}



function validate_auftragsnr(xml)
{
	var field=xml.getElementsByTagName('auftragsnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	//alert(field.substring(0,5));
	if(field.substring(0,5)=="ERROR")
	{
		$('#palinfodiv').hide();
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		$('#palinfodiv').hide();
		e.style.backgroundColor='';
		var neuButton = document.getElementById('neu').disabled=false;
                var kunde = xml.getElementsByTagName('kunde').item(0).firstChild.data;
                document.getElementById('kunde').value = kunde;
                if(kunde==355){
                    document.getElementById('ambeweingabe').style.display = 'block';
                    document.getElementById('ambeweingabe').style.visibility = 'visible';
                    insertAmBewControlsToPole();
                }
                else{
                    document.getElementById('ambeweingabe').style.display = 'none';
                    if(drueck.pole.length>21)
                        removeAmBewControlsFromPole();
                }
	}
}

function validate_amnr(xml)
{
	var errorArray = xml.getElementsByTagName('error')
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var amnrpopis=document.getElementById('amnrpopis');

	if(errorArray.length>0)
	{
		e.style.backgroundColor='red';
		amnrpopis.value='polozka nenalezena !!';
		document.getElementById('neu').disabled=true;
	}
	else
	{
		var popis='';
		var name1Node = xml.getElementsByTagName('name1').item(0);
		if(name1Node.hasChildNodes())
			popis+=name1Node.firstChild.data;
		var name2Node = xml.getElementsByTagName('name2').item(0);
		if(name2Node.hasChildNodes())
			popis+=' '+name2Node.firstChild.data;
		amnrpopis.value=popis;
		document.getElementById('neu').disabled=false;
	}
}

function pocetPrvkuVPoli(pole,prvek)
{
	var pocetVyskytu = 0;
	for(i=0;i<pole.length;i++)
		if(pole[i]==prvek) pocetVyskytu++;
	return pocetVyskytu;
}

function odeberPrvekZPole(pole,prvek)
{
	// najdu si index odebiraneho prvku
	indexHledaneho = -1;
	for(i=0;i<pole.length;i++)
	{
	 if(pole[i]==prvek) indexHledaneho=i;
	}
	// odeberu prvek z pole
	if(indexHledaneho>=0)
		pole.splice(indexHledaneho,1);
}


function pridejPrvekDoPole(pole,prvek,zaprvek)
{
	var novePole = new Array(pole.length);
	for(i=0;i<pole.length;i++)
	{
		novePole.push(pole[i]);
		if(pole[i]==zaprvek)
		novePole.push(prvek);
	}
	// vycistim puvodni pole
	while(pole.length)
		pole.pop();
	//alert('delka stareho pole='+pole.length);
	//obsah noveho pole nakopirovat zpet do puvodniho pole
	//alert('seznamPoli='+seznamPoli);
	for(i=0;i<novePole.length;i++)
	{
		pole.push(novePole[i]);
	}
}

function validate_persnr(xml)
{
//	alert('validate_persnr');
	var field=xml.getElementsByTagName('persnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_persname=document.getElementById('persname');
	var e_schicht=document.getElementById('schicht');
	var reducedoes_array=xml.getElementsByTagName('reducedoe');
        var regeloe_array=xml.getElementsByTagName('regeloe');
        var alloes_array=xml.getElementsByTagName('alloe');


	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
                if(reducedoes_array.item(0).hasChildNodes())
                    reducedoes = reducedoes_array.item(0).firstChild.data;
                else
                    reducedoes = ' ';

                if(regeloe_array.item(0).hasChildNodes())
                    regeloe = regeloe_array.item(0).firstChild.data;
                else
                    regeloe = ' ';

		e.style.backgroundColor='';
		e_persname.value=xml.getElementsByTagName('name').item(0).firstChild.data;
		e_schicht.value=xml.getElementsByTagName('schicht').item(0).firstChild.data;
                document.getElementById('oerpersnr').value = reducedoes;
                document.getElementById('regeloe').value = regeloe;

                oeselArray = reducedoes.split(';');
                oesel = oeselArray[0];
                document.getElementById('oesel').value = oesel;
                $('#oeselect').val(oesel);

                
		var neuButton = document.getElementById('neu').disabled=false;
		
		//e_schicht.disabled=false;
		//e_schicht.focus();
		// pokud jsem aktualne na tomto policku tak ho zaselektim , jinak necham fokus tam, kde je
		var eAktual = document.getElementById('elementaktual');
		if(eAktual.value=='schicht')
		{
			e_schicht.focus();
			e_schicht.select();
		}
	}

        if(alloes_array.item(0).hasChildNodes())
            alloes = alloes_array.item(0).firstChild.data;
        else
            alloes = ' ';
        document.getElementById('oeall').value = alloes;
        
}

function validate_persnredit(xml)
{
	//alert('validate_persnr');
	var field=xml.getElementsByTagName('persnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_persname=document.getElementById('persname');
	var e_schicht=document.getElementById('schicht');
	
	/*
	for(j=0;j<pole.length;j++)
	{
		if(document.getElementById(pole[j]).hasFocus) aktivniPrvek=j;
	}
	alert('element s fokusem'+pole[aktivniPrvek]);
	*/
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		e_persname.value=xml.getElementsByTagName('name').item(0).firstChild.data;
		//e_schicht.value=xml.getElementsByTagName('schicht').item(0).firstChild.data;
		var neuButton = document.getElementById('neu').disabled=false;
		
		//e_schicht.disabled=false;
		//e_schicht.focus();
		e_schicht.select();
	}
}

function js_auss_stk_validate()
{
	if((document.getElementById('stk').value==0)&&(document.getElementById('auss_stk').value==0))
	{
		document.getElementById('stk').style.backgroundColor='red';
		document.getElementById('auss_stk').style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
	}
	else
	{
		document.getElementById('stk').style.backgroundColor='';
		document.getElementById('auss_stk').style.backgroundColor='';
		var neuButton = document.getElementById('neu').disabled=false;
	}

	spocti_vykon();
}


function js_stk_validate()
{
	var stk_value=document.getElementById('stk').value*1;
	var auss_stk_value=document.getElementById('auss_stk').value*1;
	var vzaby_pro_stk_value=document.getElementById('vzaby_pro_stk').value*1;
	var e_sumvzaby=document.getElementById('sumvzaby');
	
	spocti_vykon();
	
}



function validate_auss_art(xml)
{
	
	var field=xml.getElementsByTagName('auss_art').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	if(xml.getElementsByTagName('auss_typ').item(0).hasChildNodes())
		var auss_typ_value=xml.getElementsByTagName('auss_typ').item(0).firstChild.data;
	else
		var auss_typ_value=0;
		
	var e_auss_typ=document.getElementById('auss_typ');
	
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		var neuButton = document.getElementById('neu').disabled=false;
		e_auss_typ.value=auss_typ_value;
	}
	
	spocti_vykon();
}

function validate_schicht(xml)
{
	
	var field=xml.getElementsByTagName('schichtnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		var neuButton = document.getElementById('neu').disabled=false;
	}
}

function validate_auss_typ(xml)
{
	//alert('validate_auss_typ');
	
	var field=xml.getElementsByTagName('auss_typ').item(0).firstChild.data;
	//var sql=xml.getElementsByTagName('sql').item(0).firstChild.data;
	//alert(field);
	//alert(sql);
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		var neuButton = document.getElementById('neu').disabled=false;
	}
	spocti_vykon();
}


function validate_abgnr1(xml)
{
	//alert('validate abgnr');
	var field=xml.getElementsByTagName('abgnr').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_tat_abymin=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data+'_abymin');
	var e_tat_kdmin=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data+'_kdmin');
	var e_sumvzaby=document.getElementById('vzaby_pro_stk');
	var e_tatbez=document.getElementById('tatbez');
	var mehr_value=document.getElementById('mehr').value;
	var controlid=xml.getElementsByTagName('controlid').item(0).firstChild.data;
	var bezd_array=xml.getElementsByTagName('bezd');
	var bezt_array=xml.getElementsByTagName('bezt');
        var oes_array=xml.getElementsByTagName('oes');
        var pg_array=xml.getElementsByTagName('pg');
        var reducedoes_array=xml.getElementsByTagName('reducedoe');
        var alloes_array=xml.getElementsByTagName('alloe');
	
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value=field;
		e_tat_abymin.value='0';
		e.focus();
		e.select();
	}
	else
	{
		var field_vzaby=xml.getElementsByTagName('vzaby').item(0).firstChild.data;
		var field_vzkd=xml.getElementsByTagName('vzkd').item(0).firstChild.data;

                if(oes_array.item(0).hasChildNodes())
                    oes = oes_array.item(0).firstChild.data;
                else
                    oes = ' ';

                if(reducedoes_array.item(0).hasChildNodes())
                    reducedoes = reducedoes_array.item(0).firstChild.data;
                else
                    reducedoes = ' ';

                if(alloes_array.item(0).hasChildNodes())
                    alloes = alloes_array.item(0).firstChild.data;
                else
                    alloes = ' ';

                if(pg_array.item(0).hasChildNodes())
                    pg = pg_array.item(0).firstChild.data;
                else
                    pg = ' ';

		// u viceprace povolim zadat jen jednu operaci, pokud neni nejaka chyba
		if((mehr_value>0)&&(field.substring(0,5)!="ERROR"))
		{
			document.getElementById('persnr').focus();
			var neuButton = document.getElementById('neu').disabled=false;
		}
		
		if((mehr_value>0)&&(controlid=='tat2'||controlid=='tat3'||controlid=='tat4'||controlid=='tat5'||controlid=='tat6'))
		{
			e_tat_abymin.value=0;
			e_tat_kdmin.value=0;
			//e.style.backgroundColor='red';
			e.value=0;
			var neuButton = document.getElementById('neu').disabled=false;
		}
		else
		{
			if(bezt_array.item(0).hasChildNodes())
			{
				//alert('ma detatka');
				e_tatbez.value=e.value+' '+bezt_array.item(0).firstChild.data;
			}
			else
			{
				//alert('nema detatka');
				e_tatbez.value=' ';
			}
				
			e_tat_abymin.value=field_vzaby;
			e_tat_kdmin.value=field_vzkd;
                        
                        // budu delat jen u prvni operace
                        if(controlid=='tat1'){
                            document.getElementById('oerabgnr').value = reducedoes;
                            document.getElementById('oeall').value = alloes;
                            
                            oeselArray = reducedoes.split(';');
                            oesel = oeselArray[0];
//                            alert(oeselArray);

                            // naplnim select
                            $('#oeselect').html('');
                            for(i=0;i<oeselArray.length;i++){
                                options = '<option value="'+oeselArray[i]+'">'+oeselArray[i]+'</option>';
//                                alert(options);
                                $(''+options+'').appendTo('#oeselect');
                            }
                            // vyberu hodnotu
                            $('#oeselect').val(oesel);
                            
                            document.getElementById('oesel').value = oesel;
                            document.getElementById('pg').value = pg;
                        }

			e.style.backgroundColor='';
		}
		
		soucet_vzaby=0;
		// projedu vsechny policka tatX_abymin a sectu cisla v nich obsazene
		for(i=1;i<7;i++)
		{
			var policko = document.getElementById('tat'+i+'_abymin');
			soucet_vzaby+=policko.value*1;
		}
		
		e_sumvzaby.value=soucet_vzaby;
		// pokud jde o vicepraci, umoznim zadani hodnoty do policka
		if(mehr_value>0)
		{
			// uvolnim policko pro zapis
			e_sumvzaby.disabled=false;
			// zaradit policko e_sumvzaby do pole pro prechod pomoci cr
			pridejPrvekDoPole(drueck.pole,'vzaby_pro_stk','auss_typ');
			while(pocetPrvkuVPoli(drueck.pole,'vzaby_pro_stk')>1)
				odeberPrvekZPole(drueck.pole,'vzaby_pro_stk');
			//alert(pole.join());
		}
		else
		{
			e_sumvzaby.disabled=true;
			odeberPrvekZPole(drueck.pole,'vzaby_pro_stk');
		}
		var neuButton = document.getElementById('neu').disabled=false;
	}
}

function validate_mehr(xml)
{

	var errorArray = xml.getElementsByTagName('error');
	
	var field=xml.getElementsByTagName('teil').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_teil=document.getElementById('teil');
	var abgnrarray=xml.getElementsByTagName('abgnr');
	var hasExport = xml.getElementsByTagName('hasexport').item(0).firstChild.data
	var e_sumvzaby=document.getElementById('vzaby_pro_stk'); 
	
	//nejaka chyba ?
	if(errorArray.length>0)
	{
		//alert('mam errorArray.length'+errorArray.length);
		//var description = errorArray.item(0).getElementsByTagName('errordescription').firstChild.data;
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value='';
		e.focus();
		e.select();
	}
	else
	{
		e.style.backgroundColor='';
		// vytvorim seznam moznych operaci
		operace='';
		var abgnrarray=xml.getElementsByTagName('abgnr');	
		for(i=0;i<abgnrarray.length;i++)
		{
			operace+=abgnrarray.item(i).firstChild.data+',';
		}
	
		var e_tatnrarray=document.getElementById('tatnrarray');
		e_tatnrarray.value=operace.substring(0,operace.length-1);

		if(e.value>0)
		{
			// uvolnim policko pro zapis
			e_sumvzaby.disabled=false;
			// zaradit policko e_sumvzaby do pole pro prechod pomoci cr
			pridejPrvekDoPole(drueck.pole,'vzaby_pro_stk','auss_typ');
			// zajistim si aby nahodou prvek nebyl v poli vice nez jednou
			while(pocetPrvkuVPoli(drueck.pole,'vzaby_pro_stk')>1)
				odeberPrvekZPole(drueck.pole,'vzaby_pro_stk');
			
			// zkopiruju zadanou hodnotu i do policka tat1
			document.getElementById('tat1').value=e.value;
			document.getElementById('tat1').focus();
			document.getElementById('tat1').select();
			//alert(pole.join());
		}
		else
		{
			// zakazu pristup do policka vzaby_pro_stk
			e_sumvzaby.disabled=true;
			odeberPrvekZPole(drueck.pole,'vzaby_pro_stk');
		}
	
	var neuButton = document.getElementById('neu').disabled=false;
	}
}

function validate_pal(xml)
{
	//alert(xml);
	
	var errorArray = xml.getElementsByTagName('error')
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_teil=document.getElementById('teil');
	var hasExport = xml.getElementsByTagName('hasexport').item(0).firstChild.data 
	var mehrElement = document.getElementById('mehr');
	
	//nejaka chyba ?
	if(errorArray.length>0)
	{
		//alert('mam errorArray.length'+errorArray.length);
		//var description = errorArray.item(0).getElementsByTagName('errordescription').firstChild.data;
		e.style.backgroundColor='red';
		var neuButton = document.getElementById('neu').disabled=true;
		e.value='';
		e_teil.value='';
		e.focus();
		e.select();
	}
	else
	{
		// vytvorim seznam moznych operaci
		operace='';
		var abgnrarray=xml.getElementsByTagName('abgnr');	
		for(i=0;i<abgnrarray.length;i++)
		{
			operace+=abgnrarray.item(i).firstChild.data+',';
		}
	
		var e_tatnrarray=document.getElementById('tatnrarray');
		e_tatnrarray.value=operace.substring(0,operace.length-1);
		
		var e_teilbez=document.getElementById('teilbez');
		e_teilbez.value=xml.getElementsByTagName('teilbez').item(0).firstChild.data;
		e.style.backgroundColor='';
		e_teil.value=xml.getElementsByTagName('teil').item(0).firstChild.data;;
	
		// pokud jde o paletu, kde chci mi moznost zadavat jen interni operace
		// ( paleta uz ma export )
		// vyplnim pole mehr na hodnotu 1 a posunu se na pole pro prvni operaci
		if(hasExport==1)
		{
			mehrElement.value=1;
			var tat1Element = document.getElementById('tat1');
			tat1Element.focus();
			tat1Element.select();
		}
	var neuButton = document.getElementById('neu').disabled=false;
	}
}


function validate_bis(xml)
{
	var field=xml.getElementsByTagName('bis').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_von=document.getElementById('von');
	var chybacasuE = document.getElementById('chybacasu');
	
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		// mam nejakou chybu pri zadavani casu
		// zjistim typ chyby
		
		typ_chyby=xml.getElementsByTagName('errordescription').item(0).firstChild.data;
		e.style.backgroundColor='red';
		e_von.style.backgroundColor='red';
		
		chybacasuE.style.visibility='visible';
		chybacasuE.innerHTML='<h1>'+typ_chyby+'</h1>';
		
		//alert(typ_chyby);
		
		var lastvonbis_array=xml.getElementsByTagName('lastvonbis');
		if(lastvonbis_array.length>0)
		{
			if(lastvonbis_array.item(0).hasChildNodes())
			{
				var lvon=xml.getElementsByTagName('lvon').item(0).firstChild.data;
				var lbis=xml.getElementsByTagName('lbis').item(0).firstChild.data;
				text="posledni vykon od "+lvon.substring(10,lvon.length-3)+" do "+lbis.substring(10,lbis.length-3);
				chybacasuE.innerHTML+='<p>'+text+'</p>';
				chybacasuE.innerHTML+='<input type="button" onclick="enableneu();" value="povolit zapis !!!"/>';
				//alert(text);
			}
		}
		//alert(e.value);
		
		//e.value=field;
		//e.focus();
		//e.select();
		// pokud dany den namem jeste zadny vykon, napisu upozorneni, ale nezakazu vlozeni zaznamu
		if(field=="ERROR-NOLEIST")
			var neuButton = document.getElementById('neu').disabled=false;
		else
			var neuButton = document.getElementById('neu').disabled=true;
	}
	else
	{
		e.style.backgroundColor='';
		e_von.style.backgroundColor='';
		var neuButton = document.getElementById('neu').disabled=false;
	}
}

function enableneu()
{
	var neuButton = document.getElementById('neu').disabled=false;
}

function validate_von(xml)
{
	//var sql=xml.getElementsByTagName('sql').item(0).firstChild.data;
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	var e_bis=document.getElementById('bis');
	var chybacasuE = document.getElementById('chybacasu');
        var reducedoes_array=xml.getElementsByTagName('reducedoe');
        var field=xml.getElementsByTagName('von').item(0).firstChild.data;

	//alert(field);
	//alert(sql);
	if(field.substring(0,5)=="ERROR")
	{
		// mam nejakou chybu pri zadavani casu
		// zjistim typ chyby
		
		typ_chyby=xml.getElementsByTagName('errordescription').item(0).firstChild.data;
		e.style.backgroundColor='red';
		e_bis.style.backgroundColor='red';
		
		
		chybacasuE.style.visibility='visible';
		chybacasuE.innerHTML='<h1>'+typ_chyby+'</h1>';
		//alert(typ_chyby);
		
		var lastvonbis_array=xml.getElementsByTagName('lastvonbis');
		if(lastvonbis_array.item(0).hasChildNodes())
		{
			var lvon=xml.getElementsByTagName('lvon').item(0).firstChild.data;
			var lbis=xml.getElementsByTagName('lbis').item(0).firstChild.data;
			text="posledni vykon od "+lvon.substring(10,lvon.length-3)+" do "+lbis.substring(10,lbis.length-3);
			chybacasuE.innerHTML+='<p>'+text+'</p>';
			chybacasuE.innerHTML+='<input type="button" onclick="enableneu();" value="povolit zapis !!!"/>';

			//alert(text);
		}
		//alert(e.value);
		
		//e.value=field;
		//e.focus();
		//e.select();
		var neuButton = document.getElementById('neu').disabled=true;
	}
	else
	{
		e.style.backgroundColor='';
		e_bis.style.backgroundColor='';
		chybacasuE.style.visibility='hidden';
		var neuButton = document.getElementById('neu').disabled=false;
	}

        if(reducedoes_array.item(0).hasChildNodes())
            reducedoes = reducedoes_array.item(0).firstChild.data;
        else
            reducedoes = ' ';
//        document.getElementById('oe').value = reducedoes;

        oeselArray = reducedoes.split(';');
//        alert('oeselArray='+oeselArray);
        if(oeselArray.length>1)
            oesel = oeselArray[0];
        else
            oesel = oeselArray;
        
        document.getElementById('oesel').value = oesel;
        $('#oeselect').val(oesel);

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


function abgnr_onkeyup(controlid,value)
{
	var e_tatnrarray=document.getElementById('tatnrarray');
	var e_tatbez=document.getElementById('tatbez');
	var e=document.getElementById(controlid);
	
	k=e.value;
	// vytvorim si pole moznych cinnosti, hodnoty prevzate z policka tatnrarray
	abgnrarray = e_tatnrarray.value.split(',');
	nove_abgnrarray = new Array();
	nasel=0;
	cislo_policka=controlid.substr(3)*1;
	
	if(k!=0&&k.length>0)
	{
		// vytvorim si nove pole abgnrarray ve kterem nebudou prvky, ktere jsou obsazene v predchozich polickach
		for(i=0;i<abgnrarray.length;i++)
		{
			nasel_policko=0;
			
			for(j=1;j<cislo_policka;j++)
			{
				id_policka='tat'+j;
				hodnota_k_odstraneni=document.getElementById(id_policka).value;
				if(hodnota_k_odstraneni==abgnrarray[i])
				{
					nasel_policko=1;
					break;
				}
			}

			if(!nasel_policko)
			nove_abgnrarray.push(abgnrarray[i]);
		}
		
		for(i=0;i<nove_abgnrarray.length;i++)
		{
			if(nove_abgnrarray[i].indexOf(k)==0)
			{
				nasel=1;
				break;
			}
		}
	}
	
	if(nasel)
	{
		e_tatbez.value=nove_abgnrarray[i];
		e.value=nove_abgnrarray[i];
		
		// vyselektovat rozsah retezce k prepsani
		selectRange(e,k.length,10);
		
	}
	//e_tatbez.value=e.value;
	
}


function showattachment(xml)
{
	//alert('validate_auss_typ');
	
	var field=xml.getElementsByTagName('attachment_path').item(0).firstChild.data;
	//var sql=xml.getElementsByTagName('sql').item(0).firstChild.data;
	//alert(field);
	//alert(sql);
	var e=document.getElementById(xml.getElementsByTagName('controlid').item(0).firstChild.data);
	alert(field);
	//alert(field);
	if(field.substring(0,5)=="ERROR")
	{
		e.style.backgroundColor='red';
		//e.value=field;
		//e.focus();
		//e.select();
		
	}
	else
	{
		e.style.backgroundColor='';
		nove = window.open("");
		nove.location.href=field;
	}
	
}

function removerow(xml)
{
	var id = xml.getElementsByTagName('drueck_id').item(0).firstChild.nodeValue;
	radekid = 'tr'+id;
	var radek = document.getElementById(radekid);
	var tabulka = document.getElementById('druecktab');
	// kdyz budu mit platnej index radku pouziju mazani radku u tabulky, jinak budu mazat node
	if(radek.rowIndex!=-1)
		tabulka.deleteRow(radek.rowIndex);
	else	
		tabulka.removeChild(radek);
}

function savefilterparam()
{
	var filterparamE = document.getElementById('filterparam');
	var auftragsnr = document.getElementById('auftragsnr').value;
	var teil = document.getElementById('teil').value;
	var pal = document.getElementById('pal').value;
	var taetnr = document.getElementById('taetnr').value;
	var datum = document.getElementById('datum').value;
	var persnr = document.getElementById('persnr').value;
	
	filterparamE.value=auftragsnr+';'+teil+';'+pal+';'+taetnr+';'+datum+';'+persnr;
}

function refreshwhere(xml)
{

	var scroll_apl = document.getElementById('scroll_apl');
	var drueck_tabulka = document.getElementById('druecktab');
	// zlikviduju tabulku
	scroll_apl.removeChild(drueck_tabulka);
	
	//vyrobim novou tabulku
	drueck_tabulka_new = document.createElement("table");
	drueck_tabulka_new.setAttribute('class','dauftr_table');
	drueck_tabulka_new.setAttribute('id','druecktab');
		
	// vyrobim hlavicku tabulky
	var hlavickaTR = document.createElement("tr");
	hlavickaTR.setAttribute('class','dauftr_table_header');
	// do radku s hlavickou pridam sloupce
	popisky = ["Auftragsnr","Teil","Pal","TaetNr","Stk","Auss","AArt","ATyp","VzKd","VzAby","Datum","PersNr",
	"von","bis","verb","Pause","OE","auft","user","stamp"," "];

	align = ["","","","","right","right","right","right","right","right","right","","right","","","right","right","right","","","",""];

	for(k=0;k<popisky.length;k++)
	{
		var TDnode = document.createElement('td');
		var TextNode = document.createTextNode(popisky[k]);
		TDnode.appendChild(TextNode);
		hlavickaTR.appendChild(TDnode);
	}
	
	// pripojim hlavicku k tabulce
	drueck_tabulka_new.appendChild(hlavickaTR);
	// -----------------------------------------------------------------------------------------------------
	
	// vytvorim radky z xml
	var drueckRows = xml.getElementsByTagName('row');
	for(i=0;i<drueckRows.length;i++)
	{
		drueckRowElement = document.createElement('tr');
		
		if(i%2) 
			drueckRowElement.setAttribute('class','lichy');
		else
			drueckRowElement.setAttribute('class','sudy');
			
		var drueckColumns = drueckRows.item(i).childNodes;
		
		// nastavim id pro radek
		var radekid='tr'+drueckColumns.item(0).firstChild.nodeValue;
		drueckRowElement.setAttribute('id',radekid);
		
		
		for(j=1;j<drueckColumns.length-1;j++)	// length-1, exportflag nechci zobrazit v tabulce
		{
			var drueckTdElement = document.createElement('td');
			if(drueckColumns.item(j).hasChildNodes())
				var drueckTextNode = document.createTextNode(drueckColumns.item(j).firstChild.nodeValue);
			else
				var drueckTextNode = document.createTextNode('');
			drueckTdElement.appendChild(drueckTextNode);
			drueckTdElement.setAttribute('align',align[j]);
			drueckRowElement.appendChild(drueckTdElement);
		}
		
		var exportFlag = drueckColumns.item(drueckColumns.length-1).firstChild.nodeValue;
		
		// pripojim tlacitko storno
		var TDbuttonnode = document.createElement('td');
		var inputNode = document.createElement('input');
		
		buttonid = drueckColumns.item(0).firstChild.nodeValue;
		
		onclickString = "getDataReturnXml('./stornorow.php?id="+buttonid+"',removerow);";
		inputNode.setAttribute('onclick',onclickString);
		inputNode.setAttribute('type','button');
		inputNode.setAttribute('class','stornobutton');
		if(exportFlag=='1')
			inputNode.style.backgroundColor='grey';
		//inputNode.setAttribute('value',buttonid);
		if(exportFlag=='1')
			inputNode.setAttribute('value','');
		else
			inputNode.setAttribute('value','stor');
			
		inputNode.setAttribute('id',buttonid);
		if(exportFlag=='1') 
			inputNode.setAttribute('disabled','disabled');
		
		//alert('id='+id);
		TDbuttonnode.appendChild(inputNode);
		
		// pripojim tlacitko edit
		var inputNode1 = document.createElement('input');
		onclickString= "window.location.href='./editrow.php?id="+buttonid+"&exportflag="+exportFlag+"';";
		//onclickString = "getDataReturnXml('./stornorow.php?id="+buttonid+"',removerow);";
		inputNode1.setAttribute('onclick',onclickString);
		inputNode1.setAttribute('type','button');
		inputNode1.setAttribute('class','editbutton');
		//inputNode.setAttribute('value',buttonid);
		inputNode1.setAttribute('value','edit');
		inputNode1.setAttribute('id',buttonid);
		//alert('id='+id);
		TDbuttonnode.appendChild(inputNode1);
		
		drueckRowElement.appendChild(TDbuttonnode);

		drueck_tabulka_new.appendChild(drueckRowElement);
	}
	
	// pridam tabulku do divu
	scroll_apl.appendChild(drueck_tabulka_new);
	
	// uvolnim tlacitko pro filtr
	var eFiltr = document.getElementById('filtruj');
	eFiltr.style.backgroundColor='';
	eFiltr.disabled=false;
	// a opet nastavim fokus na auftragsnr
	document.getElementById('auftragsnr').focus();document.getElementById('auftragsnr').select();
}

function refreshwhere1(xml)
{

        if($('#druecktab').length!=0){
            $('#druecktab').remove();
        }
	$('#stornodiv').hide();

	//vyrobim novou tabulku
	
	drueck_tabulka_new = document.createElement("table");
	drueck_tabulka_new.setAttribute('class','dauftr_table');
	drueck_tabulka_new.setAttribute('id','druecktab');
		
	// vyrobim hlavicku tabulky
	var hlavickaTR = document.createElement("tr");
	hlavickaTR.setAttribute('class','dauftr_table_header');
	// do radku s hlavickou pridam sloupce
	popisky = ["Auftragsnr","Teil","Pal","TaetNr","Stk","Auss","AArt","ATyp","VzKd","VzAby","Datum","PersNr",
	"von","bis","verb","Pause","OE","auft","user","stamp"," "];

	align = ["","","","","right","right","right","right","right","right","right","","right","","","right","right","right","","","",""];

	for(k=0;k<popisky.length;k++)
	{
		var TDnode = document.createElement('td');
		var TextNode = document.createTextNode(popisky[k]);
		TDnode.appendChild(TextNode);
		hlavickaTR.appendChild(TDnode);
	}
	
	// pripojim hlavicku k tabulce
	drueck_tabulka_new.appendChild(hlavickaTR);
	
	// -----------------------------------------------------------------------------------------------------
	
	// vytvorim radky z xml
	var drueckRows = xml.getElementsByTagName('row');
	for(i=0;i<drueckRows.length;i++)
	{
		drueckRowElement = document.createElement('tr');
		
		if(i%2) 
			drueckRowElement.setAttribute('class','lichy');
		else
			drueckRowElement.setAttribute('class','sudy');
			
		var drueckColumns = drueckRows.item(i).childNodes;
		
		// nastavim id pro radek
		var radekid='tr'+drueckColumns.item(0).firstChild.nodeValue;
		drueckRowElement.setAttribute('id',radekid);
		
		
		for(j=1;j<drueckColumns.length-1;j++)	// length-1, exportflag nechci zobrazit v tabulce
		{
			var drueckTdElement = document.createElement('td');
			if(drueckColumns.item(j).hasChildNodes())
				var drueckTextNode = document.createTextNode(drueckColumns.item(j).firstChild.nodeValue);
			else
				var drueckTextNode = document.createTextNode('');
			drueckTdElement.appendChild(drueckTextNode);
			drueckTdElement.setAttribute('align',align[j]);
			drueckRowElement.appendChild(drueckTdElement);
		}
		
		var exportFlag = drueckColumns.item(drueckColumns.length-1).firstChild.nodeValue;
		
		// pripojim tlacitko storno
		var TDbuttonnode = document.createElement('td');
		var inputNode = document.createElement('input');
		
		buttonid = drueckColumns.item(0).firstChild.nodeValue;
		
		onclickString = "getDataReturnXml('./stornorow.php?id="+buttonid+"',removerow);";
		inputNode.setAttribute('onclick',onclickString);
		inputNode.setAttribute('type','button');
		inputNode.setAttribute('class','stornobutton');
		if(exportFlag=='1')
			inputNode.style.backgroundColor='grey';
		//inputNode.setAttribute('value',buttonid);
		if(exportFlag=='1')
			inputNode.setAttribute('value','');
		else
			inputNode.setAttribute('value','stor');
			
		inputNode.setAttribute('id',buttonid);
		if(exportFlag=='1') 
			inputNode.setAttribute('disabled','disabled');
		
		//alert('id='+id);
		TDbuttonnode.appendChild(inputNode);
		
		// pripojim tlacitko edit
		var inputNode1 = document.createElement('input');
		onclickString= "window.location.href='./editrow.php?id="+buttonid+"&exportflag="+exportFlag+"';";
		//onclickString = "getDataReturnXml('./stornorow.php?id="+buttonid+"',removerow);";
		inputNode1.setAttribute('onclick',onclickString);
		inputNode1.setAttribute('type','button');
		inputNode1.setAttribute('class','editbutton');
		//inputNode.setAttribute('value',buttonid);
		inputNode1.setAttribute('value','edit');
		inputNode1.setAttribute('id',buttonid);
		//alert('id='+id);
		TDbuttonnode.appendChild(inputNode1);
		
		drueckRowElement.appendChild(TDbuttonnode);

		drueck_tabulka_new.appendChild(drueckRowElement);
	}
	
	divObsah = "<input type='button' id='closestornodiv' value='X'/>";
//	$('#stornodiv').html(drueck_tabulka_new);
	$('#stornodiv').html(drueck_tabulka_new);
	$('#stornodiv').prepend(divObsah);
	$('#closestornodiv').click(closeStornoDiv);
	$('#stornodiv').show('normal');
}

function closeStornoDiv(event){
    $('#stornodiv').hide();
}

function prenos_vzaby(element)
{
	var e_tat1 = document.getElementById('tat1_abymin');
	e_tat1.value=element.value;
}

function disableneu()
{
  	var neuE = document.getElementById('neu');
  	neuE.disabled=true;
}

function markfocus(element)
{
	var e = document.getElementById(element.id);
	var eAktual = document.getElementById('elementaktual');
	var divAktual = document.getElementById('eaktualinfo');
	eAktual.value = e.id;
	i = zjistiId(e.id);
	divAktual.innerHTML = e.id+': <i>'+poleNapoveda[i]+'</i>';
	
	// oznaceni aktualniho pole zlutym pozadim
	e.style.backgroundColor='#ffffbb';
}

function makeButtonBusy(element)
{
	element.style.backgroundColor='#ffaaaa';
	element.disabled=true;
}

function refreshaplinfo(xml)
{
	var aplinfo = document.getElementById('aplinfo');
	
	var staratabulka = document.getElementById('aplinfotab');
	// pokud mam starou tabulku, tak jen zobrazim, jinak musim vyrobit novou
	if(staratabulka)
		aplinfo.style.visibility='visible';
	else
	{
		//aplinfo.removeChild(staratabulka);
		// zviditelnim div
		aplinfo.style.visibility='visible';
	
		//vyrobim novou tabulku
		apltabulka = document.createElement("table");
		apltabulka.setAttribute('class','dauftr_table');
		apltabulka.setAttribute('id','aplinfotab');
			
		// vyrobim hlavicku tabulky
		var hlavickaTR = document.createElement("tr");
		hlavickaTR.setAttribute('class','dauftr_table_header');
		// do radku s hlavickou pridam sloupce
		popisky = ["abgnr","dtaetkz","name","oper_cz","oper_d"];
	
		align = ["right","left","left","left","left"];
	
		for(k=0;k<popisky.length;k++)
		{
			var TDnode = document.createElement('td');
			var TextNode = document.createTextNode(popisky[k]);
			TDnode.appendChild(TextNode);
			hlavickaTR.appendChild(TDnode);
		}
		
		// pripojim hlavicku k tabulce
		apltabulka.appendChild(hlavickaTR);
		// -----------------------------------------------------------------------------------------------------
		
		// vytvorim radky z xml
		
		var tatRows = xml.getElementsByTagName('tat');
		
		//alert('pocetradku='+tatRows.length);
		
		for(i=0;i<tatRows.length;i++)
		{
			tatRowElement = document.createElement('tr');
			
			if(i%2) 
				tatRowElement.setAttribute('class','lichy');
			else
				tatRowElement.setAttribute('class','sudy');
				
			var tatColumns = tatRows.item(i).childNodes;
			
			// nastavim id pro radek
			var radekid='tr'+tatColumns.item(0).firstChild.nodeValue;
			tatRowElement.setAttribute('id',radekid);
		
			// pri kliknuti na radek div schovam
			onclickfunkce = "document.getElementById('aplinfo').style.visibility='hidden';";
			tatRowElement.setAttribute('onclick',onclickfunkce);
			
			for(j=0;j<tatColumns.length;j++)
			{
				var tatTdElement = document.createElement('td');
				if(tatColumns.item(j).hasChildNodes())
					var tatTextNode = document.createTextNode(tatColumns.item(j).firstChild.nodeValue);
				else
					var tatTextNode = document.createTextNode('');
				tatTdElement.appendChild(tatTextNode);
				tatTdElement.setAttribute('align',align[j]);
				tatRowElement.appendChild(tatTdElement);
			}
	
			apltabulka.appendChild(tatRowElement);
		}
		
		// pridam tabulku do divu
		aplinfo.appendChild(apltabulka);
	}	
		
}