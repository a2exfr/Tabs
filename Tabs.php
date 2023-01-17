<?php defined('is_running') or die('Not an entry point...');

class Tabs{

	function GetHead(){
		global $page, $addonRelativeCode, $addonRelativeData, $addonPathData;
		common::LoadComponents('bootstrap3-tab');
		common::LoadComponents('bootstrap3-collapse');

		$page->css_user[] = $addonRelativeCode . '/css/bootstrap.css'; //only tabs,panels,listgroup
		$page->css_user[] = $addonRelativeCode . '/css/tab.css';

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

		if ($options['tabs_state']){
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

	function SectionTypes($section_types){
		$section_types['Tabs_section'] = [];
		$section_types['Tabs_section']['label'] = 'Tabs';
		return $section_types;
	}


	function SectionToContent($section_data){
		if ($section_data['type'] != 'Tabs_section'){
			return $section_data;
		}

		global $page, $dataDir, $dirPrefix, $addonRelativeCode, $addonPathData, $gp_titles;

		$t_id = $section_data['tabs_id'];
		$group_id = $section_data['tab_sel'];
		$appearance = $section_data['appearance'];

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


			echo '<div class="col-md-6">
	<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a data-toggle="tab" href="#home">Home</a></li>
    <li><a data-toggle="tab" href="#menu1">Menu 1</a></li>
    <li><a data-toggle="tab" href="#menu2">Menu 2</a></li>
    <li><a data-toggle="tab" href="#menu3">Menu 3</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
      <h3>HOME</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </div>
    <div id="menu1" class="tab-pane fade">
      <h3>Menu 1</h3>
      <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <div id="menu2" class="tab-pane fade">
      <h3>Menu 2</h3>
      <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
    </div>
    <div id="menu3" class="tab-pane fade">
      <h3>Menu 3</h3>
      <p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
    </div>
  </div>
  </div>
  ';

			echo '<div class="col-md-6">
		 <div class="panel-group" id="accordion">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Collapsible Group 1</a>
        </h4>
      </div>
      <div id="collapse1" class="panel-collapse collapse in">
        <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
        sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Collapsible Group 2</a>
        </h4>
      </div>
      <div id="collapse2" class="panel-collapse collapse">
        <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
        sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Collapsible Group 3</a>
        </h4>
      </div>
      <div id="collapse3" class="panel-collapse collapse">
        <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
        sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
      </div>
    </div>
  </div>
   </div>
  ';

		} elseif ($appearance == 1) {

			if ($opened == "yes"){
				$opened = "in";
			} else {
				$opened = "";
			};

			echo '<div class="panel-group" id="' . $t_id . '">';
			$a = 'class="panel-collapse collapse ' . $opened . '"';
			foreach ($data as $key => $item) {
				echo '<div class="panel ' . $panel_class . '">';
				echo '<div class="panel-heading">';
				echo '<h4 class="panel-title">';
				echo '<a data-toggle="collapse" data-parent="#' . $t_id . '" href="#' . $t_id . $key . '">' . $item['title'] . '</a>';
				echo '</h4>';
				echo '</div>';

				echo '<div id="' . $t_id . $key . '" ' . $a . '>';
				echo '<div class="panel-body">';
				if (isset($item['page_in_tab']) and $item['page_in_tab'] <> ""){

					if ($item['page_type'] == "special"){
						echo section_content::IncludeSpecial($item['page_in_tab_true']);
					} else {
						echo section_content::IncludePage($item['page_in_tab_true']);
					}

				} else {
					echo $item['content'];
				}
				echo '</div>';
				echo '</div>';

				$a = 'class="panel-collapse collapse"';
				echo '</div>';
			}

			echo '</div>';

		} else {

			if ($appearance == 0){
				$b = '';
			} elseif ($appearance == 2) {
				$b = 'class="tabbable tabs-left"';
			} else {
				$b = 'class="tabbable tabs-right"';
			}

			echo '<div ' . $b . '>';
			echo '<ul class="nav nav-tabs" role="tablist">';

			$a = 'class="active"';

			foreach ($data as $key => $item) {

				echo '<li ' . $a . '><a data-toggle="tab" href="#' . $t_id . $key . '">' . $item['title'] . '</a></li>';

				$a = "";

			}

			echo '</ul>';

			$a = 'class="tab-pane fade in active"';
			echo '<div class="tab-content">';

			foreach ($data as $key => $item) {

				echo '  <div id="' . $t_id . $key . '" ' . $a . '>';

				if (isset($item['page_in_tab']) and $item['page_in_tab'] <> ""){

					if ($item['page_type'] == "special"){
						echo section_content::IncludeSpecial($item['page_in_tab_true']);
					} else {
						echo section_content::IncludePage($item['page_in_tab_true']);
					}

				} else {
					echo $item['content'];
				}

				echo ' </div>';
				$a = 'class="tab-pane fade"';
			}

			echo '</div>';

			echo '</div>';

		}

		$section_data['content'] = ob_get_clean();

		return $section_data;

	}

	function NewSections($links){

		global $addonRelativeCode;

		foreach ($links as $key => $section_type_arr) {
			if ($section_type_arr[0] == 'Tabs_section'){
				$links[$key] = ['Tabs_section', $addonRelativeCode . '/img/tabs.jpg'];
			}

		}

		return $links;
	}


	function DefaultContent($default_content, $type){
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


	function SaveSection($return, $section, $type){
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


	function PageRunScript($cmd){
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
	} /* endof PageRunScript hook */

}

