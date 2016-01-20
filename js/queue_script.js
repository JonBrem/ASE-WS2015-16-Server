jQuery(document).ready(function($) {
	init_queue();
});

var _queueItemTemplate;
var queueSorting = false;

// dots to display in any "currently processing" field (, ., .., or ...)
var currentDots = "";

function init_queue() {
	_queueItemTemplate = _.template($("#queue_item_template").html());

	// call basic function
	updateQueue();

	/* list update */
	setInterval(function() {
		if(!queueSorting) {
			updateQueue();
		}

		// to display the ". . ." in any "being_processed" field.
		currentDots += ".";
		if(currentDots.length > 3) currentDots = "";

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
}

function updateQueue() {
	$.ajax({
		url: 'php_scripts/get_items/get_queue.php',
		dataType: "json",
		success: onQueueDownload,
		error: onQueueDownloadError
	});
}

function onQueueDownload(e) {
	$("#queue_list").empty();

	for(var i = 0; i < e.length; i++) {
		e[i].number = i;
		if(e[i].status == "being_processed") e[i].currentDots = currentDots;
		$("#queue_list").append(_queueItemTemplate({item: e[i]}));
	}

	$("#queue_list").sortable({
		axis: "y",
		start: onQueueSortStart,
		stop: onQueueSortStop,
		items: "li:not(.being_processed)"
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

// if there is time: make this more fancy (class for QueueItems, Singleton/Module for Queue)

