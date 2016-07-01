-- uprava casu pro barvu
update dpos
join dkopf on dkopf.Teil=dpos.teil
    set dpos.`VZ-min-kunde`=round((0.43/0.44/DPOS.`VZ-min-kunde`,4)
where
(dkopf.kunde=130)
and
(dpos.`TaetNr-Aby` between 1100 and 1299);
-- uprava casu pro tryskani
update dpos
join dkopf on dkopf.Teil=dpos.teil
    set dpos.`VZ-min-kunde`=round((0.43/0.44)*dpos.`VZ-min-kunde`,4)
where
(dkopf.kunde=130)
and
(
    dpos.`TaetNr-Aby`=5 
    or dpos.`TaetNr-Aby`=6 
    or dpos.`TaetNr-Aby`=7 
    or dpos.`TaetNr-Aby`=8 
    or dpos.`TaetNr-Aby`=46 
    or dpos.`TaetNr-Aby`=47 
    or dpos.`TaetNr-Aby`=48
);
