/*

Author: a2exfr
http://my-sitelab.com/


*/
$(document).ready(function() {


$("#page_in_tab").autocomplete({ source : tabs_pages_avail });


$(".s_item").sortable({
    update: function( event, ui ) {
		updateOrder();
	}
});



  
    
});

function updateOrder() {
	
	var sortitems = $( ".s_item" ).sortable( "toArray", {attribute: 'data-item'} );
	var group_id = $( ".s_item" ).attr('id');
	console.log(sortitems);
	console.log(group_id);
	var verified = encodeURIComponent(post_nonce);
	var cmd = 'update_order';
	
	
	var form_data = new FormData();
	form_data.append('cmd', cmd);
	form_data.append('sortitems', sortitems);
	form_data.append('verified', verified);
	form_data.append('group_id', group_id);
	
	$.ajax({
		type: 'POST',
		data: form_data,
		success: function (response) {
			 location.reload();	
		}, 
	cache: false,
		contentType: false,
		processData: false
		
	});
	
	
}



