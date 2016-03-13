jQuery(document).ready(function($) {
	Queue.init_queue();
});
// if there is time: make this more fancy (class for QueueItems, Singleton/Module for Queue)
var Queue = (function() {

	var publ = {},
	queueSorting = false,

	queueItemModels = [],

	itemInProcess = undefined,

	init_queue = function() {
		queueItemTemplate = _.template($("#queue_item_template").html());
		statusTemplate = _.template($("#status_template").html());
		processingTemplate = _.template($("#processing_template").html());
		processStatusTemplate = _.template($("#process_status_template").html());

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

		$("#queue_control_play").on("click", function(e) {onQueueControlClick(true);});
		$("#queue_control_stop").on("click", function(e) {onQueueControlClick(false);});
	},

	updateQueue = function() {
		$.ajax({
			url: 'php_scripts/get_items/get_queue.php',
			dataType: "json",
			success: onQueueDownload,
			error: onQueueDownloadError
		});

		getQueueRunningStatus();
	},

	getQueueRunningStatus = function() {
		$.ajax({
			url: 'php_scripts/get_config.php',
			dataType: "json",
			data: {
				"which" : "queue_status"
			},
			success: function(e) {
				updateQueueStatusDisplay(e);
			},
			error:function(e) {}
		});
	},

	updateQueueStatusDisplay = function(e) {
		if(e["queue_status"] == "running") {
			$("#queue_control_play").addClass('on');
			$("#queue_control_stop").removeClass('on');
		} else {
			$("#queue_control_play").removeClass('on');
			$("#queue_control_stop").addClass('on');
		}
	},

	onQueueDownload = function(e) {
		updateOrCreateItems(e);

		destroyItemsIfNecessary(e);

		makeQueueListSortable();
	},

	makeQueueListSortable = function() {
		$("#queue_list").sortable({
			axis: "y",
			start: onQueueSortStart,
			stop: onQueueSortStop
		});
	},

	/**
	 *
	 */
	updateOrCreateItems = function(e) {
		var itemBeingProcessed = 0;

		for(var i = 0; i < e.length; i++) {
			if(e[i].status != "downloaded" && e[i].status != "downloading" && e[i].status != "in_queue") {				
				itemBeingProcessed = 1;
				showItemInProcess(e[i]);
			}
		}		

		if(itemBeingProcessed == 0 && itemInProcess != undefined) showThatNoItemIsBeingProcessed();

		for(var i = 0; i < e.length; i++) {
			if(e[i].status != "downloaded" && e[i].status != "downloading" && e[i].status != "in_queue") {				
				for(var j = 0; j < queueItemModels.length; j++) {
					if(queueItemModels[j].getId() == e[i].id) queueItemModels[j].remove();
				}

				continue;
			}

			e[i].number = Number(e[i].position) - itemBeingProcessed;
			var alreadyExisted = false;
			for(var j = 0; j < queueItemModels.length; j++) {
				if(queueItemModels[j].getId() == e[i].id) {
					queueItemModels[j].update(e[i]);
					alreadyExisted = true;
					break;
				}
			}

			if(!alreadyExisted) {
				var model = QueueItemModel(e[i]);
				var view = QueueItemView(model.getViewModel());

				queueItemModels.push(model);
				view.create($("#queue_list"));
			}
		}

	},

	showItemInProcess = function(item) {
		if(itemInProcess == undefined || itemInProcess.getId() != item.id) {
			if (itemInProcess != undefined) {
				itemInProcess.remove();
			} else {
				$("#being_processed_item_wrapper").empty();
			}

			itemInProcess = BeingProcessedItemModel(item);
			itemInProcessView = BeingProcessedItemView(itemInProcess.getViewModel());
			itemInProcessView.create($("#being_processed_item_wrapper"));
		} else {
			itemInProcess.update(item);
		}
	},

	showThatNoItemIsBeingProcessed = function() {
		if(itemInProcess != undefined) {
			itemInProcess.remove();
			itemInProcess = undefined;
		}
		$("#being_processed_item_wrapper").html("Momentan wird kein Bild verarbeitet");
	},

	destroyItemsIfNecessary = function(e) {
		var removedItemIndices = [];
		for(var j = 0; j < queueItemModels.length; j++) {
			var remove = true;
			for(var i = 0; i < e.length; i++) {
				if(e[i].id == queueItemModels[j].getId()) {
					remove = false;
				}
			}

			if(remove) {
				queueItemModels[j].remove();
				removedItemIndices.push(j);
			}
		}
		for(var i = removedItemIndices.length - 1; i >= 0; i--) {
			queueItemModels.splice(removedItemIndices, 1);
		}
	},

	onQueueControlClick = function(on) {
		$.ajax({
			url: 'php_scripts/set_config.php',
			dataType: 'json',
			data: {
				"which" : "queue_status",
				"val" : (on? "running" : "stop")
			},
			success: function(e) {getQueueRunningStatus();},
			error: function(e) {getQueueRunningStatus();}
		});
	},

	onQueueDownloadError = function(e) {
		console.log("error", e);
	},

	onQueueSortStart = function() {
		queueSorting = true;
	},

	onQueueSortStop = function() {
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
	},

	getQueuePositions = function() {
		var positions = [];

		var currentlyBeingProcessed = false;
		if(itemInProcess !== undefined) {
			currentlyBeingProcessed = [itemInProcess.getId(), 1];
			positions.push(currentlyBeingProcessed);
		}

		console.log(currentlyBeingProcessed);

		var $listItems = $(".queue_list_item");
		for(var i = 0; i < $listItems.length; i++) {
			var $listItem = $listItems.eq(i);
			positions.push([
				$listItem.attr("data-item-id"),
				i + ((currentlyBeingProcessed == false)? 1 : 2)
			]);
		}

		return {
			"positions" : JSON.stringify(positions)
		}	
	};

	publ.init_queue = init_queue;


	return publ;
})();

