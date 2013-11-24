<?
	require_once('suggest.class.php');

	$suggest = new Suggest();

	$keyword = $_GET['keyword'];

	if(ob_get_length()) ob_clean();

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');

	echo $suggest->getSuggestions($keyword);
?>

<?

    // pomocny objekt k otevreni skladu

?>