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
		} else if (e.what == "destroy") {
			destroy();
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