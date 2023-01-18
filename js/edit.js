function gp_init_inline_edit(area_id, section_object) {

	loaded()
	gp_editing.editor_tools()
	var edit_div = gp_editing.get_edit_area(area_id)
	var cache_value = ''

	gp_editor = {
		save_path: gp_editing.get_path(area_id),

		destroy: function () {
		},

		checkDirty: function () {

			var curr_val = gp_editor.gp_saveData()
			if (curr_val != cache_value) {
				return true
			}
			return false

		},

		resetDirty: function () {
			cache_value = gp_editor.gp_saveData()
		},

		gp_saveData: function () {

			var options_my = $('#gp_my_options').find('input,select').serialize()
			return '&' + options_my
		},
		intervalSpeed: function () {
		},

		updateElement: function () {
		},

		updatesecttabs: function () {
		},
	}// gpeditor --end


	gp_editor.updatesecttabs = function () {

		var href = jPrep(window.location.href) + '&cmd=refresh_section_tabs' + '&my_value=' + gp_editor.gp_saveData() + '&tabs_id=' + section_object.tabs_id
		$.getJSON(href, ajaxResponse)
		// console.log(gp_editor.gp_saveData());
	}


	$gp.response.refresh_tabsFn = function (arg) {

		if (typeof arg.CONTENT.content === 'undefined') {
			var div_data = arg.CONTENT
		} else {
			var div_data = arg.CONTENT.content
		}

		edit_div.html(div_data)

	}


	if (section_object.index == 0) {

		var ind = '/index.php'

	} else {

		var ind = ''

	}


	var option_area = $('<div id="gp_my_options"/>').prependTo('#ckeditor_controls')

	var option_messages = $(
		'<div id="option_message">' +

		' <div id="tab_sel"><p>Select tab group to show</p><select class="gpselect" name="tab_sel">' +
		'<option value="0">Demo</option>' +
		'</select></div>' +


		' <div id="app"><p>Appearance</p><select class="app gpselect" name="app">' +
		'<option value="0">Tabs</option>' +
		'<option value="1">Accordion</option>' +
		'<option value="2">Tabs left</option>' +
		'<option value="3">Tabs right</option>' +
		'</select></div>' +
		' <div id="panel_class"><p>Accordion color:</p><select class=" gpselect" name="panel_class">' +
		'<option value="panel-default">Default</option>' +
		'<option value="panel-primary">Primary</option>' +
		'<option value="panel-success">Success</option>' +
		'<option value="panel-info">Info</option>' +
		'<option value="panel-warning">Warning</option>' +
		'<option value="panel-danger">Danger</option>' +
		'<option value="panel-custom">Custom</option>' +
		'</select></div>' +
		' <div id="opened"><p>Opened first item?</p><select class=" gpselect" name="opened">' +
		'<option value="yes">Yes</option>' +
		'<option value="no">No</option>' +
		'</select></div>' +


		'<br><a class="butt gpbutton" href="' + ind + '/Admin_Tabs">Add Group</a><br><br>' +

		'</div>',
	).appendTo(option_area)


	$.each(tab_groups, function (i, item) {

		$('#tab_sel select ').append('<option value=' + item.id + '>' + item.title + '</option>')

	})

	$('#gp_my_options').find('#tab_sel select').val(section_object.tab_sel)
	$('#gp_my_options').find('#app select').val(section_object.appearance)
	$('#gp_my_options').find('#panel_class select').val(section_object.panel_class)
	$('#gp_my_options').find('#opened select').val(section_object.opened)


	if (section_object.tab_sel != 0) {
		$('<a id="test" class="butt gpbutton" href="' + ind + '/Admin_Tabs?cmd=edit_group_items&group_id=' + section_object.tab_sel + '">Edit Group Items</a><br><br>').appendTo(option_area)
	}


	// options show/hide
	if (section_object.appearance == 1) {
		$('#gp_my_options').find('#panel_class').show()
		$('#gp_my_options').find('#opened').show()
	} else {
		$('#gp_my_options').find('#panel_class').hide()
	}

	$('#gp_my_options').find('#app select').change(function () {
		var ids = $(this).val()
		if (ids == 1) {
			$('#gp_my_options').find('#panel_class').show()
			$('#gp_my_options').find('#opened').show()
		} else {
			$('#gp_my_options').find('#panel_class').hide()
			$('#gp_my_options').find('#opened').hide()
		}

	})


	$('#gp_my_options').find('select').change(function () {

		gp_editor.updatesecttabs()
	})


}




