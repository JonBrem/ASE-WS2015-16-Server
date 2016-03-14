<script type="text/x-template" id="status_template">
	<%
		if (item.status=="downloading") {						
			%>
				<div class="queue_item_status queue_item_downloading">
					<i class="fi-download"></i>
					<small>Video-Download läuft</small>
				</div>
			<%
		} else if (item.status=="downloaded") {
			%>
				<div class="queue_item_status queue_item_downloaded">
					<i class="fi-download"></i>
					<small>Video-Download vollständig</small>
				</div>
			<%
		} else if(item.status=="in_queue") {
			%>
				<div class="queue_item_status queue_item_waiting">
					<small style="color: #777">in Warteschlange</small>
				</div>
			<%
		} else {

		}
	%>
</script>