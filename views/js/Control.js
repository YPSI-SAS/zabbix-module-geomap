/**
 * Create new control to make search bar
 */
L.Control.searchControlControl = L.Control.extend({

    /**
     * Initialize control with map and hosts array
     * @param {*} param0 
     */
    initialize: function ({ hosts, map }) {
        this._hosts = hosts;
        this._map = map;
    },

    /**
     * Add elements of search bar in document
     * @param {*} map 
     * @returns 
     */
    onAdd: function (map) {
        //Create differents HTML tags and set attributes
        const div = L.DomUtil.create('div', 'example');
        const input = L.DomUtil.create('input', 'searchInput', div);
        var datalist = L.DomUtil.create('datalist', '', div);
        var btn = L.DomUtil.create('button', 'searchButton', div);
        const ibalise = L.DomUtil.create('i', 'fa fa-search', btn);

        div.setAttribute('id', 'example');

        btn.setAttribute('title', 'Search host');
        btn.setAttribute('id', 'searchButton');

        input.setAttribute('type', 'text');
        input.setAttribute('id', 'input')
        input.setAttribute('placeholder', 'Search host...');
        input.setAttribute('list', 'hosts');
        input.setAttribute('autocomplete', 'off');

        datalist.setAttribute('id', 'hosts');

        //Assign search action on button if clicked
        L.DomEvent.on(btn, 'click', () => { search() });

        for (var i = 0; i < this._hosts.length; ++i) {
            const option = L.DomUtil.create('option', '', datalist);
            option.value = this._hosts[i]['name']
        }

        input.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                document.getElementById("searchButton").click();
            }
        });

        return div;
    },

});

L.control.searchControl = function (opts) {
    return new L.Control.searchControlControl(opts);
};

/**
 * Create new control to make severities filter
 */
L.Control.severityControlControl = L.Control.extend({

    /**
     * Initialize control with checked array, severity array and if it is disable
     * @param {*} param0 
     */
    initialize: function ({ checked, severity_levels, disabled }) {
        this._filter_checked = checked;
        this._severity_levels = severity_levels;
        this._disabled = disabled;
    },

    /**
     * Add elements to filter in document
     * @param {*} map 
     * @returns 
     */
    onAdd: function (map) {
        //Create differents HTML tags and set attributes
        const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
        const btn = L.DomUtil.create('a', 'geomap-filter-button', div);
        this.bar = L.DomUtil.create('ul', 'checkbox-list geomap-filter', div);

        btn.ariaLabel = t('Severity filter');
        btn.title = t('Severity filter');
        btn.role = 'button';
        btn.href = '#';

        if (!this._disabled) {
            //For each severity create one checkbox
            for (const [severity, prop] of this._severity_levels) {
                const li = L.DomUtil.create('li', '', this.bar);
                const chbox = L.DomUtil.create('input', '', li);
                const label = L.DomUtil.create('label', '', li);
                const span = L.DomUtil.create('span', '');
                const chBoxId = 'filter_severity_' + map.elmntCounter();

                label.append(span, document.createTextNode(prop.name));
                chbox.checked = this._filter_checked.includes(severity.toString(10));
                chbox.classList.add('checkbox-radio');
                chbox.type = 'checkbox';
                chbox.value = severity;
                chbox.id = chBoxId;
                label.htmlFor = chBoxId;

                //If checkbox changes update MAP
                chbox.addEventListener('change', () => {
                    var detail = [...this.bar.querySelectorAll('input[type="checkbox"]:checked')].map(n => n.value)
                    if (detail.length == 0) {
                        severity_selected = ["-1", "0", "1", "2", "3", "4", "5"]
                        updateMap()
                    } else {
                        severity_selected = detail
                        updateMap()
                    }
                });
            }

            L.DomEvent.on(btn, 'click', () => {
                this.bar.classList.toggle('collapsed');
            });
            L.DomEvent.on(this.bar, 'dblclick', (e) => {
                L.DomEvent.stopPropagation(e);
            });

        }
        else {
            div.classList.add('disabled');
        }

        L.DomEvent.on(btn, 'dblclick', (e) => {
            L.DomEvent.stopPropagation(e);
        });

        return div;
    },

    close: function () {
        this.bar.classList.remove('collapsed');
    }
});

L.control.severityControl = function (opts) {
    return new L.Control.severityControlControl(opts);
};

/**
 * Create new control to make department filter
 */
L.Control.departmentControlControl = L.Control.extend({

    /**
     * Initialize control with departments array and if it is disable
     * @param {*} param0 
     */
    initialize: function ({ departments }) {
        this._departments = departments;
    },

    /**
     * Add elements to filter in document
     * @param {*} map 
     * @returns 
     */
    onAdd: function (map) {
        //Create differents HTML tags and set attributes
        const div = L.DomUtil.create('div');
        const select = L.DomUtil.create('select', 'custom-select-perso select-top', div);
        const btn = L.DomUtil.create('button', 'custom-button-perso button-top', div);
        const ibalise = L.DomUtil.create('i', 'fa fa-search', btn);

        div.setAttribute('id', 'department');
        select.setAttribute('id', 'select-dep');
        btn.setAttribute('id', 'getFileDepartment');
        btn.setAttribute('title', 'Search department');

        //Assign getFileDepartment action on button if clicked
        L.DomEvent.on(btn, 'click', () => { getFileDepartment() });

        //Create one option for each department
        departments.forEach(department => {
            const option = L.DomUtil.create('option', '', select);
            value = department.code + "-" + department.name;
            option.value = value;
            option.innerHTML = value;
            if (department.code == "00") {
                option.setAttribute('selected', '');
            }
        });

        select.addEventListener('change', () => {
            document.getElementById("getFileDepartment").click();
        });

        select.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                document.getElementById("getFileDepartment").click();
            }
        });

        return div;
    },
});

L.control.departmentControl = function (opts) {
    return new L.Control.departmentControlControl(opts);
};

/**
 * Create new control to make region filter
 */
L.Control.regionControlControl = L.Control.extend({

    /**
     * Initialize control with regions array and if it is disable
     * @param {*} param0 
     */
    initialize: function ({ regions }) {
        this._regions = regions;
    },

    /**
     * Add elements to filter in document
     * @param {*} map 
     * @returns 
     */
    onAdd: function (map) {
        //Create differents HTML tags and set attributes
        const div = L.DomUtil.create('div');
        const select = L.DomUtil.create('select', 'custom-select-perso select-top', div);
        const btn = L.DomUtil.create('button', 'custom-button-perso button-top', div);
        const ibalise = L.DomUtil.create('i', 'fa fa-search', btn);

        div.setAttribute('id', 'region');
        select.setAttribute('id', 'select-region');
        btn.setAttribute('id', 'getFileRegion');
        btn.setAttribute('title', 'Search region');

        //Assign getFileRegion action on button if clicked
        L.DomEvent.on(btn, 'click', () => { getFileRegion() });

        //Create one option for each region
        regions.forEach(region => {
            const option = L.DomUtil.create('option', '', select);
            value = region.name;
            option.value = value;
            option.innerHTML = value;
            if (region.name == "ALL REGIONS") {
                option.setAttribute('selected', '');
            }
        });

        select.addEventListener('change', () => {
            document.getElementById("getFileRegion").click();
        });

        select.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                document.getElementById("getFileRegion").click();
            }
        });

        return div;
    },
});

L.control.regionControl = function (opts) {
    return new L.Control.regionControlControl(opts);
};

/**
 * Create new control to make clear button
 */
L.Control.clearControlControl = L.Control.extend({

    /**
     * Initialize control 
     */
    initialize: function ({ }) {
    },

    /**
     * Add elements to filter in document
     * @param {*} map 
     * @returns 
     */
    onAdd: function (map) {
        //Create differents HTML tags and set attributes
        const div = L.DomUtil.create('div');
        const input = L.DomUtil.create('input', 'button-clear button-top', div);

        div.setAttribute('id', 'clear');
        input.setAttribute('id', 'clearFilter');
        input.setAttribute('title', 'Clear department/region');
        input.setAttribute('type', 'button');
        input.setAttribute('value', 'Clear department and region');

        //Assign clearDepartmentAndRegion action on button if clicked
        L.DomEvent.on(input, 'click', () => { clearDepartmentAndRegion() });

        return div;
    },
});

L.control.clearControl = function (opts) {
    return new L.Control.clearControlControl(opts);
};