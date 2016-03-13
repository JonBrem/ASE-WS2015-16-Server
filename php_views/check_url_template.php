
<% if(item.url != null && item.url.length > 0) { %>
	<a href="<%= item.url %>">Zur Mediathek</a>
<% } else { %> 
	<a href="#">kein Link angegeben</a>
<% } %>