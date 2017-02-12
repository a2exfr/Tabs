<?php
/*
Author: a2exfr
http://my-sitelab.com/

*/


defined('is_running') or die('Not an entry point...');


class Admin_Tabs_Options{
	
	function __construct(){
		global $page, $addonRelativeCode,$addonPathData;
		
		common::LoadComponents('bootstrap-css');
		$page->css_admin[] = $addonRelativeCode . '/bootstrap_colorpicker/css/bootstrap-colorpicker.min.css';
		$page->head_js[] =   $addonRelativeCode . '/bootstrap_colorpicker/js/bootstrap-colorpicker.min.js';
		$page->head_js[] =   $addonRelativeCode . '/js/tab_admin2.js ';	
		
		$this->css_file = $addonPathData . '/tab_custom.css';
		
		$this->loadConfig();
	
		$cmd = common::GetCommand();

    switch($cmd){
      case 'saveConfig':
        $this->saveConfig();
	//	header('Refresh:0;');
		break;
    }
		$this->get_lang_ext();
		$this->showForm();
		

	
	
	}
	
	 function showForm()
  {
   global $langmessage;
   
   echo '<form action="'.common::GetUrl('Admin_Tabs_Options').'" method="post">';
  
	echo '<table class="bordered" style="width:100%;">';
    echo '<tr><th>'.$langmessage['Settings'].'</th><th>'.$langmessage['Value'].'</th></tr>';
  
  
    echo '<tr>';
    echo '<td><p>'.$this->lang_ext['ts'].'</p> </td>';
    echo '<td>';
		if( $this->tabs_state  ){
		echo '<input type="checkbox" name="tabs_state" value="tabs_state" id="tabs_state" checked="checked" />';
			}else{
		echo '<input type="checkbox" name="tabs_state" value="tabs_state" id="tabs_state"/>';
			}
	echo '</td>';
    echo '</tr>';
  
	echo '<tr>';
	echo '<td>';
	echo '<p><strong>Acordion custom style:</strong></p>';
	echo '</td>';
	echo '<td>';
	echo '</td>';
	echo '</tr>';
  
	echo '<tr>';
    echo '<td><p>Content background color</p> </td>';
    echo '<td>';
	echo '<input class="gpinput cp" type="text" name="custom[background]" value="' . $this->custom['background'] . '" />';
	echo '</td>';
    echo '</tr>';
  
  	echo '<tr>';
    echo '<td><p>Borders color</p> </td>';
    echo '<td>';
	echo '<input class="gpinput cp" type="text" name="custom[borders]" value="' . $this->custom['borders'] . '" />';
	echo '</td>';
    echo '</tr>';

  	echo '<tr>';
    echo '<td><p>Header panel text color</p> </td>';
    echo '<td>';
	echo '<input class="gpinput cp" type="text" name="custom[text_color]" value="' . $this->custom['text_color'] . '" />';
	echo '</td>';
    echo '</tr>'; 

 	echo '<tr>';
    echo '<td><p>Panel header background color</p> </td>';
    echo '<td>';
	echo '<input class="gpinput cp" type="text" name="custom[panel_color]" value="' . $this->custom['panel_color']. '" />';	
	echo '</td>';
    echo '</tr>';
	
	
	
  
	echo '</table>';
  
  
	echo '<p>';
  	echo '<input type="hidden" name="cmd" value="saveConfig" />';
    echo '<input type="submit" value="'.$langmessage['save_changes'].'" class="gpsubmit"/>';
    echo '</p>';
    echo '</form>';
	echo '</div>';
  }
  
  
  
  	function get_lang_ext(){
		global $config;
		
		$langfile      	= '/languages/'.$config['language'].'.php';
	
		if(  file_exists( dirname(__FILE__).$langfile ) )	{
		include dirname(__FILE__).$langfile;
			} else {$langfile = '/languages/en.php';
			include dirname(__FILE__).$langfile;
			
			}
			
		$this->lang_ext = $lang_ext;
	
	
	
	}
  
  
  
  	function saveConfig()
  {
    global                   $addonPathData;
    global                   $langmessage;

    $optionsFile            		= $addonPathData.'/options.php';
    $options                		= array();
    
	
	if (isset($_POST['tabs_state'])){
	$options['tabs_state'] 		= $_POST['tabs_state'];
	} else {
		$options['tabs_state'] 		=  "";
		}
	
	$options['custom'] 	= $_POST['custom'];
	
	$this->tabs_state 		= $options['tabs_state'];
	
 
	$success = gpFiles::SaveData($optionsFile,'options',$options);
	$success = $this->saveCSS() ? $success : false;

    if ( $success ){ 
      msg( $langmessage['SAVED'] );
	   return true;
    } else {
      msg( $langmessage['OOPS'] );
	   return false;
    }
  }

	function saveCSS(){
		  global $addonPathData;

		  $this->custom=$_POST['custom'];
		$background = trim($this->custom['background']);
		$borders = trim($this->custom['borders']);
		$text_color = trim($this->custom['text_color']);
		$panel_color = trim($this->custom['panel_color']);
		
	$css ='.panel.panel-custom{
				background-color: '.$background.';
			 }

			.panel-custom {
			  border-color: '.$borders.'!important;
			}
			.panel-custom > .panel-heading {
			  color: '.$text_color.';
			  background-color: '.$panel_color.';
			  border-color: '.$borders.';
			}
			.panel-custom > .panel-heading + .panel-collapse > .panel-body {
			  border-top-color: '.$borders.'!important;
			}
			
			.panel-custom > .panel-footer + .panel-collapse > .panel-body {
			  border-bottom-color: '.$borders.';
			}';
	
	 return gpFiles::Save($this->css_file, $css);
	
	}

  function loadConfig()
  {
    global                   $addonPathData;

    $optionsFile            = $addonPathData.'/options.php';
    

		
		if(  file_exists( $optionsFile ) )	{
				include_once $optionsFile;
		}
		
    if (isset($options)) {
    
	$this->tabs_state		= $options['tabs_state'];
	$this->custom			= $options['custom'];
	 
    } else {
		
		$this->tabs_state = "";
		$this->custom = array();
		$this->custom['background']="";
		$this->custom['borders']="";
		$this->custom['text_color']="";
		$this->custom['panel_color']="";
		
	}
	
  }  
	
	
	
	
	
}
?>