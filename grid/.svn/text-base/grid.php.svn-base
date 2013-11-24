<?
	require_once('error_handler.php');
	require_once('grid.class.php');

	if(!isset($_GET['action']))
	{
		echo 'Server error: client command missing.';
		exit;
	}
	else
	{
		$action=$_GET['action'];
	}

	// vytvorit instanci GRID
	$grid = new Grid($action);

	if($action=='FEED_GRID_PAGE')
	{
		$page=$_GET['page'];
		$grid->readPage($page);
	}
	else if($action=='UPDATE_ROW')
	{
		$id = $_GET['id'];
		$on_promotion = $_GET['on_promotion'];
		$price = $_GET['price'];
		$name = $_GET['name'];
		$grid->updateRecord($id,$on_promotion,$price,$name);
	}
	else
		echo 'Server error: client command unrecognized.';

	if(ob_get_length()) ob_clean();

	header('Expires: Fri, 25 Dec 1980 00:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragme: no-cache');
	header('Content-Type: text/xml');
	header('Content-Type: text/xml');

	echo "<?xml version='1.0' encoding='windows-1250'?>";
	echo "<data>";
	echo "<action>$action</action>";
	echo $grid->getParamsXML();
	echo $grid->getGridXML();
	echo "</data>";

?>
