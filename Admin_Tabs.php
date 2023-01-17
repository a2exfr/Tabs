<?php
defined('is_running') or die('Not an entry point...');
includeFile('tool/editing.php');

class Admin_Tabs{

	function __construct(){
		global $page, $addonPathData, $addonRelativeCode, $langmessage, $gp_index;

		$pageIndexJS = 'var tabs_pages_avail = [';
		$i = 0;
		foreach ($gp_index as $key => $value) {
			$i++;
			$pageIndexJS .= '"' . common::GetLabelIndex($value) . '"' . ($i == count($gp_index) ? '' : ', ');
		}
		$pageIndexJS .= '];';
		$page->head_script .= "\n" . $pageIndexJS . "\n";

		$page->css_user[] = $addonRelativeCode . '/css/admin_tabs.css';

		$page->head_js[] = $addonRelativeCode . '/js/tab_admin.js ';

		$this->PluginUrl = "Admin_Tabs";

		$this->get_lang_ext();

		$this->load_data_groups();

		if (!isset($_REQUEST['group_id']) and count($this->data_group) > 0){

			$group_id = max(array_keys($this->data_group));

		} elseif (isset($_REQUEST['group_id'])) {

			$group_id = $_REQUEST['group_id'];

		} else {

			$group_id = 0;
		}

		$this->load_data($group_id);

		if (!isset($_REQUEST['item_id']) and count($this->data) > 0){

			$id = max(array_keys($this->data));

		} elseif (isset($_REQUEST['item_id'])) {

			$id = $_REQUEST['item_id'];

		} else {

			$id = 0;
		}

		$cmd = common::GetCommand();

		switch ($cmd) {


			case 'new_item':
				$id = $id + 1;
				$this->edit($id, $group_id);
				break;

			case 'save_t':
				$this->Check_PageType($id);
				$this->SaveData($group_id);
				header('Refresh:0; url=' . common::GetUrl($this->PluginUrl) . '?cmd=edit_group_items&group_id=' . $group_id . '');

				break;

			case 'edit_item':
				$this->edit($id, $group_id);
				break;

			case 'del_item':
				$this->del_item($id, $group_id);
				header('Refresh:0; url=' . common::GetUrl($this->PluginUrl) . '');
				break;

			case 'new_group':
				$group_id = $group_id + 1;
				$this->new_group($group_id);
				break;

			case 'save_group':
				$this->SaveDataGroup();
				header('Refresh:0; url=' . common::GetUrl($this->PluginUrl) . '');
				break;

			case 'edit_group':
				$this->new_group($group_id);
				break;

			case 'edit_group_items':
				$this->show_items($group_id);
				break;

			case 'del_group':
				$this->del_group($group_id);
				header('Refresh:0; url=' . common::GetUrl($this->PluginUrl) . '');
				break;

			case 'update_order':
				$this->update_order();
				$this->SaveData($group_id);

				break;

			default:
				$this->show_groups();

		}

	}


	function update_order(){
		$group_id = $_REQUEST['group_id'];
		$ordering = explode(",", $_REQUEST['sortitems']);
		$sorted_items = array_replace(array_flip($ordering), $this->data);
		$this->data = $sorted_items;
		array_unshift($this->data, "");
		unset($this->data[0]);
		return;

	}


	function show_groups(){

		echo common::Link($this->PluginUrl, $this->lang_ext['New Group of Tabs'], 'cmd=new_group', 'class="butt" name="gpabox" ');
		echo "<br>";
		echo "<br>";
		echo '	<table class="bordered full_width striped">
				<thead>
				  <tr>
					<th>Group of Tabs</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				  </tr>
				</thead>
				<tbody>';

		foreach ($this->data_group as $key => $item) {
			echo ' <tr> ';
			echo ' <td> ' . $item["title"] . '</td>';
			echo ' <td> <a class="butt" href = ' . common::GetUrl($this->PluginUrl) . '?cmd=edit_group_items&group_id=' . $key . ' >' . $this->lang_ext['Edit items'] . '</a> </td>';
			echo ' <td>';
			echo ' <td> <a class="butt" name="gpabox" href = ' . common::GetUrl($this->PluginUrl) . '?cmd=edit_group&group_id=' . $key . ' >' . $this->lang_ext['Edit name'] . '</a> </td>';
			echo ' <td>';
			echo '<a class="butt" href = ' . common::GetUrl($this->PluginUrl) . '?cmd=del_group&group_id=' . $key . ' >' . $this->lang_ext['Delete group'] . ' </a>';
			echo ' </tr>';
		}
		echo '    </tbody>
		  </table> ';

	}


	function show_items($group_id){
		global $langmessage;

		echo '<a class="butt" href = ' . common::GetUrl($this->PluginUrl) . ' >' . $langmessage['back'] . '</a> ';
		echo '<a class="butt" href = ' . common::GetUrl($this->PluginUrl) . '?cmd=new_item&group_id=' . $group_id . ' >' . $this->lang_ext['New Item'] . ' </a> ';
		echo "<br>";
		echo "<br>";
		echo '	<table class="bordered full_width striped">
			<thead>
			  <tr>
				<th>Tabs</th>
				<th></th>
				<th></th>
			  </tr>
			</thead>
			<tbody class="s_item" id="' . $group_id . '">';

		foreach ($this->data as $key => $item) {
			echo ' <tr data-item="' . $key . '"> ';
			echo '<td>' . $item["title"] . '</td> ';
			echo ' <td> <a class="butt" href = ' . common::GetUrl($this->PluginUrl) . '?cmd=edit_item&item_id=' . $key . '&group_id=' . $group_id . ' >' . $langmessage['edit'] . '  </a> </td>';
			echo ' <td>';
			echo '<a class="butt" href = ' . common::GetUrl($this->PluginUrl) . '?cmd=del_item&item_id=' . $key . '&group_id=' . $group_id . ' >' . $langmessage['delete'] . '   </a>';
			echo ' </tr>';
		}
		echo '    </tbody>
	  </table> ';

	}


	function edit($id, $group_id){
		global $addonRelativeCode, $langmessage;

		echo '<form action=" " method="post">';

		echo '<div id="my"></div>';
		echo '<h3>Title:</h3>';

		if (array_key_exists($id, $this->data)){
			echo '<input class="gpinput" style="width:200px" type="text" name="item[title]" value="' . $this->data[$id]['title'] . '" />';
		} else {
			echo '<input class="gpinput" style="width:200px" type="text" name="item[title]" value="" />';
		}

		echo '<h3>Content:</h3>';
		echo '<table style="width:100%">';

		echo '<tr><td colspan="2">';
		echo '<div id="#res">';
		if (array_key_exists($id, $this->data)){
			gp_edit::UseCK($this->data[$id]['content'], 'item[content]');
		} else {
			gp_edit::UseCK('', 'item[content]');
		}

		echo '</div>';
		echo '</td></tr>';

		echo '</table>';

		echo '<h3>' . $this->lang_ext['Display page in tab'] . ':</h3>';

		if (array_key_exists($id, $this->data)){

			echo '<input id="page_in_tab" class="gpinput" style="width:200px" type="text" name="item[page_in_tab]" value="' . $this->data[$id]['page_in_tab'] . '" />';
			echo '<p>' . $this->lang_ext['Info'] . '</p>';
		} else {

			echo '<input id="page_in_tab" class="gpinput" style="width:200px" type="text" name="item[page_in_tab]" value="" />';
			echo '<p>' . $this->lang_ext['Info'] . '</p>';
		}

		echo '<p>';
		echo '<input type="hidden" name="group_id" value="' . $group_id . '" />';
		echo '<input type="hidden" name="cmd" value="save_t" />';
		echo '<input type="hidden" name="item_id" value="' . $id . '" />';

		echo '<input type="submit" value="' . $langmessage['save_changes'] . '" class="gpsubmit"/>';
		echo '<input type="button" onClick="location.href=\'' . common::GetUrl($this->PluginUrl) . '\'" name="cmd" ';
		echo 'value="' . $langmessage['cancel'] . '" class="gpcancel" />';
		echo '</p>';
		echo '</form>';
	}


	function new_group($id){
		global $addonRelativeCode, $langmessage;
		echo '<div class="inline_box">';
		echo '<form action=" " method="post">';

		echo '<h3>' . $this->lang_ext['Name of Group'] . '</h3>';

		if (array_key_exists($id, $this->data_group)){
			echo '<input class="gpinput" style="width:200px" type="text" name="item[title]" value="' . $this->data_group[$id]['title'] . '" />';
		} else {
			echo '<input class="gpinput" style="width:200px" type="text" name="item[title]" value="" />';
		}

		echo '<p>';
		echo '<input type="hidden" name="cmd" value="save_group" />';
		echo '<input type="hidden" name="group_id" value="' . $id . '" />';

		echo '<input type="submit" value="' . $langmessage['save_changes'] . '" class="gpsubmit"/>';
		echo '<input type="button" onClick="location.href=\'' . common::GetUrl($this->PluginUrl) . '\'" name="cmd" ';
		echo 'value="' . $langmessage['cancel'] . '" class="gpcancel" />';
		echo '</p>';
		echo '</form>';

		echo '</div>';

	}


	function Check_PageType($id){

		global $gp_titles, $gp_index;

		$check = $_POST['item']['page_in_tab'];

		if (!isset($check)){
			return;
		}

		$index = $this->array_find_deep($gp_titles, $check);

		if (!empty($index)){
			$page_index = $index[0];
			$page_type = $gp_titles[$page_index]['type'];

		} else {
			$page_type = "special";
		}

		$_POST['item']['page_type'] = $page_type;

		if (!empty($index) and $page_type == "special"){

			$_POST['item']['page_in_tab_true'] = $index[0];

		} else {

			//$_POST['item']['page_in_tab_true'] = $check;
			$_POST['item']['page_in_tab_true'] = array_search($index[0], $gp_index);
		}

		return;

	}


	function array_find_deep($array, $search, $keys = []){
		foreach ($array as $key => $value) {
			if (is_array($value)){
				$sub = $this->array_find_deep($value, $search, array_merge($keys, [
					$key,
				]));
				if (count($sub)){
					return $sub;
				}
			} elseif ($value === $search) {
				return array_merge($keys, [
					$key,
				]);
			}
		}

		return [];
	}

	function del_group($group_id){
		global $addonPathData;

		if ($this->data_group){
			unset($this->data_group[$group_id]);
		}

		$this->SaveDataGroup($group_id);

		$fileoftabs = $addonPathData . '/' . $group_id . '.php';

		unlink($fileoftabs);

	}


	function del_item($id, $group_id){


		if ($this->data){
			unset($this->data[$id]);

		}

		$this->saveData($group_id);

	}


	function SaveData($group_id){

		global $addonPathData;
		global $langmessage;

		$saveMsg = "";

		$configFile = $addonPathData . '/' . $group_id . '.php';

		$data = $this->data;

		if ($_POST['item']){
			$data_temp [$_POST['item_id']] = $_POST['item'];
		}

		if ($data_temp){

			if ($this->data){
				$data = $data_temp + $this->data;
			} else {
				$data = $data_temp;
			}

		}

		array_filter($data);
		ksort($data);

		if (!gpFiles::SaveData($configFile, 'data', $data)){
			message($langmessage['OOPS'] . $saveMsg);
			return false;
		}

		message($langmessage['SAVED'] . $saveMsg);
		return true;

	}


	function SaveDataGroup(){

		global $addonPathData;
		global $langmessage;

		$saveMsg = "";

		$configFile = $addonPathData . '/data_groups.php';

		$data = $this->data_group;

		if ($_POST['group_id']){
			$data_temp [$_POST['group_id']] = $_POST['item'];
		}

		if ($data_temp){

			if ($this->data_group){
				$data = $data_temp + $this->data_group;
			} else {
				$data = $data_temp;
			}

		}

		array_filter($data);
		ksort($data);

		if (!gpFiles::SaveData($configFile, 'data', $data)){
			message($langmessage['OOPS'] . $saveMsg);
			return false;
		}

		message($langmessage['SAVED'] . $saveMsg);
		return true;

	}


	function load_data($group_id){
		global $addonPathData;
		global $langmessage;

		$saveMsg = "";

		$configFile = $addonPathData . '/' . $group_id . '.php';

		if (file_exists($configFile)){
			include_once $configFile;
		} else {
			$data = [];
		}

		$this->data = $data;

	}

	function load_data_groups(){
		global $addonPathData;
		global $langmessage;

		$configFile = $addonPathData . '/data_groups.php';

		if (file_exists($configFile)){
			include_once $configFile;
		} else {
			$data = [];
		}

		$this->data_group = $data;

	}


	function get_lang_ext(){
		global $config;

		$langfile = '/languages/' . $config['language'] . '.php';

		if (file_exists(dirname(__FILE__) . $langfile)){
			include dirname(__FILE__) . $langfile;
		} else {
			$langfile = '/languages/en.php';
			include dirname(__FILE__) . $langfile;

		}

		$this->lang_ext = $lang_ext;

	}


}

?>