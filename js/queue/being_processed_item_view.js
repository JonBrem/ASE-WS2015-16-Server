var processingTemplate = undefined;
var processStatusTemplate = undefined;

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
