var errorTemplate = undefined;
var errorStatusTemplate = undefined;

var ErrorItemView = function(viewModel) {
	var publ = {};

	var $item;
	var $statusEl;

	var viewModelData = viewModel.getData();

	$(viewModel).on("change", onViewModelChange);

	var create = function(appendTo) {
    	$item = $(errorTemplate({item: {
    		id: viewModelData.id,
    		preview_img: viewModelData.preview_img,
    		title: viewModelData.title,
    		url: viewModelData.url,
    		status: viewModelData.status
    	}}));

    	$statusEl = $item.find(".error_item_status");

    	$statusEl.html(errorStatusTemplate({item: {
    		status: viewModelData.status
    	}}));


    	appendTo.append($item);
	};


	var onViewModelChange = function(e) {
		if(e.what == "status") { // nothing else can really change...
			$statusEl.html(errorStatusTemplate({item: {
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
