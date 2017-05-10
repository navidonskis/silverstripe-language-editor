class SearchForm {

    constructor(container) {
        this.container = container;
        this.input = this.container.querySelector('input[name="search"]');
        this.timeout = null; // init timeout variable to be used below in bindEvents
        this.time = 500; // in ms

        this.bindEvents();
    }

    bindEvents() {
        // listen for keystroke events
        this.input.addEventListener('keyup', (e) => {
            // clear timeout if it has already been set.
            // this will prevent the previous task from executing
            // if it has been less than <MILLISECONDS>
            clearTimeout(this.timeout);

            // make a new timeout set to go off in {this.time}
            this.timeout = setTimeout(() => this.search(this.input.value), this.time);
        });
    }

    search(string) {
        let link = this.setUrlParameter(
            this.container.getAttribute('action'),
            'search',
            string
        );

        let moduleId = this.container.querySelector('input[name="moduleId"]');

        if (moduleId) {
            link = this.setUrlParameter(link, 'moduleId', moduleId.value);
        }

        window.location = link;
    }

    setUrlParameter(url, key, value) {
        let parts = url.split("#", 2), anchor = parts.length > 1 ? "#" + parts[1] : '';
        let query = (url = parts[0]).split("?", 2);
        if (query.length === 1)
            return url + "?" + key + "=" + value + anchor;

        for (let params = query[query.length - 1].split("&"), i = 0; i < params.length; i++)
            if (params[i].toLowerCase().startsWith(key.toLowerCase() + "="))
                return params[i] = key + "=" + value, query[query.length - 1] = params.join("&"), query.join("?") + anchor;

        return url + "&" + key + "=" + value + anchor
    }
}

export default SearchForm;