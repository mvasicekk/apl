<?

session_start();
require "../fns_dotazy.php";
dbConnect();

$dpos_id = $_GET['dpos_id'];

// nejdriv zjistim v jakem stavu je kz-druck
$sql = "select `kz-druck` as kz_druck,`kz_aktiv` from dpos where (dpos_id='" . $dpos_id . "')";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);


if ($row['kz_druck'] != 0) {
    if ($row['kz_aktiv'] == 0) {
	$sql = "update dpos set `kz_aktiv`=1 where (dpos_id='" . $dpos_id . "')";
	$novabarva = 'red';
    } else {
	$sql = "update dpos set `kz_aktiv`=0 where (dpos_id='" . $dpos_id . "')";
	$novabarva = 'grey';
    }
}
else{
    $novabarva = '';
}

$res = mysql_query($sql);

echo "$dpos_id:$novabarva";
?>

