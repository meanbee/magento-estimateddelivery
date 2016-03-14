(function () {
    "use strict";
    if (!window.Meanbee) window.Meanbee = {};
    if (!window.Meanbee.EstimatedDelivery) window.Meanbee.EstimatedDelivery = {}

    /**
     * Left-pads a string to desired length.
     *
     * @param  {number | string}  n     Starting string.
     * @param  number             width Desired string length.
     * @param  string             z     Character to use as padding.
     * @return string                   Padded string.
     */
    function pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    }

    /**
     * Conceptual opposite of modulo. Repeatedly adds the multidand to the multidor
     * until doing so exceeds the limit. Result is that of last sum. Unlike modulo,
     * multido is not restricted to integers.
     *
     * @param  number multidor
     * @param  number multidand
     * @param  number limit
     * @return number
     */
    function multido (multidor, multidand, limit) {
        if (multidor + multidand > limit) return multidor;
        return multido (multidor + multidand, multidand, limit);
    }

    /**
     * Utility function for creation of HTMLELement objects.
     *
     * @param  string      tagName Tag name of the HTML element
     * @param  object      options Object representing attributes (@), properties (% | +)
     *                             `data-*` attributes ($) and event listeners (: | =)
     *                             applied to the element.
     * @param  object      ctx     The context with which to bind event listeners (=) to.
     * @return HTMLElement         Constructed HTMLElement
     */
    function createElement (tagName, options, ctx) {
        var element = document.createElement(tagName);
        for (var option of Object.keys(options)) {
            if (option.substring(0,1) === '@') element.setAttribute(option.substring(1), options[option]);
            else if (option.substring(0,1) === '%' && element[option.substring(1)] !== undefined) element[option.substring(1)] = options[option];
            else if (option.substring(0,1) === '+' && element[option.substring(1)] === undefined) element[option.substring(1)] = options[option];
            else if (option.substring(0,1) === '$') element.setAttribute('data-'+option.substr(1).replace(/([^A-Z])([A-Z])/g, '$1-$2').toLowerCase(), options[option]);
            else if (option.substring(0,1) === ':') element.addEventListener(option.substr(1), options[option], false);
            else if (option.substring(0,1) === '=') element.addEventListener(option.substr(1), options[option].bind(ctx), false);
            //else element.appendChild(createElement(option, options[option]));
        }
        return element;
    };

    Meanbee.EstimatedDelivery.SlotPicker = function() {
        var resolution = 'day',
            year = (new Date).getFullYear(),
            month = (new Date).getMonth(),
            start = new Date(0),
            end = null,
            validDays = [0, 1, 2, 3, 4, 5, 6],
            holidays = [],
            container;

        // Define accessors and mutators.
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
        Object.defineProperty(this, "end", {
            get: function () { return end; },
            set: function (fin) { end = fin instanceof Date ? fin : end; }
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

        /**
         * Move pointer (and keyboard focus) to date, if target date is disabled
         * interate until enabled date is found.
         *
         * @param  object   obj Date to move to.
         * @param  number   dir Direction and step to iterate (-: past, +: future)
         */
        this.goto = function (obj, dir) {
            obj = {y: obj.y === undefined ? this.year : obj.y, m: obj.m === undefined ? this.month : obj.m, d:  obj.d === undefined ? 1 : obj.d};
            if (new Date(obj.y, obj.m + 1, 0) < this.start || new Date(obj.y, obj.m, 1) > this.end) return;
            var date = new Date(obj.y, obj.m, obj.d + 1);
            this.year = date.getFullYear();
            this.month = date.getMonth();
            this.render();
            if (obj.d !== undefined && dir) {
                var targetDay = this.container.querySelector('input[type="radio"][value="' + (date.getDate() - 1) + '"]');
                if (!targetDay.disabled) targetDay.focus();
                else {
                    if (date < this.start) this.goto({y: this.start.getFullYear(), m: this.start.getMonth(), d: this.start.getDate()}, +1);
                    else if (date > this.end) this.goto({y: this.end.getFullYear(), m: this.end.getMonth(), d: this.end.getDate()}, -1);
                    else this.goto({y: obj.y, m: obj.m, d: obj.d + dir}, dir);
                }
            }
        };

        /**
         * Formats current date for display.
         *
         * @param  number day Zero-based index of the day in the current month.
         * @return string     HTML string with formatted date.
         */
        this.formatDate = function (day) {
            var date = new Date(this.year, this.month, day + 1);
            var datestr = '<span class="visually-hidden">' + Meanbee.EstimatedDelivery.config.weekday[date.getDay()] + ', </span>' +
                          date.getDate() + '<span class="visually-hidden">' +
                          Meanbee.EstimatedDelivery.config.ordinal[Math.min(((date.getDate() - 1) % 30) % 20, Meanbee.EstimatedDelivery.config.ordinal.length - 1)] +
                          ' ' + Meanbee.EstimatedDelivery.config.month[date.getMonth()] +  ' ' + date.getFullYear() + '</span>';
            return datestr;
        };

        /**
         * Constructs and renders slotpicker to the DOM.
         */
        this.render = function () {
            while (container.firstChild) container.removeChild(container.firstChild);
            container.setAttribute('role', 'application');

            var header = createElement('div', {'@class': 'selector-header'}),
                headerText = createElement('div', {
                    '@class': 'selector-header-text',
                    '@role': 'heading'
                }),
                previousMonthButton = createElement('button', {
                    '@class': 'selector-previous-month',
                    '%tabIndex': -1,
                    '%type': 'button',
                    '=click': function (event) {
                        this.goto({m: this.month - 1});
                        container.querySelector('.selector-previous-month').focus();
                    }
                }, this),
                nextMonthButton = createElement('button', {
                    '@class': 'selector-next-month',
                    '%tabIndex': -1,
                    '%type': 'button',
                    '=click': function (event) {
                        this.goto({m: this.month + 1});
                        container.querySelector('.selector-next-month').focus();
                    }
                }, this),
                previousYearButton = createElement('button', {
                    '@class': 'selector-previous-year',
                    '%tabIndex': -1,
                    '%type': 'button',
                    '=click': function (event) {
                        this.goto({y: this.year - 1});
                        container.querySelector('.selector-previous-year').focus();
                    }
                }, this),
                nextYearButton = createElement('button', {
                    '@class': 'selector-next-year',
                    '%tabIndex': -1,
                    '%type': 'button',
                    '=click': function (event) {
                        this.goto({y: this.year + 1});
                        container.querySelector('.selector-next-year').focus();
                    }
                }, this);

            header.appendChild(headerText);
            header.appendChild(previousYearButton);
            if (~['week', 'day'].indexOf(this.resolution)) {
                header.appendChild(previousMonthButton);
                header.appendChild(nextMonthButton);
            }
            header.appendChild(nextYearButton);

            container.appendChild(header);

            switch (resolution) {
                case 'month':
                    headerText.innerHTML = this.year;

                    var monthsContainer = createElement('div', {
                        '@class': 'selector-months',
                        '@role': 'grid'
                    });
                    container.appendChild(monthsContainer);
                    container.appendChild(createElement('input', {
                        '@name': 'slot-year',
                        '@type': 'hidden',
                        '@value': this.year
                    }));

                    for (var m = 0; m < 12; m++) {
                        var monthRadio = createElement('input', {
                            '@class': 'validate-one-required-by-name',
                            '@id': 'selector-month-' + m,
                            '@name': 'slot-month',
                            '@role': 'gridcell',
                            '@value': m,
                            '%disabled': (new Date(this.year, m + 1, 0) - this.start < 24*60*60) ||
                                         (new Date(this.year, m, 1) > this.end),
                            '%required': true
                        })
                        if (!monthRadio.disabled && new Date(this.year, m + 1, 0) - this.start < (7 - this.validDays.length)*24*60*60) {
                            for (var i = this.start; i < new Date(this.year, m + 1, 1); i.setDate(i.getDate(i) + 1)) {
                                if (!~this.validDays.indexOf(i.getDay())) {
                                    monthRadio.disabled = true;
                                    break;
                                }
                            }
                        }

                        monthsContainer.appendChild(monthRadio);
                        monthsContainer.appendChild(createElement('label', {
                            '@for': 'selector-month-' + m,
                            '%innerHTML': '<abbr title="' + Meanbee.EstimatedDelivery.config.month[m] + '">' + Meanbee.EstimatedDelivery.config.monthAbbr[m] + '</abbr>'
                        }));
                    }
                    break;
                case 'week':
                    headerText.innerHTML = '<abbr title="' + Meanbee.EstimatedDelivery.config.month[this.month] + '">' +
                                           Meanbee.EstimatedDelivery.config.monthAbbr[this.month] + '</abbr>&nbsp;' + this.year;

                    var weeksContainer = createElement('div', {
                            '@class': 'selector-weeks',
                            '@role': 'grid'
                        }),
                        tableHeader = createElement('div', {
                            '@class': 'selector-table-header',
                            '@role': 'row'
                        });
                    for (var i = 0, d = Meanbee.EstimatedDelivery.config.startOfWeek; i < 7; i++) {
                        tableHeader.appendChild(createElement('div', {
                            '@class': 'day',
                            '%innerHTML': '<abbr title="' + Meanbee.EstimatedDelivery.config.weekday[(i + d) % 7] + '">' +
                                          Meanbee.EstimatedDelivery.config.weekdayAbbr[(i + d) % 7] + '</abbr>'
                        }));
                    }
                    weeksContainer.appendChild(tableHeader);
                    container.appendChild(weeksContainer);

                    var dayOfWeek = (new Date(this.year, this.month, 1)).getDay() - Meanbee.EstimatedDelivery.config.startOfWeek;
                    for (var d = 0, w = 0, currentLabel; d < (new Date(this.year, this.month + 1, 0)).getDate(); d++, dayOfWeek++) {
                        dayOfWeek %= 7;
                        w += Math.floor(dayOfWeek / 7);

                        if (dayOfWeek == 0 || d == 0) {
                            if (currentLabel) {
                                weeksContainer.appendChild(weekRadio);
                                weeksContainer.appendChild(currentLabel);
                                if (!currentLabel.querySelector(':not(.disabled)')) weekRadio.disabled = true;
                                currentLabel = undefined;
                            }

                            var weekRadio = createElement('input', {
                                    '@class': 'validate-one-required-by-name',
                                    '@id': 'selector-week-' + w,
                                    '@name': 'slot-week',
                                    '@role': 'gridcell',
                                    '@type': 'radio',
                                    '@value': w,
                                    '%required': true
                                }),
                                currentLabel = createElement('label', {
                                    '@for': 'selector-week-' + w,
                                    '@rule': 'row'
                                });
                        }
                        currentLabel.appendChild(createElement('div', {
                            '@class': 'day' + (new Date(this.year, this.month, d + 1) < this.start ||
                                               new Date(this.year, this.month, d + 1) > this.end   ||
                                               !~this.validDays.indexOf(dayOfWeek)                 ||
                                               ~(Meanbee.EstimatedDelivery.holidays[this.holidays]||[]).indexOf(this.year+'-'+pad(this.month+1, 2)+'-'+pad(d+1, 2))
                                           ) ? ' disabled' : '',
                            '%innerHTML': this.formatDate(d)
                        }));
                    }
                    if (currentLabel) {
                        weeksContainer.appendChild(weekRadio);
                        weeksContainer.appendChild(currentLabel);
                        if (!currentLabel.querySelector(':not(.disabled)')) weekRadio.disabled = true;
                    }

                    container.appendChild(createElement('input', {
                        '@name': 'slot-year',
                        '@type': 'hidden',
                        '@value': this.year
                    }));
                    container.appendChild(createElement('input', {
                        '@name': 'slot-month',
                        '@type': 'hidden',
                        '@value': this.month
                    }));
                    break;
                case 'day':
                    headerText.innerHTML = '<abbr title="' + Meanbee.EstimatedDelivery.config.month[this.month] + '">' +
                                           Meanbee.EstimatedDelivery.config.monthAbbr[this.month] + '</abbr>&nbsp;' + this.year;

                    var daysContainer = createElement('div', {
                            '@class': 'selector-days',
                            '@role': 'grid'
                        }),
                        tableHeader = createElement('div', {
                            '@class': 'selector-table-header',
                            '@role': 'row'
                        });
                    for (var i = 0, d = Meanbee.EstimatedDelivery.config.startOfWeek; i < 7; i++) {
                        tableHeader.appendChild(createElement('div', {
                            '@class': 'day',
                            '@role': 'columnheader',
                            '%innerHTML': '<abbr title="' + Meanbee.EstimatedDelivery.config.weekday[(i + d) % 7] + '">' +
                                          Meanbee.EstimatedDelivery.config.weekdayAbbr[(i + d) % 7] + '</abbr>'
                        }));
                    }
                    daysContainer.appendChild(tableHeader);
                    container.appendChild(daysContainer);

                    var dayOfWeek = (new Date(this.year, this.month, 1)).getDay() - Meanbee.EstimatedDelivery.config.startOfWeek;
                    for (var d = 0, w = 0, currentWeek; d < (new Date(this.year, this.month + 1, 0)).getDate(); d++, dayOfWeek++) {
                        dayOfWeek %= 7;
                        w += Math.floor(dayOfWeek / 7);

                        if (dayOfWeek == 0 || d == 0) var currentWeek = createElement('div', {
                            '@class': 'week',
                            '@role': 'row'
                        });
                        daysContainer.appendChild(currentWeek);

                        currentWeek.appendChild(createElement('input', {
                            '@class': 'validate-one-required-by-name',
                            '@id': 'selector-day-' + d,
                            '@name': 'slot-day',
                            '@role': 'gridcell',
                            '@type': 'radio',
                            '@value': d,
                            '$dayOfWeek': dayOfWeek,
                            '%disabled': new Date(this.year, this.month, d + 1) < this.start ||
                                         new Date(this.year, this.month, d + 1) > this.end   ||
                                         !~this.validDays.indexOf(dayOfWeek)                 ||
                                         ~(Meanbee.EstimatedDelivery.holidays[this.holidays]||[]).indexOf(this.year+'-'+pad(this.month+1, 2)+'-'+pad(d+1, 2)),
                            '%required': true,
                            '=keydown': dayKeyhandler,
                            ':focus': radioDefocus
                        }, this));
                        currentWeek.appendChild(createElement('label', {
                            '@class': 'day',
                            '@for': 'selector-day-' + d,
                            '%innerHTML': this.formatDate(d)
                        }));
                    }

                    container.appendChild(createElement('input', {
                        '@name': 'slot-year',
                        '@type': 'hidden',
                        '@value': this.year
                    }));
                    container.appendChild(createElement('input', {
                        '@name': 'slot-month',
                        '@type': 'hidden',
                        '@value': this.month
                    }));
                    break;
            }
        }

        /**
         * Handles keyboard navigation according to WAI-ARIA best practices of
         * a date picker.
         *
         * @see https://www.w3.org/TR/2009/WD-wai-aria-practices-20090224/#datepicker
         *
         * @param  KeyboardEvent event
         */
        function dayKeyhandler (event) {
            switch (event.keyCode || event.which) {
                case 13: // Enter
                case 32: // Space
                    /* Spacebar and/or Enter selects a day and deletes all multiple or range selection. */
                    event.preventDefault();
                    event.target.checked = !event.target.checked;
                    break;
                case 33: // Page Up
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey || event.shiftKey) { // Ctrl/Cmd/Shift + Page Up moves the focus to the same date of the previous year.
                        this.goto({y: this.year - 1, d: +event.target.value}, -1);
                    } else {                                                // Page Up moves the focus to same day in the previous month.
                        this.goto({m: this.month - 1, d: +event.target.value}, -1);
                    }
                    break;
                case 34: // Page Down
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey || event.shiftKey) { // Ctrl/Cmd/Shift + Page Down moves the focus to the same date of the next year.
                        this.goto({y: this.year + 1, d: +event.target.value}, -1);
                    } else {                                                // Page Down moves the focus to same day in the next month.
                        this.goto({m: this.month + 1, d: +event.target.value}, -1);
                    }
                    break;
                case 35: // End
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) { // Ctrl/Cmd + End moves the focus to the last day of the year.
                        this.goto({m: 11, d: 31}, -1);
                    } else {                              // End moves the focus to the last day of the month.
                        this.goto({d: event.target.parentElement.parentElement.querySelectorAll('input[type="radio"]').length - 1}, -1);
                    }
                    break;
                case 36: // Home
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) { // Ctrl/Cmd + Home moves the focus to the first day of the year.
                        this.goto({m: 0, d: 0}, +1);
                    } else {                              // Home moves the focus to the first day of the month.
                        this.goto({d: 0}, +1);
                    }
                    break;
                case 37: // Left Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) { // Ctrl/Cmd + Left Arrow moves the focus to the start of the week.
                        this.goto({d: +event.target.value - event.target.getAttribute('data-day-of-week')}, +1);
                    } else {                              // Left Arrow moves the focus to the left, continued to previous week and previous month.
                        this.goto({d: +event.target.value - 1}, -1);
                    }
                    break;
                case 38: // Up Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) { // Ctrl/Cmd + Up Arrow moves the focus to the same day of the week in the first week of the month.
                        this.goto({d: +event.target.value % 7}, +1);
                    } else {                              // Up Arrow moves the focus to the same day of the week in the previous week, continued to the next month.
                        this.goto({d: +event.target.value - 7}, +1);
                    }
                    break;
                case 39: // Right Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) { // Ctrl/Cmd + Right Arrow moves the focus to the end of the week.
                        this.goto({d: +event.target.value + 6 - event.target.getAttribute('data-day-of-week')}, -1);
                    } else {                              // Right Arrow moves the focus to right, continued to the next week and next month.
                        this.goto({d: +event.target.value + 1}, +1);
                    }
                    break;
                case 40: // Down Arrow
                    event.preventDefault();
                    if (event.metaKey || event.ctrlKey) { // Ctrl/Cmd + Down Arrow moves focus to the same day of the week in the last week of the month.
                        this.goto({d: multido(+event.target.value, 7, event.target.parentElement.parentElement.querySelectorAll('input[type="radio"]').length - 1)}, -1);
                    } else {                              // Down Arrow moves the focus to same weekday in the next week, continued to the next month.
                        this.goto({d: +event.target.value + 7}, -1);
                    }
                    break;
            }
        };
    }

    /**
     * Prevents automatic selection of a radio button upon focus. Allows for
     * navigating the option space without forcing a selection.
     *
     * @param  FocusEvent event
     */
    function radioDefocus(event) {
        event.preventDefault();
    }

    Meanbee.EstimatedDelivery.loadedShippingMethods = function () {
        var container = document.querySelector('.meanbee_estimateddelivery-selectslot .selector');
        var selected = document.querySelector('input[name="shipping_method"][checked]');
        if (selected && selected.getAttribute('data-resolution')) {
            Meanbee.EstimatedDelivery.render(container,
                   selected.getAttribute('data-resolution'),
                   selected.getAttribute('data-first-valid-date'),
                   selected.getAttribute('data-deliverable-days'),
                   selected.getAttribute('data-exclude-holidays'),
                   selected.getAttribute('data-upper-limit')
            );
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('change', function (event) {
            if (event.target.name === 'shipping_method') {
                var container = document.querySelector('.meanbee_estimateddelivery-selectslot .selector');
                var resolution = event.target.getAttribute('data-resolution');
                if (resolution) {
                    Meanbee.EstimatedDelivery.render(container,
                           resolution,
                           event.target.getAttribute('data-first-valid-date'),
                           event.target.getAttribute('data-deliverable-days'),
                           event.target.getAttribute('data-exclude-holidays'),
                           event.target.getAttribute('data-upper-limit')
                    );
                } else {
                    while (container.firstChild) container.removeChild(container.firstChild);
                    document.querySelector('.meanbee_estimateddelivery-selectslot').hidden = true;
                }
            }
        }, false);
    }, false);
    Meanbee.EstimatedDelivery.render = function (container, resolution, firstValidDate, deliverableDays, holidays, upperLimit) {
        var slotPicker = new Meanbee.EstimatedDelivery.SlotPicker();
        slotPicker.resolution = resolution;
        slotPicker.container = container;
        slotPicker.start = new Date(firstValidDate);
        slotPicker.validDays = deliverableDays.split(',');
        slotPicker.holidays = holidays;
        var upperLimitParts = upperLimit.split(/[^0-9.]/).filter(function (x) { return x.length; }).map(function (x) { return +x; });
        upperLimit = new Date(
            slotPicker.start.getFullYear() + upperLimitParts[0],
            slotPicker.start.getMonth() + upperLimitParts[1],
            slotPicker.start.getDate() + upperLimitParts[2]
        );
        slotPicker.end = upperLimit;
        slotPicker.render();
        window.slotPicker = slotPicker;

        document.querySelector('.meanbee_estimateddelivery-selectslot').hidden = false;
    }
}());
