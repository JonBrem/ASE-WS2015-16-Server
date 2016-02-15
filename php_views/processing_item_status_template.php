<script type="text/x-template" id="process_status_template">
	<%
		if (item.status=="segmenting_video") {
			%>
				<div class="queue_item_status being_processed_status_segmenting_video">
					<small>Schritt 1 von 4: Video wird segmentiert...</small>
				</div>
			<%
		} else if (item.status=="finished_segmenting_video") {
			%>
				<div class="queue_item_status being_processed_status_finished_segmenting_video">
					<small>Schritt 1 von 4: Videosegmentierung abgeschlossen</small>
				</div>
			<%
		} else if (item.status=="being_processed") {
			%>
				<div class="queue_item_status being_processed_status_being_processed">
					<small>Schritt 2 von 4: Texterkennung läuft...</small>
				</div>
			<%						
		} else if (item.status=="finished_processing") {
			%>
				<div class="queue_item_status being_processed_status_finished_processing">
					<small>Schritt 2 von 4: Texterkennung abgeschlossen</small>
				</div>
			<%
		} else if (item.status=="evaluating_words") {
			%>
				<div class="queue_item_status being_processed_status_evaluating_words">
					<small>Schritt 3 von 4: Evaluierung der erkannten Wörter...</small>
				</div>
			<%
		} else if (item.status=="finished_evaluating_words") {
			%>
				<div class="queue_item_status being_processed_status_finished_evaluating_words">
					<small>Schritt 3 von 4: Evaluierung der erkannten Wörter abgeschlossen</small>
				</div>
			<%
		} else if (item.status=="looking_for_tags") {
			%>
				<div class="queue_item_status being_processed_status_looking_for_tags">
					<small>Schritt 4 von 4: EvaluierungWörter abgeschlossen</small>
				</div>
			<%
		} else if (item.status=="ready_for_history") {
			%>
				<div class="queue_item_status being_processed_status_ready_for_history">
					<small>Schritt 4 von 4: Verarbeitung abgeschlossen</small>
				</div>
			<%
		}
	%>
</script>