import SearchForm from './modules/SearchForm';

(function ($) {
    $.entwine('ss', function ($) {
        $('#lang-editor-cms-content').entwine({
            onmatch: function () {
                window.langeditor = {
                    instances: [],
                    modules: [
                        {name: 'search-form', class: SearchForm}
                    ]
                };

                window.langeditor.modules.forEach((item) => {
                    // try to find an element
                    let element = $(this).find(`*[data-module="${item.name}"]`);

                    if (element.length) {
                        window.langeditor.instances.push(new item.class(element[0]));
                    }
                });
            }
        });
    });
})(jQuery);