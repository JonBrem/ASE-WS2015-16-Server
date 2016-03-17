jQuery(document).ready(function($) {
	History.initHistory();
});

/**
 * Main routine for the history / finished items part of the web site.
 */
var History = (function() {
	var that = {},
	historyItemModels = [],

	initHistory = function() {
		history_item_template = _.template($("#history_item_template").html());
		tag_template = _.template($("#history_item_tag_template").html());

		updateHistory();
		setInterval(function() {
			updateHistory();
		}, 2000);
	},

	updateHistory = function() {
		$.ajax({
			url: 'php_scripts/get_items/get_history.php',
			dataType: 'json',
			success: onHistoryDownloaded,
			error: function(e) {console.log("error", e);}
		});
	},

	onHistoryDownloaded = function(e) {
		updateOrCreateItems(e);
		destroyItemsIfNecessary(e);
	},

	updateOrCreateItems = function(e) {
		$("#history_title_addition").text("(" + e.length + ")");

		for(var i = 0; i < e.length; i++) {
			var alreadyExisted = false;

			for(var j = 0; j < historyItemModels.length; j++) {
				if(historyItemModels[j].getId() == e[i].id) {
					historyItemModels[j].update(e[i]);
					alreadyExisted = true;
					break;
				}
			}

			if(!alreadyExisted) {
				var model = HistoryItemModel(e[i]);

				var view = HistoryItemView(model.getAutoUpdateData());

				historyItemModels.push(model);
				view.create($("#history_list"));
			}
		}
	},

	destroyItemsIfNecessary = function(e) {
		var removeIndices = [];

		for(var i = 0; i < historyItemModels.length; i++) {
			var idStillExists = false;

			for(var j = 0; j < e.length; j++) {
				if(historyItemModels[i].getId() == e[j].id) {
					idStillExists = true;
					break;
				}
			}

			if(!idStillExists) {
				removeIndices.push(i);
			}
		}

		for(var i = removeIndices.length - 1; i >= 0; i--) {
			historyItemModels[removeIndices[i]].remove();
			historyItemModels.splice(removeIndices[i], 1);
		}
	};

	that.initHistory = initHistory;
	that.updateHistory = updateHistory;
	return that;
})();
