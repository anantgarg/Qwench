<?php
class Helper {
	
	protected $variables = array();
	protected $_action;
	
	/** Set Variables **/



	function set($name,$value) {
		$this->variables[$name] = $value;
	}

	/** Display Template **/
	
    function render() {

		$backtrace = debug_backtrace();
		$function = strtolower($backtrace[1]['function']);
		
		extract($this->variables);

		$contents = '';
		
		if (file_exists(ROOT . DS . 'views' . DS . 'helpers' . DS . $function . '.php')) {
			$filename = (ROOT . DS . 'views' . DS . 'helpers' . DS . $function . '.php');	
			
			if (is_file($filename)) {
				ob_start();
				include $filename;
				$contents = ob_get_contents();
				ob_end_clean();
			}
		}

		$this->variables = array();
		
		return $contents;

    }

	

}

function getContent($filename) {

    return false;
}
