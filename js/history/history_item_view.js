var history_item_template = undefined; // will be initialized in History
var tag_template = undefined; // that, too

var HistoryItemView = function(viewModel) {
	var publ = {};

	var $item;

	var $tagsWrapper;

	var viewModelData = viewModel.getData();

	$(viewModel).on("change", onViewModelChange);

	var create = function(appendTo) {
    	$item = $(history_item_template({item: {
    		id: viewModelData.id,
    		preview_img: viewModelData.preview_img,
    		title: viewModelData.title,
    		url: viewModelData.url,
    		tags: viewModelData.tags
    	}}));

    	$tagsWrapper = $item.find('.history_item_tags');
    	updateTags();

    	$item.find('.history_item_delete').on('click', function(e) {
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
    					History.updateHistory();
    				},error: function(e) {console.log(e);}
    			});    			
    		}
    	});

    	appendTo.append($item);
	};

	var updateTags = function() {
    	$tagsWrapper.empty();
    	for(var i = 0; i < viewModelData.tags.length; i++) {
    		var $newTagItem = $(tag_template({
    			item : viewModelData.tags[i]
    		}));
    		$tagsWrapper.append($newTagItem);

    		$newTagItem.on("click", onTagClick);
    	}
	};

	var onTagClick = function(e) {
		var $tagElement = $(e.target);

		$.ajax({
			url: 'php_scripts/accept_decline_tag.php',
			type: 'GET',
			data: {
				"id" : $tagElement.attr("data-id"),
				"accepted" : $tagElement.hasClass('success')? 0: 1
			},
			success: function(e) {
				History.updateHistory();
			},
			error: function(e) {console.log(e);}
		});
		
	};	

	var onViewModelChange = function(e) {
		if(e.what == "tags") {
			updateTags();
		} else if(e.what == "destroy") {
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