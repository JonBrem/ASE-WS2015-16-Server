var queueItemTemplate = undefined; // will be initialized in Queue
var statusTemplate = undefined;

/**
 * View for the Videos that are currently in the queue.
 * Like the other three [Something]ItemView class/functions, this
 * takes data from an AutoUpdateData object and displays it in an underscore template
 * that it will update when the object registered a change.
 */
var QueueItemView = function(_autoUpdateData) {
	var publ = {};

	var $item;

	var $numberEl,
	    $statusEl,
        $imgEl,
        $titleEl,
        $urlEl;


	var viewData = _autoUpdateData.getData();

	$(_autoUpdateData).on("change", onViewModelChange);

	var create = function(appendTo) {
    	$item = $(queueItemTemplate({item: {
    		id: viewData.id,
    		preview_img: viewData.preview_img,
    		number: Number(viewData.number),
    		title: viewData.title,
    		url: viewData.url,
    		status: viewData.status,
            assigned_id: viewData.assigned_id
    	}}));

    	$numberEl = $item.find('.queue_item_number');
    	$statusEl = $item.find('.queue_item_progress');
        $imgEl = $item.find('.thumbnail');
        $titleEl = $item.find('.queue_item_title');
        $urlEl = $item.find('.queue_item_url a');

    	$statusEl.html(statusTemplate({item: {
    		status: viewData.status
    	}}));

    	$item.find('.queue_item_delete').on('click', function(e) {
    		var x = confirm("Möchten Sie das Video " + viewData.title + " wirklich löschen?");

    		if(x) {
    			$.ajax({
    				url: 'php_scripts/api/delete_video.php',
    				type: 'GET',
                    dataType: 'application/json',
    				data: {
    					'id_type' : 'db_id',
    					'id_value' : viewData.id
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
            EditVideoHelper.showForVideo(viewData);
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
        } else if (e.what == "assigned_id") {
            $item.find('.queue_item_assigned_id').text((e.value != null && e.value.length > 0)? ("Zugewiesene ID: " + e.value) : "");
		} else if (e.what == "destroy") {
			destroy();
		}
	};

	var destroy = function() {
		$item.remove();
	};

	_autoUpdateData.registerChangeListener(onViewModelChange);

	publ.create = create;
	publ.destroy = destroy;
	return publ;
};