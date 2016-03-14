var errorTemplate = undefined;
var errorStatusTemplate = undefined;

var ErrorItemView = function(viewModel) {
	var publ = {};

	var $item;
	var $statusEl,
        $imgEl,
        $titleEl,
        $urlEl;

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
        $imgEl = $item.find('.thumbnail');
        $titleEl = $item.find('.error_item_title');
        $urlEl = $item.find('.error_item_url a');

    	$statusEl.html(errorStatusTemplate({item: {
    		status: viewModelData.status
    	}}));

    	$item.find(".error_item_delete").on("click", function(e) {
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
    					ErroneousItems.updateErrorList();
    				},error: function(e) {console.log(e);}
    			});    			
    		}    		
    	});

    	$item.find(".error_item_retry").on("click", function(e) {
    		$.ajax({
    			url: 'php_scripts/api/try_video_again.php',
    			type: 'GET',
    			dataType: 'json',
    			data: {"id_type" : "db_id", "id_value" : viewModelData.id},
    			success: function(e) {
    				if(e.status != "ok") {
    					alert(e.message);
    				}
    				ErroneousItems.updateErrorList();
    				Queue.updateQueue();	
    			},error: function(e) {console.log(e);}
    		});
    	});

        $item.find(".error_item_edit").on("click", function(e) {
            EditVideoHelper.showForVideo(viewModelData);
        });

    	appendTo.append($item);
	};


	var onViewModelChange = function(e) {
		if(e.what == "status") {
			$statusEl.html(errorStatusTemplate({item: {
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
