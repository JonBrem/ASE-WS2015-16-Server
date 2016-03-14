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

    	$item.find('.queue_item_delete').on('click', function(e) {
    		var x = confirm("Möchten Sie das Video " + viewModelData.title + " wirklich löschen?");

    		if(x) {
    			$.ajax({
    				url: 'php_scripts/api/delete_video.php',
    				type: 'GET',
    				data: {
    					'id_type' : 'db_id',
    					'id_value' : viewModelData.id
    				},
    				success: function(e) {
    					if(e.status != "ok") {
    						alert(e.message);
    					}
    					Queue.updateQueue();
    				},error: function(e) {console.log(e);}
    			});    			
    		}
    	});

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