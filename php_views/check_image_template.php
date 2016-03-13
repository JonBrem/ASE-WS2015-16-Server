<%
			if(item.preview_img != null && item.preview_img.length > 0) { 
				%><%= item.preview_img %><% 
			} else { %>no_image_available.png<% 
			} %>