ErroneousItems = (function() {
	var that = {},

	$errorTitle = null,

	errorItemModels = [],

	init = function() {
		errorTemplate = _.template($("#error_item_template").html());
		errorStatusTemplate = _.template($("#error_item_status_template").html());

		updateErrorList();
		setInterval(function() {
			updateErrorList();
		}, 2000);
	},

	updateErrorList = function() {
		$.ajax({
			url: 'php_scripts/get_items/get_erroneous_items.php',
			'dataType' : 'json',
			type: 'GET',
			success: onErrorListDownload,
			error: function(e) {console.log(e);}
		});
	},

	onErrorListDownload = function(e) {
		updateOrCreateItems(e);
		destroyItemsIfNecessary(e);
	},

	updateOrCreateItems = function(e) {
		for(var i = 0; i < e.length; i++) {
			var alreadyExisted = false;

			for(var j = 0; j < errorItemModels.length; j++) {
				if(errorItemModels[j].getId() == e[i].id) {
					errorItemModels[j].update(e[i]);
					alreadyExisted = true;
					break;
				}
			}

			if(!alreadyExisted) {
				var model = ErrorItemModel(e[i]);

				var view = ErrorItemView(model.getViewModel());

				errorItemModels.push(model);
				view.create($("#error_list"));
			}
		}		

		$("#errors_title_addition").text("(" + e.length + ")");
	},

	destroyItemsIfNecessary = function(e) {
		var removeIndices = [];

		for(var i = 0; i < errorItemModels.length; i++) {
			var idStillExists = false;

			for(var j = 0; j < e.length; j++) {
				if(errorItemModels[i].getId() == e[j].id) {
					idStillExists = true;
					break;
				}
			}

			if(!idStillExists) {
				removeIndices.push(i);
			}
		}

		for(var i = removeIndices.length - 1; i >= 0; i--) {
			errorItemModels[removeIndices[i]].remove();
			errorItemModels.splice(removeIndices[i], 1);
		}
	};

	that.updateErrorList = updateErrorList;
	that.init = init;
	return that;
})();

jQuery(document).ready(function($) {
	ErroneousItems.init();	
});
