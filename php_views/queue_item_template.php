<script type="text/x-template" id="queue_item_template">
	<li class="queue_list_item row align-middle <%= item.status %>" data-item-id="<%= item.id %>">
		<div class="small-1 large-1 columns queue_item_number"><%= item.number %></div>
		<div class="small-2 large-3 columns queue_item_image_wrapper"><img class="thumbnail" src="<%= item.preview_img %>" /></div>
		<div class="small-5 large-5 columns queue_item_info">
			<div class="row">
				<div class="small-12 columns queue_item_title"><%= item.title %></div>
			</div>
			<div class="row">
				<div class="small-12 columns queue_item_url"><a href="<%= item.url %>">Zur Mediathek</a></div>
			</div>
		</div>

		<div class="small-3 large-2 columns queue_item_progress">
		<!--__ -->
		</div>
		<div class="small-1 large-1 columns queue_item_cancel">
			x
		</div>
	</li>
</script>