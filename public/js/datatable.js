var datatable_page = 1;
var datatable_formData = "";
var datatable_ajaxUrl = "";
var datatable_formElement = "";
var datatable_loading = false;

$(document).ready(function() {
	$('body').append("<div class='corner-loading-indicator'><i class='fa fa-spin fa-refresh'></i></div>");
	
	searchSubmit();

	datatable_formElement.submit(function(e) {
		e.preventDefault();
		searchSubmit();
	});
});

function searchSubmit() {
	datatable_page = 1;
	datatable_formData = datatable_formElement.serialize();
	loadTable();
}

function jumpPage(p) {
	datatable_page = p;
	loadTable();
}

function set_loading(state) {
	datatable_loading = state;
	if(state) {
		$('div.corner-loading-indicator').css('visibility', 'visible');
	} else {
		$('div.corner-loading-indicator').css('visibility', 'hidden');
	}
}

function loadTable() {
	if(!datatable_loading) {
		set_loading(true);
		
		$.ajax({
			method: datatable_formElement.attr('method'),
			data: (datatable_formData + "&page=" + datatable_page),
			url: datatable_ajaxUrl
		})
		.done(function(result) {
			$('table#datatable tbody').html(result['data']);
			$('ul.pagination').html(result['pagination']);
			set_loading(false);
		})
		.fail(function(result) {
			$('table#datatable tbody').html("<tr><td colspan='99' style='padding:1em;text-align:center'>Gagal. Coba <a href='javascript:location.reload()'>refresh</a> halaman.</td></tr>")
			set_loading(false);
		});
	}
}