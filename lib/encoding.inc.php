<?
	function hex($in) {
		return join(array_map(
			create_function('$a', 'return sprintf("%02X", ord($a));'),
			str_split($in)
		));
	}

	function unhex($in) {
		return join(array_map(
			create_function('$a', 'return chr(hexdec($a));'),
			str_split($in, 2)
		));
	}

	function bin($in, $force_width = false) {
		$t = (ctype_digit($in)) ? "integer" : gettype($in);

		if ($t == "integer") {
			if ($force_width !== false)
				$padlen = $force_width;
			else {
				if      ($in <=       0xff) $padlen = 8;
				else if ($in <=     0xffff) $padlen = 16;
				else if ($in <= 0xffffffff) $padlen = 32;
			}

			return str_pad(decbin($in), $padlen, "0", STR_PAD_LEFT);
		} else if ($t == "string") {
			return join(array_map(
				create_function('$a', 'return str_pad(decbin(ord($a)), 8, "0", STR_PAD_LEFT);'),
				str_split($in)
			));
		}
	}

	function u8   ($in) { return pack("C", $in); }
	function u16  ($in) { return pack("v", $in); }
	function u16be($in) { return pack("n", $in); }
	function u32  ($in) { return pack("V", $in); }
	function u32be($in) { return pack("N", $in); }

	function b64  ($in) { return base64_encode($in); }
	function unb64($in) { return base64_decode($in); }
?>
