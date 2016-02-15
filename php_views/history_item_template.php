<script type="text/x-template" id="history_item_template">
		<li class="history_list_item row align-middle" data-item-id="<%= item.id %>">
			<div class="small-2 large-3 columns history_item_image_wrapper">
				<img class="thumbnail" src="<%= item.preview_image %>" />
			</div>
			<div class="small-5 large-5 columns history_item_info">
				<div class="row">
					<div class="small-12 columns history_item_title"><%= item.title %></div>
				</div>
				<div class="row">
					<div class="small-12 columns history_item_url"><a href="<%= item.url %>">Zur Mediathek</a></div>
				</div>
			</div>

			<div class="small-5 large-4 columns history_item_tags">
				<% for(var i = 0; i < item.tags.length; i++) { %>
					<span 
						class="<% if(item.tags[i].accepted==0){%>secondary<%} else {%>success<%} %> label history_item_tag"
						data-id="<%= item.tags[i].id %>"><%= item.tags[i].content %></label>
					</span>
				<% } %>
			</div>

		</li>
	</script>