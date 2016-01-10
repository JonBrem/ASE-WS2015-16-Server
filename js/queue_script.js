jQuery(document).ready(function($) {
	init_queue();
});

var _queueItemTemplate;
var queueSorting = false;

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
	$("#queue_list").empty();

	for(var i = 0; i < e.length; i++) {
		e[i].number = i;
		$("#queue_list").append(_queueItemTemplate({item: e[i]}));
	}

	$("#queue_list").sortable({
		axis: "y",
		start: onQueueSortStart,
		stop: onQueueSortStop
	});
}

function onQueueDownloadError(e) {
	console.log("error", e);
}

function onQueueSortStart() {
	queueSorting = true;
}

function onQueueSortStop() {
	queueSorting = false;
	$.ajax({
		url: 'php_scripts/change_queue_positions.php',
		type: "GET",
		dataType: 'json',
		data: getQueuePositions(),
		success: function(e) {
			updateQueue();
		}, error: function(e) {
			console.log("error", e);
		}
	});
}

function getQueuePositions() {
	var positions = [];
	var $listItems = $(".queue_list_item");
	for(var i = 0; i < $listItems.length; i++) {
		var $listItem = $listItems.eq(i);
		positions.push([
			$listItem.attr("data-item-id"),
			i + 1
		]);
	}

	return {
		"positions" : JSON.stringify(positions)
	}	
}

function QueueItem() {

}

// call basic function
updateQueue();

/* list update */
setInterval(function() {
	if(!queueSorting) {
		updateQueue();
	}
}, 2000);


/* simple solution for fancy downloading style thingy */
setInterval(function(){
	$(".queue_item_downloading .fi-download")
		.animate({
			color: "#909033"
		}, 2000)
		.animate({
			color: "#333333"
		}, 2000);
}, 4000);
