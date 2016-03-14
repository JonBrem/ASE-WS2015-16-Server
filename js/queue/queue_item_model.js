var QueueItemModel = function(data) {
	var publ = {};

	var viewModel = ViewModel({
		id: data.id,
		assigned_id: data.assigned_id,
		number: data.number,
		preview_img: data.preview_img,
   		title: data.title,
   		url: data.url,
		video_url: data.video_url,
   		status: data.status
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

	var getStatus = function() {
		return data.status;
	};

	viewModel.registerChangeListener(onViewModelChange);
	publ.update = update;
	publ.getViewModel = getViewModel;
	publ.getId = getId;
	publ.remove = remove;
	publ.getStatus = getStatus;
	return publ;
};