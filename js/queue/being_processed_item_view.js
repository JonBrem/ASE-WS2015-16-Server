var processingTemplate = undefined;
var processStatusTemplate = undefined;

/**
 * View for the Videos that is currently being processed.
 * Like the other three [Something]ItemView class/functions, this
 * takes data from an AutoUpdateData object and displays it in an underscore template
 * that it will update when the object registered a change.
 */
var BeingProcessedItemView = function(_autoUpdateData) {
	var publ = {};

	var $item;
	var $statusEl;

	var viewData = _autoUpdateData.getData();

	$(_autoUpdateData).on("change", onDataChange);

	var create = function(appendTo) {
    	$item = $(processingTemplate({item: {
    		id: viewData.id,
    		preview_img: viewData.preview_img,
    		title: viewData.title,
    		url: viewData.url,
    		status: viewData.status,
    		assigned_id: viewData.assigned_id
    	}}));

    	$statusEl = $item.find(".being_processed_item_progress");

    	$statusEl.html(processStatusTemplate({item: {
    		status: viewData.status
    	}}));


    	appendTo.append($item);
	};


	var onDataChange = function(e) {
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

	_autoUpdateData.registerChangeListener(onDataChange);

	publ.create = create;
	publ.destroy = destroy;
	return publ;
};
