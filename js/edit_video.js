/**
 * This module handles everything concerning the modal where you can edit videos.
 * Very similar to the part where videos can be added; this "does more" insofar as it inserts the video data
 * into the input elements before the modal is shown.
 */
var EditVideoHelper = (function() {
	var that = {},
	$editVideoModal = undefined,

	currentVideo = {},

	init = function() {
		$editVideoModal = $("#edit_modal");

		$("#save_edit_video_button").on("click", updateVideo);
		$("#cancel_edit_video_button").on("click", function(e) {
			$("#edit_modal").foundation('close');
		});
	},

	updateVideo = function(e) {
		var $videoFileUrlInput = $("#edit_modal_input_video_file_url");
		if($videoFileUrlInput.val().length == 0) {
			alert("Die Video-Datei muss angegeben werden!");
			return;
		}

		$.ajax({
			url: 'php_scripts/api/update_item.php',
			type: 'GET',
			datatype: 'json',
			data: {
				'id_type' : 'db_id',
				'id_value' : currentVideo.id,
				'video_file_url' : $videoFileUrlInput.val(),
				'video_id' : $('#edit_modal_input_assigned_id').val(),
				'title' : encodeURIComponent($('#edit_modal_input_title').val()),
				'url' : $('#edit_modal_input_video_url').val(),
				'preview_image' : $('#edit_modal_input_preview_image').val()
			},
			success: function(e) {
				if(e.status && e.status!="ok") {
					alert(e.message);
				}
				$("#edit_modal").foundation('close');

				$("#edit_modal_input_video_file_url").val("");
				$("#edit_modal_input_assigned_id").val("");
				$("#edit_modal_input_title").val("");
				$("#edit_modal_input_video_url").val("");
				$("#edit_modal_input_preview_image").val("");
			},
			error: function(e) {alert(e);}
		});
	},

	showForVideo = function(videoData) {
		currentVideo = {
			"id" : videoData.id,
			"video_file_url" : videoData.video_url,
			"video_id" : videoData.assigned_id,
			"title" : videoData.title,
			"url" : videoData.url,
			"preview_image" : videoData.preview_img 
		};

		$("#edit_modal_input_video_file_url").val(currentVideo.video_file_url);
		$("#edit_modal_input_assigned_id").val(currentVideo.video_id);
		$("#edit_modal_input_title").val(currentVideo.title);
		$("#edit_modal_input_video_url").val(currentVideo.url);
		$("#edit_modal_input_preview_image").val(currentVideo.preview_image);

		$editVideoModal.foundation('open', {});	
	};

	that.showForVideo = showForVideo;
	that.init = init;
	return that;
})();

EditVideoHelper.init();