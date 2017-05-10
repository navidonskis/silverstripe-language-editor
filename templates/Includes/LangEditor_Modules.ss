<% if $Modules %>
    <section class="langeditor__modules">
        <% loop $Modules %>
            <a href="$Link"<% if $Current %> class="current"<% end_if %> id="$Name">$Name</a>
        <% end_loop %>
    </section>
<% end_if %>