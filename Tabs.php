<?php defined('is_running') or die('Not an entry point...');

gpPlugin_incl('tools/TabView.php');

class Tabs{

	static function GetHead(){
		global $page, $addonRelativeCode, $addonRelativeData, $addonPathData, $gpversion;

		if (version_compare($gpversion, '5.1.0', '<=')){
			$bootstrap = 'b3';
			common::LoadComponents('bootstrap3-tab');
			common::LoadComponents('bootstrap3-collapse');

			$page->css_user[] = $addonRelativeCode . '/css/bootstrap.css'; //only tabs,panels,listgroup
			$page->css_user[] = $addonRelativeCode . '/css/tab.css';
		} else {
			$bootstrap = 'b4';
			common::LoadComponents('bootstrap4-tab');
			common::LoadComponents('bootstrap4-collapse');
		}

		if (file_exists($addonPathData . '/tab_custom.css')){
			$page->css_user[] = $addonRelativeData . '/tab_custom.css';
		}

		global $addonPathData;

		$configFile = $addonPathData . '/data_groups.php';
		$optionsFile = $addonPathData . '/options.php';

		if (file_exists($optionsFile)){
			include $optionsFile;
		} else {
			$options = [];
			$options['tabs_state'] = "";
		}

		if ($options['tabs_state'] && $bootstrap == 'b3'){
			$page->head_js[] = $addonRelativeCode . '/js/tabs_state.js';
		}

		if (file_exists($configFile)){
			include $configFile;
		} else {
			$data = [];
			$tab_groups = [];
		}

		$tab_group_t = $data;
		foreach ($tab_group_t as $key => $tab_gp) {
			$tab_groups[] = ['id' => $key, 'title' => $tab_gp['title']];
		}

		$page->head_script .= "\n" . 'var tab_groups = ' . json_encode($tab_groups) . ';';

	}

	static function SectionTypes($section_types){
		$section_types['Tabs_section'] = [];
		$section_types['Tabs_section']['label'] = 'Tabs';
		return $section_types;
	}


	static function SectionToContent($section_data){
		if ($section_data['type'] != 'Tabs_section'){
			return $section_data;
		}

		global $addonPathData, $gpversion;

		if (version_compare($gpversion, '5.1.0', '<=')){
			$bootstrap = 'b3';
		} else {
			$bootstrap = 'b4';
		}

		$t_id = $section_data['tabs_id'];
		$group_id = $section_data['tab_sel'];
		$appearance = $section_data['appearance'];
		$temp = new TabView();

		if (array_key_exists('panel_class', $section_data)){
			$panel_class = $section_data['panel_class'];
		} else {
			$panel_class = "panel-default";
		}
		if (array_key_exists('opened', $section_data)){
			$opened = $section_data['opened'];
		} else {
			$opened = "yes";
		}

		$configFile = $addonPathData . '/' . $group_id . '.php';

		if (file_exists($configFile)){
			include $configFile;
		} else {
			$data = [];
		}

		ob_start();

		if ($group_id == 0){
			$temp->render($bootstrap . '-demo.phtml');
		} elseif ($appearance == 1) {

			if ($opened == "yes"){
				$opened = "in";
			} else {
				$opened = "";
			}
			$temp->t_id = $t_id;
			$temp->opened = $opened;
			$temp->panel_class = $panel_class;
			$temp->data = $data;
			$temp->render($bootstrap . '-accordion.phtml');

		} else {
			if ($appearance == 0){
				$b = '';
			} elseif ($appearance == 2) {
				$b = 'class="tabbable tabs-left"';
			} else {
				$b = 'class="tabbable tabs-right"';
			}

			$temp->t_id = $t_id;
			$temp->b = $b;
			$temp->data = $data;
			$temp->render($bootstrap . '-tabs.phtml');
		}

		$section_data['content'] = ob_get_clean();

		return $section_data;

	}

	static function NewSections($links){

		global $addonRelativeCode;

		foreach ($links as $key => $section_type_arr) {
			if ($section_type_arr[0] == 'Tabs_section'){
				$links[$key] = ['Tabs_section', $addonRelativeCode . '/img/tabs.jpg'];
			}

		}

		return $links;
	}


	static function DefaultContent($default_content, $type){
		if ($type != 'Tabs_section'){
			return $default_content;
		}

		$section = [];
		$section['content'] = '<p>Tabs section</p>';

		$section['tabs_id'] = "tabs" . crc32(uniqid("", true));
		$section['tab_sel'] = 0;
		$section['appearance'] = 0;
		$section['panel_class'] = "panel-default";
		$section['opened'] = "yes";

		if (!$_SERVER['gp_rewrite']){

			$section['index'] = 0;

		} else {

			$section['index'] = 1;

		}

		return $section;
	}


	static function SaveSection($return, $section, $type){
		if ($type != 'Tabs_section'){
			return $return;
		}
		global $page;
		$page->file_sections[$section]['tab_sel'] = &$_POST['tab_sel'];
		$page->file_sections[$section]['appearance'] = &$_POST['app'];
		$page->file_sections[$section]['panel_class'] = &$_POST['panel_class'];
		$page->file_sections[$section]['opened'] = &$_POST['opened'];

		if (!$_SERVER['gp_rewrite']){

			$page->file_sections[$section]['index'] = 0;

		} else {

			$page->file_sections[$section]['index'] = 1;

		}

		return true;
	}


	static function InlineEdit_Scripts($scripts, $type){
		if ($type !== 'Tabs_section'){
			return $scripts;
		}
		global $addonRelativeCode;
		$scripts[] = $addonRelativeCode . '/js/edit.js';

		return $scripts;
	}


	static function PageRunScript($cmd){
		global $page, $addonRelativeCode;

		if ($cmd == 'refresh_section_tabs'){

			$page->ajaxReplace = [];

			$tab_sel = &$_REQUEST['tab_sel'];
			$appearance = &$_REQUEST['app'];
			$tabs_id = &$_REQUEST['tabs_id'];
			$panel_class = &$_REQUEST['panel_class'];
			$opened = &$_REQUEST['opened'];

			$sect_options = [
				'type' => 'Tabs_section', 'tab_sel' => $tab_sel, 'appearance' => $appearance,
				'tabs_id' => $tabs_id, 'panel_class' => $panel_class, 'opened' => $opened,
			];

			$arg_value = \gp\tool\Output\Sections::SectionToContent($sect_options, '');

			$page->ajaxReplace[] = ['refresh_tabsFn', 'arg', $arg_value];

			return 'return';
		}

		return $cmd;
	}

}

