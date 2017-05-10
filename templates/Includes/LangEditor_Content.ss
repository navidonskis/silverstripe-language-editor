<div id="lang-editor-cms-content" class="langeditor cms-content center $BaseCSSClasses" data-layout-type="border"
     data-pjax-fragment="Content" data-ignore-tab-state="true">

    <div class="langeditor__header cms-content-header north">
        <div class="cms-content-header-info">
            <h2 id="page-title-heading">
                <% include CMSBreadcrumbs %>
            </h2>

            <% include FormSearchEntities %>
        </div>
    </div>

    <div class="langeditor__content cms-content-tools west cms-panel cms-panel-layout" id="cms-content-tools-LangEditor"
         data-expandOnClick="false" data-layout-type="border">
        <div class="langeditor__sidebar cms-panel-content center">
            <h3 class="cms-panel-header"><% _t('AssetAdmin_Tools.FILTER', 'Filter') %></h3>
            <h4><% _t('LangEditor.AVAILABLE_MODULES','Available modules') %></h4>
            <div id="available_modules">
                <% include LangEditor_Modules %>
            </div>
        </div>
        <div class="cms-panel-content-collapsed">
            <h3 class="cms-panel-header"><% _t('AssetAdmin_Tools.FILTER', 'Filter') %></h3>
        </div>
    </div>

    <div class="langeditor__content--form-entities cms-content-fields center">
        $FormEntities
    </div>
</div>