var queueItemTemplate = undefined; // will be initialized in Queue
var statusTemplate = undefined;

var QueueItemView = function(viewModel) {
	var publ = {};

	var $item;

	var $numberEl,
	    $statusEl,
        $imgEl,
        $titleEl,
        $urlEl;


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
        $imgEl = $item.find('.thumbnail');
        $titleEl = $item.find('.queue_item_title');
        $urlEl = $item.find('.queue_item_url a');

    	$statusEl.html(statusTemplate({item: {
    		status: viewModelData.status
    	}}));

    	$item.find('.queue_item_delete').on('click', function(e) {
    		var x = confirm("Möchten Sie das Video " + viewModelData.title + " wirklich löschen?");

    		if(x) {
    			$.ajax({
    				url: 'php_scripts/api/delete_video.php',
    				type: 'GET',
                    dataType: 'application/json',
    				data: {
    					'id_type' : 'db_id',
    					'id_value' : viewModelData.id
    				},
    				success: function(e) {
    					if(e.status && e.status != "ok") {
    						alert(e.message);
    					}
    					Queue.updateQueue();
    				},error: function(e) {console.log(e);}
    			});    			
    		}
    	});


        $item.find(".queue_item_edit").on("click", function(e) {
            EditVideoHelper.showForVideo(viewModelData);
        });

    	appendTo.append($item);
	};


	var onViewModelChange = function(e) {
		if(e.what == "number") {
			$numberEl.html(e.value);
		} else if(e.what == "status") {
			$statusEl.html(statusTemplate({item: {
				status: e.value
			}}));
        } else if(e.what == "preview_img") {
            $imgEl.attr("src", (e.value != null && e.value.length > 0)? e.value : "no_image_available.png");
        } else if(e.what == "title") {
            $titleEl.text((e.value != null && e.value.length > 0)? e.value : "kein Titel angegeben");
        } else if(e.what == "url") {            
            $urlEl.attr("href", (e.value != null && e.value.length > 0)? e.value : "#");
            $urlEl.text((e.value != null && e.value.length > 0)? "Zur Mediathek" : "kein Link angegeben");
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