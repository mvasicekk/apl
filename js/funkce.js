
YAHOO.util.Event.on(document,'click',move);

function move(e) {
	pozice = Array();
	pozice = YAHOO.util.Event.getXY(e);
	//pozice[0]=pozice[0]+50;
	//pozice[1]=pozice[1]+100;
	YAHOO.util.Dom.setXY('b',pozice);
}

