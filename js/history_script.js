jQuery(document).ready(function($) {
	init_history();
});

var _historyItemTemplate;

function init_history() {
	_historyItemTemplate = _.template($("#history_item_template").html());
	downloadHistory();
}

function downloadHistory() {
	$.ajax({
		url: 'php_scripts/get_items/get_history.php',
		dataType: 'json',
		success: onHistoryDownloaded,
		error: function(e) {console.log("error", e);}
	});
	
}

function onHistoryDownloaded(e) {
	$("#history_list").empty();

	for(var mediaID in e) {
		var item = e[mediaID];
		item.id = mediaID;
		$("#history_list").append(_historyItemTemplate({item: item}));
	}

	$(".history_item_tag").on("click", function(e) {
		$tag = $(e.target);

		$.ajax({
			url: 'php_scripts/accept_decline_tag.php',
			type: 'GET',
			data: {
				"id" : $tag.attr("data-id"),
				"accepted" : $tag.hasClass('success')? 0 : 1
			},
			success: function(e) {
				console.log(e);
				downloadHistory()
			},
			error: function(e) {console.log(e);}
		});
	});
}
