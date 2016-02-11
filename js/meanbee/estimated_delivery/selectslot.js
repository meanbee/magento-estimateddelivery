(function () {
    "use strict";
    if (!window.Meanbee) window.Meanbee = {};
    if (!window.Meanbee.EstimatedDelivery) window.Meanbee.EstimatedDelivery = {}

    function pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    }
    function multido (multidor, multidand, limit) {
        if (multidor + multidand > limit) return multidor;
        return multido (multidor + multidand, multidand, limit);
    }

    Meanbee.EstimatedDelivery.SlotPicker = function() {
        var resolution = 'day',
            year = (new Date).getFullYear(),
            month = (new Date).getMonth(),
            start = new Date(0),
            validDays = [0, 1, 2, 3, 4, 5, 6],
            holidays = [],
            container;

        Object.defineProperty(this, "resolution", {
            get: function () { return resolution; },
            set: function (res) { resolution = ~['day', 'week', 'month'].indexOf(res) ? res : resolution; }
        });
        Object.defineProperty(this, "year", {
            get: function () { return year; },
            set: function (y) { year = Math.floor(+y); }
        });
        Object.defineProperty(this, "month", {
            get: function () { return month; },
            set: function (m) { month = m < 12 && m >= 0 ? Math.floor(m) : month; }
        });
        Object.defineProperty(this, "start", {
            get: function () { return start; },
            set: function (st) { start = st instanceof Date ? st : start; }
        });
        Object.defineProperty(this, "validDays", {
            get: function () { return validDays; },
            set: function (vd) {
                if (vd.length > 7) return vd;
                for (var i = 0; i < vd.length; i++) {
                    if (+vd[i] >= 7 || +vd[i] < 0) return vd;
                }
                validDays = vd.map(function (day) { return Math.floor(+day)});
            }
        });
        Object.defineProperty(this, "holidays", {
            get: function () { return holidays; },
            set: function (hols) { holidays = hols; }
        });
        Object.defineProperty(this, "container", {
            get: function () { return container; },
            set: function (cont) { container = cont instanceof HTMLElement ? cont : container; }
        });

        this.goto = function (obj, dir) {
            obj = {y: obj.y === undefined ? this.year : obj.y, m: obj.m === undefined ? this.month : obj.m, d:  obj.d === undefined ? 1 : obj.d};
            var date = new Date(obj.y, obj.m, obj.d + 1);
            this.year = date.getFullYear();
            this.month = date.getMonth();
            this.render();
            if (obj.d !== undefined) {
                var targetDay = this.container.querySelector('input[type="radio"][value="' + (date.getDate() - 1) + '"]');
                if (!targetDay.disabled) targetDay.focus();
                else {
                    if (date < this.start) dir = +1;
                    this.goto({y: obj.y, m: obj.m, d: obj.d + dir}, dir);
                }
            }
        }

        this.render = function () {
            while (container.firstChild) container.removeChild(container.firstChild);

            var header = document.createElement('div');
            header.classList.add('selector-header');

            var headerText = document.createElement('div');
            headerText.classList.add('selector-header-text');

            var previousMonthButton = document.createElement('button');
            previousMonthButton.classList.add('selector-previous-month');
            previousMonthButton.type = 'button';
            previousMonthButton.tabIndex = -1;
            previousMonthButton.addEventListener('click', (function (event) {
                this.goto({m: this.month - 1});
                container.querySelector('.selector-previous-month').focus();
            }).bind(this), false);

            var nextMonthButton = document.createElement('button');
            nextMonthButton.classList.add('selector-next-month');
            nextMonthButton.type = 'button';
            nextMonthButton.tabIndex = -1;
            nextMonthButton.addEventListener('click', (function (event) {
                this.goto({m: this.month + 1});
                container.querySelector('.selector-next-month').focus();
            }).bind(this), false);

            var previousYearButton = document.createElement('button');
            previousYearButton.classList.add('selector-previous-year');
            previousYearButton.type = 'button';
            previousYearButton.tabIndex = -1;
            previousYearButton.addEventListener('click', (function (event) {
                this.goto({y: this.year - 1});
                container.querySelector('.selector-previous-year').focus();
            }).bind(this), false);

            var nextYearButton = document.createElement('button');
            nextYearButton.classList.add('selector-next-year');
            nextYearButton.type = 'button';
            nextYearButton.tabIndex = -1;
            nextYearButton.addEventListener('click', (function (event) {
                this.goto({y: this.year + 1});
                container.querySelector('.selector-next-year').focus();
            }).bind(this), false);

            header.appendChild(headerText);
            header.appendChild(previousYearButton);
            if (~['week', 'day'].indexOf(this.resolution)) {
                header.appendChild(previousMonthButton);
                header.appendChild(nextMonthButton);
            }
            header.appendChild(nextYearButton);
            switch (resolution) {
                case 'month':
                    headerText.innerHTML = this.year;

                    var monthsContainer = document.createElement('div');
                    monthsContainer.classList.add('selector-months');

                    for (var m = 0; m < 12; m++) {
                        var monthRadio = document.createElement('input');
                        monthRadio.type = 'radio';
                        monthRadio.required = true;
                        monthRadio.classList.add('validate-one-required-by-name');
                        monthRadio.name = 'slot-month';
                        monthRadio.value = m;
                        monthRadio.id = 'selector-month-' + m;
                        if (new Date(this.year, m + 1, 0) - this.start < 24*60*60) monthRadio.disabled = true;
                        else if (new Date(this.year, m + 1, 0) - this.start < (7 - this.validDays.length)*24*60*60) {
                            for (var i = this.start; i < new Date(this.year, m + 1, 1); i.setDate(i.getDate(i) + 1)) {
                                if (!~this.validDays.indexOf(i.getDay())) {
                                    monthRadio.disabled = true;
                                    break;
                                }
                            }
                        }

                        var monthLabel = document.createElement('label');
                        monthLabel.setAttribute('for', 'selector-month-' + m);
                        monthLabel.innerHTML = Meanbee.EstimatedDelivery.config.monthAbbr[m];

                        monthsContainer.appendChild(monthRadio);
                        monthsContainer.appendChild(monthLabel);
                    }

                    var yearField = document.createElement('input');
                    yearField.type = 'hidden';
                    yearField.name = 'slot-year';
                    yearField.value = this.year;

                    container.appendChild(header);
                    container.appendChild(monthsContainer);
                    container.appendChild(yearField);
                    break;
                case 'week':
                    headerText.innerHTML = Meanbee.EstimatedDelivery.config.monthAbbr[this.month] + ' ' + this.year;

                    var weeksContainer = document.createElement('div');
                    weeksContainer.classList.add('selector-weeks');

                    var tableHeader = document.createElement('div');
                    tableHeader.classList.add('selector-table-header');
                    for (var i = 0, d = Meanbee.EstimatedDelivery.config.startOfWeek; i < 7; i++) {
                        var day = document.createElement('div');
                        day.classList.add('day');
                        day.innerHTML = Meanbee.EstimatedDelivery.config.weekdayAbbr[(i + d) % 7];
                        tableHeader.appendChild(day);
                    }
                    weeksContainer.appendChild(tableHeader);

                    var dayOfWeek = (new Date(this.year, this.month, 1)).getDay() - Meanbee.EstimatedDelivery.config.startOfWeek;
                    for (var d = 0, w = 0, currentLabel; d < (new Date(this.year, this.month + 1, 0)).getDate(); d++, dayOfWeek++) {
                        if (dayOfWeek >= 7) {
                            dayOfWeek = 0;
                            w++;
                        }
                        if (dayOfWeek == 0 || d == 0) {
                            if (currentLabel) {
                                weeksContainer.appendChild(weekRadio);
                                weeksContainer.appendChild(currentLabel);
                                if (!currentLabel.querySelector(':not(.disabled)')) weekRadio.disabled = true;
                                currentLabel = undefined;
                            }

                            var weekRadio = document.createElement('input');
                            weekRadio.type = 'radio';
                            weekRadio.required = true;
                            weekRadio.classList.add('validate-one-required-by-name');
                            weekRadio.name = 'slot-week';
                            weekRadio.value = w;
                            weekRadio.id = 'selector-week-' + w;

                            currentLabel = document.createElement('label');
                            currentLabel.setAttribute('for', 'selector-week-' + w);
                        }
                        var day = document.createElement('div');
                        day.classList.add('day');
                        if (new Date(this.year, this.month, d + 1) < this.start || !~this.validDays.indexOf(dayOfWeek)) day.classList.add('disabled');
                        day.innerHTML = d + 1;
                        currentLabel.appendChild(day);
                    }
                    if (currentLabel) {
                        weeksContainer.appendChild(weekRadio);
                        weeksContainer.appendChild(currentLabel);
                        if (!currentLabel.querySelector(':not(.disabled)')) weekRadio.disabled = true;
                    }

                    var yearField = document.createElement('input');
                    yearField.type = 'hidden';
                    yearField.name = 'slot-year';
                    yearField.value = this.year;

                    var monthField = document.createElement('input');
                    monthField.type = 'hidden';
                    monthField.name = 'slot-month';
                    monthField.value = this.month;

                    container.appendChild(header);
                    container.appendChild(weeksContainer);
                    container.appendChild(yearField);
                    container.appendChild(monthField);
                    break;
                case 'day':
                    headerText.innerHTML = Meanbee.EstimatedDelivery.config.monthAbbr[this.month] + ' ' + this.year;

                    var daysContainer = document.createElement('div');
                    daysContainer.classList.add('selector-days');

                    var tableHeader = document.createElement('div');
                    tableHeader.classList.add('selector-table-header');
                    for (var i = 0, d = Meanbee.EstimatedDelivery.config.startOfWeek; i < 7; i++) {
                        var day = document.createElement('div');
                        day.classList.add('day');
                        day.innerHTML = Meanbee.EstimatedDelivery.config.weekdayAbbr[(i + d) % 7];
                        tableHeader.appendChild(day);
                    }
                    daysContainer.appendChild(tableHeader);

                    var dayOfWeek = (new Date(this.year, this.month, 1)).getDay() - Meanbee.EstimatedDelivery.config.startOfWeek;
                    for (var d = 0, w = 0, currentWeek; d < (new Date(this.year, this.month + 1, 0)).getDate(); d++, dayOfWeek++) {
                        if (dayOfWeek >= 7) {
                            dayOfWeek = 0;
                            w++;
                        }
                        if (dayOfWeek == 0 || d == 0) {
                            if (currentWeek) {
                                daysContainer.appendChild(currentWeek);
                                currentWeek = undefined;
                            }

                            var currentWeek = document.createElement('div');
                            currentWeek.classList.add('week');
                        }
                        var dayRadio = document.createElement('input');
                        dayRadio.type = 'radio';
                        dayRadio.required = true;
                        dayRadio.classList.add('validate-one-required-by-name');
                        dayRadio.name = 'slot-day';
                        dayRadio.value = d;
                        dayRadio.id = 'selector-day-' + d;
                        dayRadio.setAttribute('data-day-of-week', dayOfWeek);
                        if (new Date(this.year, this.month, d + 1) < this.start ||
                            !~this.validDays.indexOf(dayOfWeek)                 ||
                            ~Meanbee.EstimatedDelivery.holidays[this.holidays].indexOf(this.year+'-'+pad(this.month+1, 2)+'-'+pad(d+1, 2))) dayRadio.disabled = true;
                        dayRadio.addEventListener('keydown', dayKeyhandler.bind(this), false);
                        dayRadio.addEventListener('focus', radioDefocus, false);

                        var dayLabel = document.createElement('label');
                        dayLabel.setAttribute('for', 'selector-day-' + d);
                        dayLabel.classList.add('day');
                        dayLabel.innerHTML = d + 1;

                        currentWeek.appendChild(dayRadio);
                        currentWeek.appendChild(dayLabel);
                    }
                    if (currentWeek) {
                        daysContainer.appendChild(currentWeek);
                    }

                    var yearField = document.createElement('input');
                    yearField.type = 'hidden';
                    yearField.name = 'slot-year';
                    yearField.value = this.year;

                    var monthField = document.createElement('input');
                    monthField.type = 'hidden';
                    monthField.name = 'slot-month';
                    monthField.value = this.month;

                    container.appendChild(header);
                    container.appendChild(daysContainer);
                    container.appendChild(yearField);
                    container.appendChild(monthField);
                    break;
            }
        }
        function dayKeyhandler (event) {
            /** Follows WAI-ARIA best practices: https://www.w3.org/TR/2009/WD-wai-aria-practices-20090224/#datepicker */
            switch (event.keyCode || event.which) {
                case 13: // Enter
                case 32: // Space
                    /* Spacebar and/or Enter selects a day and deletes all multiple or range selection. */
                    event.preventDefault();
                    event.target.checked = !event.target.checked;
                    break;
                case 33: // Page Up
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey || event.shiftKey) {
                        /* Ctrl/Cmd/Shift + Page Up moves the focus to the same date of the previous year. */
                        this.goto({y: this.year - 1, d: +event.target.value}, -1);
                    } else {
                        /* Page Up moves the focus to same day in the previous month. */
                        this.goto({m: this.month - 1, d: +event.target.value}, -1);
                    }
                    break;
                case 34: // Page Down
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey || event.shiftKey) {
                        /* Ctrl/Cmd/Shift + Page Down moves the focus to the same date of the next year. */
                        this.goto({y: this.year + 1, d: +event.target.value}, -1);
                    } else {
                        /* Page Down moves the focus to same day in the next month. */
                        this.goto({m: this.month + 1, d: +event.target.value}, -1);
                    }
                    break;
                case 35: // End
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) {
                        /* Ctrl/Cmd + End moves the focus to the last day of the year. */
                        this.goto({m: 11, d: 31}, -1);
                    } else {
                        /* End moves the focus to the last day of the month. */
                        this.goto({d: event.target.parentElement.parentElement.querySelectorAll('input[type="radio"]').length - 1}, -1);
                    }
                    break;
                case 36: // Home
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) {
                        /* Ctrl/Cmd + Home moves the focus to the first day of the year. */
                        this.goto({m: 0, d: 0}, +1);
                    } else {
                        /* Home moves the focus to the first day of the month. */
                        this.goto({d: 0}, +1);
                    }
                    break;
                case 37: // Left Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) {
                        /* Ctrl/Cmd + Left Arrow moves the focus to the start of the week. */
                        this.goto({d: +event.target.value - event.target.getAttribute('data-day-of-week')}, +1);
                    } else {
                        /* Left Arrow moves the focus to the left, continued to previous week and previous month. */
                        this.goto({d: +event.target.value - 1}, -1);
                    }
                    break;
                case 38: // Up Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) {
                        /* Ctrl/Cmd + Up Arrow moves the focus to the same day of the week in the first week of the month. */
                        this.goto({d: +event.target.value % 7}, +1);
                    } else {
                        /* Up Arrow moves the focus to the same day of the week in the previous week, continued to the next month. */
                        this.goto({d: +event.target.value - 7}, +1);
                    }
                    break;
                case 39: // Right Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) {
                        /* Ctrl/Cmd + Right Arrow moves the focus to the end of the week. */
                        this.goto({d: +event.target.value + 6 - event.target.getAttribute('data-day-of-week')}, -1);
                    } else {
                        /* Right Arrow moves the focus to right, continued to the next week and next month. */
                        this.goto({d: +event.target.value + 1}, +1);
                    }
                    break;
                case 40: // Down Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) {
                        /* Ctrl/Cmd + Down Arrow moves focus to the same day of the week in the last week of the month. */
                        this.goto({d: multido(+event.target.value, 7, event.target.parentElement.parentElement.querySelectorAll('input[type="radio"]').length - 1)}, -1);
                    } else {
                        /* Down Arrow moves the focus to same weekday in the next week, continued to the next month. */
                        this.goto({d: +event.target.value + 7}, -1);
                    }
                    break;
            }
        }
    }
    function radioDefocus(event) {
        event.preventDefault();
    }
    Meanbee.EstimatedDelivery.loadedShippingMethods = function () {
        var container = document.querySelector('.meanbee_estimateddelivery-selectslot .selector');
        var selected = document.querySelector('input[name="shipping_method"][checked]');
        if (selected && selected.getAttribute('data-resolution')) {
            render(container,
                   selected.getAttribute('data-resolution'),
                   selected.getAttribute('data-first-valid-date'),
                   selected.getAttribute('data-deliverable-days'),
                   selected.getAttribute('data-exclude-holidays')
            );
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('change', function (event) {
            if (event.target.name === 'shipping_method') {
                var container = document.querySelector('.meanbee_estimateddelivery-selectslot .selector');
                var resolution = event.target.getAttribute('data-resolution');
                if (resolution) {
                    render(container,
                           resolution,
                           event.target.getAttribute('data-first-valid-date'),
                           event.target.getAttribute('data-deliverable-days'),
                           event.target.getAttribute('data-exclude-holidays')
                    );
                } else {
                    while (container.firstChild) container.removeChild(container.firstChild);
                    document.querySelector('.meanbee_estimateddelivery-selectslot').hidden = true;
                }
            }
        }, false);
    }, false);
    function render (container, resolution, firstValidDate, deliverableDays, holidays) {
        var slotPicker = new Meanbee.EstimatedDelivery.SlotPicker();
        slotPicker.resolution = resolution;
        slotPicker.container = container;
        slotPicker.start = new Date(firstValidDate);
        slotPicker.validDays = deliverableDays.split(',');
        slotPicker.holidays = holidays;
        slotPicker.render();
        window.slotPicker = slotPicker;

        document.querySelector('.meanbee_estimateddelivery-selectslot').hidden = false;
    }
}());
