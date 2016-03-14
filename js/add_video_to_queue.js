var AddVideoHelper = (function() {
	var that = {},
	$addVideoModal = undefined,

	init = function() {
		$addVideoModal = $("#add_modal");

		$("#save_add_video_button").on("click", saveVideo);
		$("#cancel_add_video_button").on("click", function(e) {
			$("#add_modal").foundation('close');
		});
	},

	saveVideo = function(e) {
		var $videoFileUrlInput = $("#add_modal_input_video_file_url");
		if($videoFileUrlInput.val().length == 0) {
			alert("Die Video-Datei muss angegeben werden!");
			return;
		}

		$.ajax({
			url: 'php_scripts/api/add_to_queue.php',
			type: 'GET',
			data: {
				'video_file_url' : $videoFileUrlInput.val(),
				'video_id' : $('#add_modal_input_assigned_id').val(),
				'title' : $('#add_modal_input_title').val(),
				'url' : $('#add_modal_input_video_url').val(),
				'preview_image' : $('#add_modal_input_preview_image').val()
			},
			success: function(e) {
				$("#add_modal").foundation('close');

				$("#add_modal_input_video_file_url").val("");
				$("#add_modal_input_assigned_id").val("");
				$("#add_modal_input_title").val("");
				$("#add_modal_input_video_url").val("");
				$("#add_modal_input_preview_image").val("");
			},
			error: function(e) {alert(e);}
		});
	};

	that.init = init;
	return that;
})();

AddVideoHelper.init();