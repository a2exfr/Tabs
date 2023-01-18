<?php

class TabView{

	protected $template_dir = 'templates/';
	protected $vars = [];

	public function __construct($template_dir = null){
		if ($template_dir !== null){
			//  should check here if this dir really exists
			$this->template_dir = $template_dir;
		}
	}

	public function render($template_file){
		global $addonPathCode;

		if (file_exists($addonPathCode . '/' . $this->template_dir . $template_file)){
			require_once $addonPathCode . '/' . $this->template_dir . $template_file;
		} else {
			throw new Exception('no template file ' . $template_file . ' present in directory ' . $addonPathCode . '/' . $this->template_dir);
		}
	}

	public function __set($name, $value){
		$this->vars[$name] = $value;
	}

	public function __get($name){
		return $this->vars[$name];
	}

	public function __isset($name){
		return $this->vars[$name];
	}

}

?>