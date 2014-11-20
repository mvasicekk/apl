<?
require_once '../../security.php';
require "../fns_dotazy.php";
dbConnect();

	$dpos_id = $_GET['dpos_id'];

	// nejdriv zjistim v jakem stavu je kz-druck
	$sql="select `kz-druck` from dpos where (dpos_id='".$dpos_id."')";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	
	
	if($row['kz-druck']==0)
	{
		$sql="update dpos set `kz-druck`=-1 where (dpos_id='".$dpos_id."')";
		$novabarva='red';
	}
	else
	{
		$sql="update dpos set `kz-druck`=0 where (dpos_id='".$dpos_id."')";
		$novabarva='grey';
	}
	
	$res=mysql_query($sql);
	
	echo "$dpos_id:$novabarva";
	
?>

