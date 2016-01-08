jQuery(document).ready(function($) {
	init_queue();
});

var _queueItemTemplate;

function init_queue() {
	_queueItemTemplate = _.template($("#queue_item_template").html());

}

function updateQueue() {
	$.ajax({
		url: 'php_scripts/queue_status.php',
		dataType: "json",
		success: onQueueDownload,
		error: onQueueDownloadError
	});
}

function onQueueDownload(e) {
	console.log(e);

	$("#queue_list").empty();

	for(var i = 0; i < e.length; i++) {
		e[i].number = i;
		$("#queue_list").append(_queueItemTemplate({item: e[i]}));
	}

}

function onQueueDownloadError(e) {
	console.log("error", e);
}


function QueueItem() {

}
