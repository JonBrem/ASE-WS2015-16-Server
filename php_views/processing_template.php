<script type="text/x-template" id="processing_template">
	<div class="being_processed_item row align-middle <%= item.status %>" data-item-id="<%= item.id %>">
		<div class="small-2 large-3 columns being_processed_item_image_wrapper"><img class="thumbnail" src="<%= item.preview_img %>" /></div>
		<div class="small-5 large-5 columns being_processed_item_info">
			<div class="row">
				<div class="small-12 columns being_processed_item_title"><%= item.title %></div>
			</div>
			<div class="row">
				<div class="small-12 columns being_processed_item_url"><a href="<%= item.url %>">Zur Mediathek</a></div>
			</div>
		</div>

		<div class="small-3 large-2 columns being_processed_item_progress">
		<!--__ -->
		</div>
		<div class="small-1 large-1 columns being_processed_item_cancel">
			x
		</div>
	</div>
</script>