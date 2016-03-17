/**
 * Model for the items that caused errors when being processed.
 * Simple data storage class/function, AutoUpdateData wrapper.
 */
 var QueueItemModel = function(data) {
	var publ = {};

	var autoUpdateData = AutoUpdateData({
		id: data.id,
		assigned_id: data.assigned_id,
		number: data.number,
		preview_img: data.preview_img,
   		title: data.title,
   		url: data.url,
		video_url: data.video_url,
   		status: data.status
	});

	var update = function(newData) {
		autoUpdateData.update(newData);
	};

	var getAutoUpdateData = function() {
		return autoUpdateData;
	};

	var getId = function() {
		return data.id;
	};

	var remove = function() {
		autoUpdateData.destroy();
		publ = undefined;
	};

	var getStatus = function() {
		return data.status;
	};

	publ.update = update;
	publ.getAutoUpdateData = getAutoUpdateData;
	publ.getId = getId;
	publ.remove = remove;
	publ.getStatus = getStatus;
	return publ;
};