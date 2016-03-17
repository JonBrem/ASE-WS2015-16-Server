var history_item_template = undefined; // will be initialized in History
var tag_template = undefined; // that, too

/**
 * View for the Videos for which processing is finished.
 * Like the other three [Something]ItemView class/functions, this
 * takes data from an AutoUpdateData object and displays it in an underscore template
 * that it will update when the object registered a change.
 */
var HistoryItemView = function(_autoUpdateData) {
	var publ = {};

	var $item;

	var $tagsWrapper,
		$imgEl,
		$titleEl,
		$urlEl;

	var viewData = _autoUpdateData.getData();

	$(_autoUpdateData).on("change", onViewModelChange);

	var create = function(appendTo) {
    	$item = $(history_item_template({item: {
    		id: viewData.id,
    		preview_img: viewData.preview_img,
    		title: viewData.title,
    		url: viewData.url,
    		tags: viewData.tags,
            assigned_id: viewData.assigned_id
    	}}));

    	$tagsWrapper = $item.find('.history_item_tags');
        $imgEl = $item.find('.thumbnail');
        $titleEl = $item.find('.history_item_title');
        $urlEl = $item.find('.history_item_url a');

    	updateTags();

    	$item.find('.history_item_delete').on('click', function(e) {
    		var x = confirm("Möchten Sie das Video " + viewData.title + " wirklich löschen?");

    		if(x) {
    			$.ajax({
    				url: 'php_scripts/api/delete_video.php',
    				type: 'GET',
    				data: {
    					'id_type' : 'db_id',
    					'id_value' : viewData.id
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
    	for(var i = 0; i < viewData.tags.length; i++) {
    		var $newTagItem = $(tag_template({
    			item : viewData.tags[i]
    		}));
    		$tagsWrapper.append($newTagItem);

    		$newTagItem.on("click", onTagClick);
    	}
	};

	var onTagClick = function(e) {
		var $tagElement = $(e.target);

		$.ajax({
			url: 'php_scripts/api/accept_decline_tag.php',
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
		} else if(e.what == "preview_img") {
            $imgEl.attr("src", (e.value != null && e.value.length > 0)? e.value : "no_image_available.png");
        } else if(e.what == "title") {
            $titleEl.text((e.value != null && e.value.length > 0)? e.value : "kein Titel angegeben");
        } else if(e.what == "url") {            
            $urlEl.attr("href", (e.value != null && e.value.length > 0)? e.value : "#");
            $urlEl.text((e.value != null && e.value.length > 0)? "Zur Mediathek" : "kein Link angegeben");
        } else if (e.what == "assigned_id") {
            $item.find('.history_item_assigned_id').text((e.value != null && e.value.length > 0)? ("Zugewiesene ID: " + e.value) : "");
        } else if(e.what == "destroy") {
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