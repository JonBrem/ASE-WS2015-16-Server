<script type="text/x-template" id="history_item_template">
	<li class="history_list_item row align-middle" data-item-id="<%= item.id %>">
		<div class="small-2 large-3 columns history_item_image_wrapper">
			<img class="thumbnail" src="<?php include('check_image_template.php');?>" />
		</div>
		<div class="small-4 large-4 columns history_item_info">
			<div class="row">
				<div class="small-12 columns history_item_title">					
					<?php include('check_title_template.php'); ?>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns history_item_url">
					<?php include('check_url_template.php'); ?>
				</div>
			</div>
		</div>

		<div class="small-5 large-4 columns history_item_tags">
		</div>

		<div class="small-1 large-1 columns history_item_cancel">
			<button type="button" class="alert button history_item_delete has-tip top" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="2" title="Video löschen"><i class="fi-x"></i></button>
		</div>

	</li>
</script>

<script type="text/x-template" id="history_item_tag_template">
	<span 
		class="<% if(item.accepted==0){%>secondary<%} else {%>success<%} %> label history_item_tag"
		data-id="<%= item.id %>"><%= item.content %></label>
	</span>
</script>