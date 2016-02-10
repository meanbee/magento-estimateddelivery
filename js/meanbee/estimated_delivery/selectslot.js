(function () {
    "use strict";
    if (!window.Meanbee) window.Meanbee = {};
    if (!window.Meanbee.EstimatedDelivery) window.Meanbee.EstimatedDelivery = {}
    Meanbee.EstimatedDelivery.SlotPicker = function() {
        var resolution = 'day',
            year = (new Date).getFullYear(),
            month = (new Date).getMonth(),
            start = new Date(0),
            validDays = [0, 1, 2, 3, 4, 5, 6],
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
        Object.defineProperty(this, "container", {
            get: function () { return container; },
            set: function (cont) { container = cont instanceof HTMLElement ? cont : container; }
        });

        this.render = function () {
            while (container.firstChild) container.removeChild(container.firstChild);

            var header = document.createElement('div');
            header.classList.add('selector-header');

            var headerText = document.createElement('div');
            headerText.classList.add('selector-header-text');

            var previousMonthButton = document.createElement('button');
            previousMonthButton.classList.add('selector-previous-month');
            previousMonthButton.type = 'button';
            previousMonthButton.addEventListener('click', (function (event) {
                if (this.month == 0) {
                    this.month = 11;
                    this.year--;
                } else {
                    this.month--;
                }
                this.render();
                container.querySelector('.selector-previous-month').focus();
            }).bind(this), false);

            var nextMonthButton = document.createElement('button');
            nextMonthButton.classList.add('selector-next-month');
            nextMonthButton.type = 'button';
            nextMonthButton.addEventListener('click', (function (event) {
                if (this.month == 11) {
                    this.month = 0;
                    this.year++;
                } else {
                    this.month++;
                }
                this.render();
                container.querySelector('.selector-next-month').focus();
            }).bind(this), false);

            var previousYearButton = document.createElement('button');
            previousYearButton.classList.add('selector-previous-year');
            previousYearButton.type = 'button';
            previousYearButton.addEventListener('click', (function (event) {
                this.year--;
                this.render();
                container.querySelector('.selector-previous-year').focus();
            }).bind(this), false);

            var nextYearButton = document.createElement('button');
            nextYearButton.classList.add('selector-next-year');
            nextYearButton.type = 'button';
            nextYearButton.addEventListener('click', (function (event) {
                this.year++;
                this.render();
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
                        if (new Date(this.year, this.month, d + 1) < this.start || !~this.validDays.indexOf(dayOfWeek)) dayRadio.disabled = true;

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
    }
    Meanbee.EstimatedDelivery.loadedShippingMethods = function () {
        var container = document.querySelector('.meanbee_estimateddelivery-selectslot .selector');
        var selected = document.querySelector('input[name="shipping_method"][checked]');
        if (selected && selected.getAttribute('data-resolution')) {
            render(container, selected.getAttribute('data-resolution'), selected.getAttribute('data-first-valid-date'), selected.getAttribute('data-deliverable-days'));
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('change', function (event) {
            if (event.target.name === 'shipping_method') {
                var container = document.querySelector('.meanbee_estimateddelivery-selectslot .selector');
                var resolution = event.target.getAttribute('data-resolution');
                if (resolution) {
                    render(container, resolution, event.target.getAttribute('data-first-valid-date'), event.target.getAttribute('data-deliverable-days'));
                } else {
                    while (container.firstChild) container.removeChild(container.firstChild);
                    document.querySelector('.meanbee_estimateddelivery-selectslot').hidden = true;
                }
            }
        }, false);
    }, false);
    function render (container, resolution, firstValidDate, deliverableDays) {
        var slotPicker = new Meanbee.EstimatedDelivery.SlotPicker();
        slotPicker.resolution = resolution;
        slotPicker.container = container;
        slotPicker.start = new Date(firstValidDate);
        slotPicker.validDays = deliverableDays.split(',');
        slotPicker.render();
        window.slotPicker = slotPicker;

        document.querySelector('.meanbee_estimateddelivery-selectslot').hidden = false;
    }
}());
