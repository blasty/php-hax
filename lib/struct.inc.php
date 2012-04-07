<?
	function struct($defs) {
		$res = new bin_struct($defs);
		return $res;
	}

	class bin_struct {
		public $fields;

		private $fmt = array(
			'u8'  => 'C', 's8'  => 'c',
			'u16' => 'v', 's16' => 's', 'u16be' => 'n',
			'u32' => 'V', 's32' => 'l', 'u32be' => 'N'
		);

		private $sizes = [
			'u8' => 1, 's8' => 1,
			'u16' => 2, 's16' => 2, 'u16be' => 2,
			'u32' => 4, 's32' => 4, 'u32be' => 4
		];

		function __sleep() {
			return array("herp", "derp", "smerp");
		}

		function &__get($name) {
			if (!isset($this->fields[$name]))
				return NULL;

			return ($this->fields[$name]['len'] == 1) ?
				$this->fields[$name]['val'] :
				$this->fields[$name]['val']
			;
		}

		function __set($name, $value) {
			if (!isset($this->fields[$name]))
				trigger_error("Invalid struct property '{$name}' :(", E_USER_ERROR);

			if ($this->fields[$name]['len'] == 1)
				$this->fields[$name]['val'][0] = $value;
			else
				$this->fields[$name]['val'] = $value;
		}

		function __construct($fields) {
			foreach($fields as $field_name => $field_type) {
				if (isset($this->fields[ $field_name ]))
					trigger_error("Duplicate struct field '{$field_name}'", E_USER_ERROR);

				$type = $this->parse_type($field_type);

				$this->fields[ $field_name ] = [
					'type'  => $type['type'],
					'val' => ($type['len'] == 1) ? 0 : array_fill(0, $type['len'], 0),
					'len'   => $type['len'],
					'fmt'   => $type['fmt'],
					'size'  => $this->sizes[ $type['type'] ]	
				];
			}
		}

		private function parse_type($type_val) {
			$type = (is_array($type_val)) ? $type_val[0] : $type_val;
			$len  = (is_array($type_val)) ? $type_val[1] : 1;

			if (!in_array($type, array_keys($this->fmt)))
				trigger_error("Invalid data-type '{$type}'", E_USER_ERROR);

			return [
				'type' => $type,
				'fmt' => $this->fmt[$type],
				'len'  => $len,
				'size' => $this->sizes[$type]
			];
		}

		function read($buf) {
			$pos = 0;

			foreach($this->fields as $name => $meta) {
				$data = substr($buf, $pos, $meta['size'] * $meta['len']);
				$uval = unpack($meta['fmt'].$meta['len']."/", $data);

				$val  = array();

				foreach($uval as $v)
						$val[] = $v;

				$this->fields[$name]['val'] = ($meta['len'] == 1) ? $val[0] : $val;

				$pos += ($meta['size'] * $meta['len']);
			}
		}

		function write() {
			$o = '';

			foreach($this->fields as $name => $meta) {
				$o .= call_user_func_array(pack, array_merge(
					array($meta['fmt'].$meta['len']),
					$meta['val']	
				));				
			}

			return $o;
		}
	}
?>
