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

		for(var i = 0; i < e.length; i++) {
			if(e[i].status != "downloaded" && e[i].status != "downloading" && e[i].status != "in_queue") {				
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

		if(itemBeingProcessed == 0) {
			showThatNoItemIsBeingProcessed();
		}
	},

	showItemInProcess = function(item) {
		if(itemInProcess == undefined) {
			itemInProcess = BeingProcessedItemModel(item);
			itemInProcessView = BeingProcessedItemView(itemInProcess.getViewModel());
			itemInProcessView.create($("#being_processed_item_wrapper"));
		} else {
			itemInProcess.update(item);
		}
	},

	showThatNoItemIsBeingProcessed = function() {
		if(itemInProcess != undefined) {
			itemInProcess.destroy();
		}
		$("#being_processed_item_wrapper").html("Momentan wird kein Bild verarbeitet");
	},

	destroyItemsIfNecessary = function(e) {
		for(var j = 0; j < queueItemModels.length; j++) {
			var remove = true;
			for(var i = 0; i < e.length; i++) {
				if(e[i].id == queueItemModels[j].getId()) {
					remove = false;
				}
			}

			if(remove) {
				queueItemModels[j].remove();
			}
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

var QueueItemModel = function(data) {
	var publ = {};

	var viewModel = ViewModel({
		id: data.id,
		number: data.number,
		preview_img: data.preview_img,
   		title: data.title,
   		url: data.url,
   		status: data.status
	});


	var onViewModelChange = function(e) {
	};

	var update = function(newData) {
		viewModel.update(newData);
	};

	var getViewModel = function() {
		return viewModel;
	};

	var getId = function() {
		return data.id;
	};

	var remove = function() {
		//
	};

	var getStatus = function() {
		return data.status;
	};

	viewModel.registerChangeListener(onViewModelChange);
	publ.update = update;
	publ.getViewModel = getViewModel;
	publ.getId = getId;
	publ.remove = remove;
	publ.getStatus = getStatus;
	return publ;
};

var ViewModel = function(modelData) {
	var publ = {};
	var changeListeners = [];

	var data = {};
	for(var key in modelData) {
		data[key] = modelData[key];
	}

	var update = function(newData) {
		for(var key in newData) {
			if(key in data) {
				if(data[key] != newData[key]) {
					data[key] = newData[key];
					change(key, data[key]);
				}
			} else {
				data[key] = newData[key];
				change(key, data[key]);
			}
		}
	};

	var getData = function() {
		return data;
	};

	var registerChangeListener = function(onChangeListener) {
		changeListeners.push(onChangeListener);
	};

	var change = function(what, value) {
		for(var i = 0; i < changeListeners.length; i++) {
			changeListeners[i]({"what": what, "value": value});
		}
	};

	publ.registerChangeListener = registerChangeListener;
	publ.getData = getData;
	publ.update = update;
	return publ;
};


var queueItemTemplate = undefined; // will be initialized in Queue
var statusTemplate = undefined;

var QueueItemView = function(viewModel) {
	var publ = {};

	var $item;

	var $numberEl;
	var $statusEl;

	var viewModelData = viewModel.getData();

	$(viewModel).on("change", onViewModelChange);

	var create = function(appendTo) {
    	$item = $(queueItemTemplate({item: {
    		id: viewModelData.id,
    		preview_img: viewModelData.preview_img,
    		number: Number(viewModelData.number),
    		title: viewModelData.title,
    		url: viewModelData.url,
    		status: viewModelData.status
    	}}));

    	$numberEl = $item.find('.queue_item_number');
    	$statusEl = $item.find('.queue_item_progress');

    	$statusEl.html(statusTemplate({item: {
    		status: viewModelData.status
    	}}));


    	appendTo.append($item);
	};


	var onViewModelChange = function(e) {
		if(e.what == "number" || e.what == "status") { // nothing else can really change...
			if(e.what == "number") {
				$numberEl.html(e.value);
			} else { // status
				$statusEl.html(statusTemplate({item: {
					status: e.value
				}}));
			}
		}
	};

	var destroy = function() {
		$item.remove();
	};

	viewModel.registerChangeListener(onViewModelChange);

	publ.create = create;
	publ.destroy = destroy;
	return publ;
};

var processingTemplate = undefined;
var processStatusTemplate = undefined;

var BeingProcessedItemModel = function(data) {
	var publ = {};

	var viewModel = ViewModel({
		id: data.id,
		preview_img: data.preview_img,
   		title: data.title,
   		url: data.url,
   		status: data.status
	});

	var onViewModelChange = function(e) {
	};

	var update = function(newData) {
		viewModel.update(newData);
	};

	var getViewModel = function() {
		return viewModel;
	};

	var getId = function() {
		return data.id;
	};

	var remove = function() {
		//
	};

	viewModel.registerChangeListener(onViewModelChange);
	publ.update = update;
	publ.getViewModel = getViewModel;
	publ.getId = getId;
	publ.remove = remove;
	return publ;
};

var BeingProcessedItemView = function(viewModel) {
	var publ = {};

	var $item;
	var $statusEl;

	var viewModelData = viewModel.getData();

	$(viewModel).on("change", onViewModelChange);

	var create = function(appendTo) {
    	$item = $(processingTemplate({item: {
    		id: viewModelData.id,
    		preview_img: viewModelData.preview_img,
    		title: viewModelData.title,
    		url: viewModelData.url,
    		status: viewModelData.status
    	}}));

    	$statusEl = $item.find(".being_processed_item_progress");

    	$statusEl.html(processStatusTemplate({item: {
    		status: viewModelData.status
    	}}));


    	appendTo.append($item);
	};


	var onViewModelChange = function(e) {
		if(e.what == "status") { // nothing else can really change...
			$statusEl.html(processStatusTemplate({item: {
				status: e.value
			}}));
		}
	};

	var destroy = function() {
		$item.remove();
	};

	viewModel.registerChangeListener(onViewModelChange);

	publ.create = create;
	publ.destroy = destroy;
	return publ;
};
