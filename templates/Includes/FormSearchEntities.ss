<form action="$Link" class="langeditor__search-form" method="get" data-module="search-form">
    <div class="field text">
        <input type="text" class="text" placeholder="<%t LangEditor.SEARCH_ENTITIES 'Search entities' %>" name="search"<% if $CurrentSearchTerm %> value="$CurrentSearchTerm"<% end_if %>>
    </div>

    <% if $CurrentModule %>
        <input type="hidden" name="moduleId" value="$CurrentModule.ID">
    <% end_if %>
</form>