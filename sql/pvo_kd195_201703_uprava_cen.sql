update dkopf
set 
    dkopf.preis_stk_gut=round(dkopf.preis_stk_gut*(1-1.8/100),2),
    dkopf.preis_stk_auss=round(dkopf.preis_stk_auss*(1-1.8/100),2)
where
    kunde=195;
update dpos
join dkopf on dpos.Teil=dkopf.Teil
    set dpos.`VZ-min-kunde`=round(dpos.`VZ-min-kunde`*(1-1.8/100),2),
    dpos.`VZ-min-aby`=round(dpos.`VZ-min-aby`*(1-2/100),2)
where
    kunde=195
    and
    dpos.`kz-druck`<>0
    and
    dpos.`TaetNr-Aby`<>1701
    and
    dpos.`TaetNr-Aby`<>95
