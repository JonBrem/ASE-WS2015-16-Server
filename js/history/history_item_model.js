var HistoryItemModel = function(data) {
	var publ = {};

	var viewModel = ViewModel({
		id: data.id,
		assigned_id: data.assigned_id,
		preview_img: data.preview_img,
		video_url: data.video_url,
   		title: data.title,
   		url: data.url,
   		tags: data.tags
	});


	var onViewModelChange = function(e) {
	};

	var update = function(newData) {
		viewModel.update(newData);
	};

	var getViewModel = function() {
		return viewModel;
	};

	var getId = function() {
		return data.id;
	};

	var remove = function() {
		viewModel.destroy();
		publ = undefined;
	};

	viewModel.registerChangeListener(onViewModelChange);
	publ.update = update;
	publ.getViewModel = getViewModel;
	publ.getId = getId;
	publ.remove = remove;
	return publ;
};