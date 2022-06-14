L.Control.searchControlControl = L.Control.extend({

    initialize: function ({ data, map }) {
        this._data = data;
        this._map = map;
    },

    onAdd: function (map) {
        const div = L.DomUtil.create('div', 'example');
        const input = L.DomUtil.create('input', 'searchInput', div);
        var datalist = L.DomUtil.create('datalist', '', div);
        const btn = L.DomUtil.create('button', 'searchButton', div);
        const ibalise = L.DomUtil.create('i', 'fa fa-search', btn);
        div.setAttribute('id', 'example')

        btn.setAttribute('title', 'Search host');
        btn.setAttribute('id', 'searchButton')

        input.setAttribute('type', 'text')
        input.setAttribute('id', 'input')
        input.setAttribute('placeholder', 'Search host...')
        input.setAttribute('list', 'hosts')
        input.setAttribute('autocomplete', 'off')

        datalist.setAttribute('id', 'hosts')
        datalist.id = "hosts"

        for (var i = 0; i < this._data.length; ++i) {
            const option = L.DomUtil.create('option', '', datalist);
            option.value = this._data[i]['name']
        }

        L.DomEvent.on(btn, 'click', () => { search() });
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

L.Control.severityControlControl = L.Control.extend({
    _severity_levels: null,
    _filter_checked: [],

    initialize: function ({ checked, severity_levels, disabled }) {
        this._filter_checked = checked;
        this._severity_levels = severity_levels;
        this._disabled = disabled;
    },

    onAdd: function (map) {
        const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
        const btn = L.DomUtil.create('a', 'geomap-filter-button', div);
        this.bar = L.DomUtil.create('ul', 'checkbox-list geomap-filter', div);

        btn.ariaLabel = t('Severity filter');
        btn.title = t('Severity filter');
        btn.role = 'button';
        btn.href = '#';

        if (!this._disabled) {
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

L.Control.departmentControlControl = L.Control.extend({

    _departments: null,

    initialize: function ({ departments, disabled }) {
        this._departments = departments;
        this._disabled = disabled;
    },

    onAdd: function (map) {
        const div = L.DomUtil.create('div');
        const select = L.DomUtil.create('select', 'custom-select-perso select-top', div);
        const btn = L.DomUtil.create('button', 'custom-button-perso button-top', div);
        const ibalise = L.DomUtil.create('i', 'fa fa-search', btn);

        div.setAttribute('id', 'department');
        select.setAttribute('id', 'select-dep');
        btn.setAttribute('id', 'searchDepartment');
        btn.setAttribute('title', 'Search department');

        L.DomEvent.on(btn, 'click', () => { chooseDepartment() });

        departments.forEach(department => {
            const option = L.DomUtil.create('option', '', select);
            value = department.code + "-" + department.name;
            option.value = value;
            option.innerHTML = value;
            if (department.code == "00") {
                option.setAttribute('selected', '');
            }
        });

        L.DomEvent.on(btn, 'click', () => { chooseDepartment() });
        select.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                document.getElementById("searchDepartment").click();
            }
        });

        return div;
    },
});

L.control.departmentControl = function (opts) {
    return new L.Control.departmentControlControl(opts);
};

L.Control.regionControlControl = L.Control.extend({

    _regions: null,

    initialize: function ({ regions, disabled }) {
        this._regions = regions;
        this._disabled = disabled;
    },

    onAdd: function (map) {
        const div = L.DomUtil.create('div');
        const select = L.DomUtil.create('select', 'custom-select-perso select-top', div);
        const btn = L.DomUtil.create('button', 'custom-button-perso button-top', div);
        const ibalise = L.DomUtil.create('i', 'fa fa-search', btn);

        div.setAttribute('id', 'region');
        select.setAttribute('id', 'select-region');
        btn.setAttribute('id', 'searchRegion');
        btn.setAttribute('title', 'Search region');

        L.DomEvent.on(btn, 'click', () => { chooseRegion() });

        regions.forEach(region => {
            const option = L.DomUtil.create('option', '', select);
            value = region.name;
            option.value = value;
            option.innerHTML = value;
            if (region.name == "ALL REGIONS") {
                option.setAttribute('selected', '');
            }
        });

        L.DomEvent.on(btn, 'click', () => { chooseRegion() });
        select.addEventListener("keyup", function (event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                document.getElementById("searchRegion").click();
            }
        });

        return div;
    },
});

L.control.regionControl = function (opts) {
    return new L.Control.regionControlControl(opts);
};