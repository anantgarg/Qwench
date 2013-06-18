<?php
class Template {
	
	protected $variables = array();
	protected $_controller;
	protected $_action;
	
	function __construct($controller,$action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}

	/** Set Variables **/

	function set($name,$value) {
		$this->variables[$name] = $value;
	}

	function overrideController($controller) {
		$this->_controller = $controller;
	}

	function overrideAction($action) {
		$this->_action = $action;
	}

	/** Display Template **/
	
    function render($doNotRenderHeader = 0) {
		
		extract($this->variables);
		
		if ($doNotRenderHeader == 0) {
			
			if (file_exists(ROOT . DS . 'views' . DS . $this->_controller . DS . 'header.php')) {
				include (ROOT . DS . 'views' . DS . $this->_controller . DS . 'header.php');
			} else {
				include (ROOT . DS . 'views' . DS . 'header.php');
			}
		}

		if (file_exists(ROOT . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php')) {
			include (ROOT . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');		 
		}
			
		if ($doNotRenderHeader == 0) {
			if (file_exists(ROOT . DS . DS . $this->_controller . DS . 'footer.php')) {
				include (ROOT . DS . DS . $this->_controller . DS . 'footer.php');
			} else {
				include (ROOT . DS . 'views' . DS . 'footer.php');
			}
		}
		exit();
    }

}