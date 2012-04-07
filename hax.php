<?
	$ver = explode(".", phpversion());

	if (($ver[0] < 5) || ($ver[0] == 5 && $ver[1] < 4))
		trigger_error("hax.php needs PHP 5.4.0", E_USER_ERROR);

	$libs = glob("lib/*");

	foreach($libs as $lib)
		require_once($lib);
?>
