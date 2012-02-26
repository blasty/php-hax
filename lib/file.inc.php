<?
	function content($name) {
		return file_get_contents($name);
	}

	function md5file($name) {
		return md5(content($name));
	}

	function sha1file($name) {
		return sha1(content($name));
	}
?>
