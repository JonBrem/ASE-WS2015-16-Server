<?php

	/**
	 * <small>The function does nothing, this is just for the documentation.</small>
	 * The status codes define what happens and the order in which that happens.
	 * The main table has some status codes (crawled, in_queue, history and various error codes)
	 * and the queue table has many more status.
	 * In both tables, the status describes what is currently happening to the media item.
	 * Status codes in the queue table are used to communicate the items' status from within the subroutines.
	 */
	function status_codes() {}

	// the item was crawled and will be added to the queue if there are fewer than 10 items in there.
	define("STATUS_CRAWLED", "crawled"); // FOR "media" TABLE

	// item is in queue, not being downloaded or anything.
	define("STATUS_IN_QUEUE", "in_queue"); // FOR "media" TABLE

// ______________
// EVERYTHING UNTIL THE NEXT "Line" IS ONLY FOR THE "queue" TABLE!! THE ITME IN THE MEDIA TABLE MUST KEEP THE STATUS "in_queue" !!!


	// video is currently being downloaded to the server
	define("STATUS_DOWNLOADING", "downloading"); // FOR "queue" TABLE

	// video has been downloaded to the server
	define("STATUS_DOWNLOADED", "downloaded"); // FOR "queue" TABLE

	// php is cutting up the video. follows after "STATUS_DOWNLOADED" if the item is the fist in the queue.
	define("STATUS_SEGMENTING_VIDEO", "segmenting_video"); // FOR "queue" TABLE

	// php is done cutting up the video. follows after "STATUS_SEGMENTING_VIDEO".
	define("STATUS_FINISHED_SEGMENTING_VIDEO", "finished_segmenting_video"); // FOR "queue" TABLE

	// c++ executable is currently running with that video. follows after "STATUS_FINISHED_SEGMENTING_VIDEO"
	define("STATUS_BEING_PROCESSED", "being_processed"); // FOR "queue" TABLE

	// c++ executable is done with the video & has written everything to the file.
	// follows after "STATUS_BEING_PROCESSED"
	define("STATUS_FINISHED_PROCESSING", "finished_processing"); // FOR "queue" TABLE

	// c++ executable is done with the file, but JAVA still needs to select tags from the recognized words.
	// follows after STATUS_FINISHED_PROCESSING
	define("STATUS_EVALUATING_WORDS", "evaluating_words"); // FOR "queue" TABLE


	define("STATUS_FINISHED_EVALUATING_WORDS", "finished_evaluating_words"); // FOR "queue" TABLE


	define("STATUS_LOOKING_FOR_TAGS", "looking_for_tags"); // FOR "queue" TABLE

	// tags were found, item is ready for history
	// follows after "STATUS_LOOKING_FOR_TAGS"
	define("STATUS_READY_FOR_HISTORY", "ready_for_history"); // FOR "queue" TABLE


// ___________________

	// video is in the history
	define("STATUS_HISTORY", "history"); // FOR "media" TABLE

	// video does no longer exist / some problem in download script.
	define("STATUS_DOWNLOAD_ERROR", "download_error"); // FOR "queue" TABLE

	// probably: FFmpeg // FFprobe binaries could not be found.
	define("STATUS_SEGMENTING_ERROR", "segmenting_error"); // FOR "queue" TABLE

	define("ERRORS_SQL", "\"" . STATUS_DOWNLOAD_ERROR . "\",\"" . STATUS_SEGMENTING_ERROR . "\"");