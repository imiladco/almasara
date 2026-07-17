/**
 * Car Wash — فایل JavaScript اصلی
 * Version: 3.2.0
 * File: assets/js/carwash.js
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // CONFIG از PHP
    // ============================================================

    var cfg = window.carwashConfig || {};

    var carTypePrices = {};
    var carTypeNames  = {};
    var servicePrices = {};
    var serviceNames  = {};
    var orgMap        = {};

    (cfg.carTypes || []).forEach(function (car) {
        carTypePrices[car.type] = car.price;
        carTypeNames[car.type]  = car.title;
    });

    (cfg.services || []).forEach(function (svc) {
        servicePrices[svc.value] = svc.price;
        serviceNames[svc.value]  = svc.title;
    });

    // FIX باگ ۳: cfg.organization نه cfg.organizations
    (cfg.organization || []).forEach(function (org) {
        var daysArr = org.days
            ? org.days.toString().split(',')
                .map(function (d) { return parseInt(d, 10); })
                .filter(function (n) { return !isNaN(n); })
            : [];
        orgMap[org.value] = { title: org.title, days: daysArr };
    });

    // ============================================================
    // CONSTANTS — همه اینجا تعریف می‌شوند (قبل از هر استفاده‌ای)
    // FIX باگ ۱: SUMMARY_STEP قبل از STEP_ORDER تعریف شد
    // ============================================================

    var STEP_3_5     = 'step-3-5';
    var SUMMARY_STEP = 6;
    var FIRST_STEP   = 1;
    var LABOR_FEE    = 100000;
    var MAX_CARS     = 7;

    // FIX باگ ۱: حالا SUMMARY_STEP تعریف شده، خطا نمی‌دهد
    var STEP_ORDER_NORMAL = [1, 2, 3, 4, 5, SUMMARY_STEP];
    var STEP_ORDER_ORG    = [1, 2, 3, STEP_3_5, 4, 5, SUMMARY_STEP];

    // FIX باگ ۲: کلیدها باید رشته literal باشند نه نام متغیر
    // در object literal، کلید متغیر باید با [] نوشته شود
    var stepTitles = {
        1:   'انتخاب خودرو',
        2:   'انتخاب خدمات اضافه',
        3:   'اطلاعات تکمیلی',
        4:   'زمان انجام سرویس',
        5:   'تکمیل اطلاعات شخصی'
    };
    // کلید داینامیک با [] — تنها راه صحیح در ES5
    stepTitles[STEP_3_5] = 'انتخاب سازمان';

    // ============================================================
    // STATE
    // ============================================================

    var formData = {
        selected_cars:        [],
        selected_services:    [],
        info_additional_type: '',
        organization_type:    '',
        org_allowed_days:     null,
        service_date:         '',
        time_slot:            '',
        full_name:            '',
        phone:                '',
        address:              ''
    };

    var currentStep          = FIRST_STEP;
    var interactedFields     = {};
    var isSubmitting         = false;
    var selectedTimeSlot     = '12:00-16:00';
    var selectedDate         = '';
    var datePickerVm         = null;
    var isVuePluginRegistered = false;

    // ============================================================
    // DOM HELPERS
    // ============================================================

    function qs(sel)  { return document.querySelector(sel); }
    function qsa(sel) { return document.querySelectorAll(sel); }

    var formContainer   = qs('.mdotcar-form-container');
    var nextBtn         = qs('.mdotcar-next');
    var prevBtn         = qs('.mdotcar-prev');
    var cancelBtn       = qs('.mdotcar-cancel');
    var submitBtn       = qs('.mdotcar-submit');
    var navTitle        = qs('.mdotcar-nav div span:first-child');
    var navProgress     = qs('.mdotcar-nav div span:last-child');
    var stepsCompleted  = qs('.steps-completed');
    var hoursModal      = qs('.hours-modal');
    var datePickerModal = qs('.date-picker-modal');

    // ============================================================
    // NAVIGATION HELPERS
    // FIX باگ ۴: توابع قبل از استفاده تعریف شدند
    // ============================================================

    function isSummaryStep(step) {
        return step === SUMMARY_STEP;
    }

    function getStepOrder() {
        // صفحه سازمانی = URL شامل 'carwash-organization'
        return window.location.pathname.includes('carwash-organization')
            ? STEP_ORDER_ORG
            : STEP_ORDER_NORMAL;
    }

    function getNextStep(current) {
        var order = getStepOrder();
        var idx   = order.indexOf(current);
        return (idx !== -1 && idx < order.length - 1) ? order[idx + 1] : current;
    }

    function getPrevStep(current) {
        var order = getStepOrder();
        var idx   = order.indexOf(current);
        return idx > 0 ? order[idx - 1] : current;
    }

    // ============================================================
    // مرحله ۳.۵ — انتخاب سازمان
    // ============================================================

    function setupOrgStep() {
        // listener روی radio های مرحله ۳.۵
        qsa('.carwash-iorganization label').forEach(function (label) {
            label.addEventListener('click', function () {
                qsa('.carwash-iorganization label').forEach(function (l) {
                    l.classList.remove('selected', 'invalid');
                });
                label.classList.add('selected');

                var radio = label.querySelector('input[type="radio"]');
                if (!radio) return;
                radio.checked = true;

                var orgValue = radio.value;
                var daysStr  = radio.getAttribute('data-days');
                var days     = daysStr
                    ? daysStr.split(',')
                        .map(function (d) { return parseInt(d, 10); })
                        .filter(function (n) { return !isNaN(n); })
                    : null;

                formData.organization_type = orgValue;
                formData.org_allowed_days  = days;

                // sync با Vue datepicker اگر باز است
                if (datePickerVm) {
                    datePickerVm.allowedDays = days;

                    // تاریخ قبلی اگر با روزهای جدید ناسازگار است پاک کن
                    if (datePickerVm.selectedDate && days && days.length > 0) {
                        var wd = getJalaliWeekDay(datePickerVm.selectedDate);
                        if (days.indexOf(wd) === -1) {
                            datePickerVm.selectedDate = '';
                            clearDateInput();
                        }
                    }
                }

                interactedFields['organization_type'] = true;
                validateStep(STEP_3_5);
            });
        });
    }

    function validateOrgStep(force) {
        var valid = !!formData.organization_type;
        if (!valid && force) {
            qsa('.carwash-iorganization label').forEach(function (l) {
                l.classList.add('invalid');
            });
        }
        return valid;
    }

    // ============================================================
    // COUNTER — خودرو
    // ============================================================

    function getCarData(type) {
        for (var i = 0; i < formData.selected_cars.length; i++) {
            if (formData.selected_cars[i].type === type) return formData.selected_cars[i];
        }
        return null;
    }

    function updateCarCounter(carItem) {
        var type     = carItem.getAttribute('data-car-type');
        var carData  = getCarData(type);
        var count    = carData ? carData.count : 0;
        var addBtn   = carItem.querySelector('.counter-add');
        var controls = carItem.querySelector('.counter-controls');
        var valSpan  = carItem.querySelector('.counter-value');
        var minusBtn = carItem.querySelector('.counter-minus');

        if (count === 0) {
            if (addBtn)   addBtn.style.display   = 'flex';
            if (controls) controls.style.display = 'none';
            carItem.classList.remove('selected');
        } else {
            if (addBtn)   addBtn.style.display   = 'none';
            if (controls) controls.style.display = 'flex';
            if (valSpan)  valSpan.textContent    = count;
            carItem.classList.add('selected');
            if (minusBtn) {
                var img = minusBtn.querySelector('img');
                if (img) {
                    img.src = count === 1
                        ? '/wp-content/uploads/2026/02/trash.svg'
                        : '/wp-content/uploads/2026/02/minus.svg';
                    img.alt = count === 1 ? 'حذف' : 'کاهش';
                }
            }
        }
    }

    function handleCarCounterClick(btn, carItem) {
        var type    = carItem.getAttribute('data-car-type');
        var action  = btn.getAttribute('data-action');
        var carData = getCarData(type);
        var total   = formData.selected_cars.reduce(function (s, c) { return s + c.count; }, 0);

        if (action === 'add' || action === 'plus') {
            if (total >= MAX_CARS) { showCarCountError(); return; }
            if (!carData) { formData.selected_cars.push({ type: type, count: 1 }); }
            else { carData.count++; }
        } else if (action === 'minus') {
            if (carData && carData.count > 0) {
                carData.count--;
                if (carData.count === 0) {
                    formData.selected_cars = formData.selected_cars.filter(function (c) {
                        return c.type !== type;
                    });
                }
            }
        }

        updateCarCountError();
        interactedFields['selected_cars'] = true;
        updateCarCounter(carItem);
        validateStep(1);
    }

    function showCarCountError() {
        var el = qs('.car-count-error');
        if (el) el.classList.add('show');
    }

    function updateCarCountError() {
        var el    = qs('.car-count-error');
        var total = formData.selected_cars.reduce(function (s, c) { return s + c.count; }, 0);
        if (el) el.classList.toggle('show', total >= MAX_CARS);
    }

    // ============================================================
    // COUNTER — خدمات
    // ============================================================

    function getSvcData(type) {
        for (var i = 0; i < formData.selected_services.length; i++) {
            if (formData.selected_services[i].type === type) return formData.selected_services[i];
        }
        return null;
    }

    function updateServiceCounter(serviceItem) {
        var type    = serviceItem.getAttribute('data-service-type');
        var svcData = getSvcData(type);
        var count   = svcData ? svcData.count : 0;
        var addBtn   = serviceItem.querySelector('.counter-add');
        var controls = serviceItem.querySelector('.counter-controls');
        var valSpan  = serviceItem.querySelector('.counter-value');
        var minusBtn = serviceItem.querySelector('.counter-minus');

        if (count === 0) {
            if (addBtn)   addBtn.style.display   = 'flex';
            if (controls) controls.style.display = 'none';
            serviceItem.classList.remove('selected');
        } else {
            if (addBtn)   addBtn.style.display   = 'none';
            if (controls) controls.style.display = 'flex';
            if (valSpan)  valSpan.textContent    = count;
            serviceItem.classList.add('selected');
            if (minusBtn) {
                var img = minusBtn.querySelector('img');
                if (img) {
                    img.src = count === 1
                        ? '/wp-content/uploads/2026/02/trash.svg'
                        : '/wp-content/uploads/2026/02/minus.svg';
                    img.alt = count === 1 ? 'حذف' : 'کاهش';
                }
            }
        }
    }

    function handleServiceCounterClick(btn, serviceItem) {
        var type    = serviceItem.getAttribute('data-service-type');
        var action  = btn.getAttribute('data-action');
        var svcData = getSvcData(type);

        if (action === 'add' || action === 'plus') {
            if (!svcData) { formData.selected_services.push({ type: type, count: 1 }); }
            else { svcData.count++; }
        } else if (action === 'minus') {
            if (svcData && svcData.count > 0) {
                svcData.count--;
                if (svcData.count === 0) {
                    formData.selected_services = formData.selected_services.filter(function (s) {
                        return s.type !== type;
                    });
                }
            }
        }

        interactedFields['selected_services'] = true;
        updateServiceCounter(serviceItem);
    }

    function setupCounters() {
        if (!formContainer) return;

        formContainer.addEventListener('click', function (e) {
            var btn = e.target.closest('.counter-btn');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();

            var carItem     = btn.closest('.mdotcar-car-item');
            var serviceItem = btn.closest('.mdotcar-service-item');
            if (carItem)          handleCarCounterClick(btn, carItem);
            else if (serviceItem) handleServiceCounterClick(btn, serviceItem);
        });

        qsa('.mdotcar-car-item').forEach(updateCarCounter);
        qsa('.mdotcar-service-item').forEach(updateServiceCounter);
    }

    // ============================================================
    // DATE PICKER
    // ============================================================

    function getJalaliWeekDay(jDateStr) {
        if (typeof moment === 'undefined' || !jDateStr) return -1;
        var m = moment(jDateStr, 'jYYYY/jMM/jDD', true).locale('fa');
        if (!m.isValid()) return -1;
        return m.weekday(); // 0=شنبه … 6=جمعه با dow=6
    }

    function initializeDatePicker() {
        if (typeof Vue === 'undefined' || typeof VuePersianDatetimePicker === 'undefined') return;
        if (typeof moment === 'undefined') return;

        if (!isVuePluginRegistered) {
            Vue.use(VuePersianDatetimePicker, {
                name: 'DatePicker',
                props: {
                    format:        'jYYYY/jMM/jDD',
                    displayFormat: 'jYYYY/jMM/jDD',
                    locale:        'fa',
                    inline:        true,
                    input:         false,
                    color:         '#417df4',
                    showNowBtn:    false,
                    autoSubmit:    false,
                    localeConfig: {
                        fa: {
                            dow:           6,
                            weekdays:      ['شنبه','یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه'],
                            weekdaysShort: ['ش','ی','د','س','چ','پ','ج']
                        }
                    }
                }
            });
            isVuePluginRegistered = true;
        }

        if (datePickerVm) {
            datePickerVm.allowedDays = formData.org_allowed_days;
            return;
        }

        var container = qs('#date-picker-modal-app');
        if (!container) return;

        container.innerHTML =
            '<date-picker' +
            '  v-model="selectedDate"' +
            '  :min="minDate"' +
            '  :max="maxDate"' +
            '  :disable="isDisabled">' +
            '  <template #next-month>' +
            '    <img src="/wp-content/uploads/2025/09/right.svg" alt="next">' +
            '  </template>' +
            '  <template #prev-month>' +
            '    <img src="/wp-content/uploads/2025/09/left.svg" alt="prev">' +
            '  </template>' +
            '</date-picker>';

        var tomorrow  = moment().add(1, 'days').format('jYYYY/jMM/jDD');
        var endOfYear = moment().endOf('jYear').format('jYYYY/jMM/jDD');

        datePickerVm = new Vue({
            el: '#date-picker-modal-app',
            data: {
                selectedDate: formData.service_date || '',
                minDate:      tomorrow,
                maxDate:      endOfYear,
                allowedDays:  formData.org_allowed_days // reactive
            },
            methods: {
                isDisabled: function (date) {
                    if (!this.allowedDays || this.allowedDays.length === 0) return false;
                    var wd = getJalaliWeekDay(date);
                    return this.allowedDays.indexOf(wd) === -1;
                }
            },
            mounted: function () {
                selectedDate = this.selectedDate;
                this.$nextTick(function () {
                    var header = document.querySelector('.vpd-header');
                    if (header) header.remove();
                });
            },
            watch: {
                selectedDate: function (newDate) {
                    if (newDate) selectedDate = newDate;
                }
            }
        });
    }

    function clearDateInput() {
        var input = qs('[data-field="service_date"]');
        if (input) { input.value = ''; input.classList.remove('invalid'); }
        formData.service_date = '';
        selectedDate          = '';
        delete interactedFields['service_date'];
    }

    // ============================================================
    // MODALS
    // ============================================================

    function showHoursModal()      { if (hoursModal)      hoursModal.style.display      = 'block'; }
    function hideHoursModal()      { if (hoursModal)      hoursModal.style.display      = 'none';  }
    function showDatePickerModal() {
        if (datePickerModal) datePickerModal.style.display = 'block';
        setTimeout(initializeDatePicker, 100);
    }
    function hideDatePickerModal() { if (datePickerModal) datePickerModal.style.display = 'none'; }

    function saveSelectedTime() {
        if (!selectedTimeSlot) return;
        var input = qs('[data-field="service_time"]');
        var item  = qs('.Periodic-service-hours .mdotcar-time-item[data-value="' + selectedTimeSlot + '"]');
        if (input && item) {
            input.value = item.textContent;
            input.classList.remove('invalid');
            formData.time_slot            = selectedTimeSlot;
            interactedFields['time_slot'] = true;
            validateStep(4);
        }
        hideHoursModal();
    }

    function saveSelectedDate() {
        var finalDate = selectedDate || (datePickerVm && datePickerVm.selectedDate);
        if (!finalDate) return;

        var m = moment(finalDate, 'jYYYY/jMM/jDD', true);
        if (!m.isValid()) {
            var input = qs('[data-field="service_date"]');
            if (input) input.classList.add('invalid');
            return;
        }

        // دفاع دوم: اگر روز مجاز نیست
        if (formData.org_allowed_days && formData.org_allowed_days.length > 0) {
            var wd = getJalaliWeekDay(finalDate);
            if (formData.org_allowed_days.indexOf(wd) === -1) {
                var inputEl = qs('[data-field="service_date"]');
                if (inputEl) inputEl.classList.add('invalid');
                hideDatePickerModal();
                return;
            }
        }

        var input = qs('[data-field="service_date"]');
        if (input) {
            input.value = m.locale('fa').format('dddd jDD jMMMM jYYYY');
            input.classList.remove('invalid');
        }
        formData.service_date            = finalDate;
        interactedFields['service_date'] = true;
        validateStep(4);
        hideDatePickerModal();
    }

    function cancelSelectedDate() { hideDatePickerModal(); }

    // ============================================================
    // VALIDATION
    // ============================================================

    var validationRules = {};
    validationRules[1] = function () {
        return formData.selected_cars.length > 0 &&
               formData.selected_cars.reduce(function (s, c) { return s + c.count; }, 0) > 0;
    };
    validationRules[2] = function () { return true; };
    validationRules[3] = function () {
        var checked = qs('[name="info_additional_type"]:checked');
        if (checked) formData.info_additional_type = checked.value;
        return !!checked;
    };
    // FIX باگ ۲: کلید داینامیک با []
    validationRules[STEP_3_5] = function (force) {
        return validateOrgStep(force);
    };
    validationRules[4] = function () {
        if (!formData.service_date) return false;
        var min      = moment().startOf('day').add(1, 'days');
        var selected = moment(formData.service_date, 'jYYYY/jMM/jDD', true).startOf('day');
        if (!selected.isValid() || selected.isBefore(min, 'day')) return false;
        if (formData.org_allowed_days && formData.org_allowed_days.length > 0) {
            var wd = getJalaliWeekDay(formData.service_date);
            if (formData.org_allowed_days.indexOf(wd) === -1) return false;
        }
        return !!formData.time_slot;
    };
    validationRules[5] = function () {
        var fn = qs('[data-field="full_name"]');
        var ph = qs('[data-field="phone"]');
        var ad = qs('[data-field="address"]');
        var ok = true;

        if (!fn || !fn.value.trim())                        ok = false; else formData.full_name = fn.value.trim();
        if (!ph || !/^09\d{9}$/.test(ph.value.trim()))     ok = false; else formData.phone     = ph.value.trim();
        if (!ad || !ad.value.trim())                        ok = false; else formData.address   = ad.value.trim();

        return ok;
    };

    function validateStep(step, force) {
        var rule = validationRules[step];
        if (!rule) return true;

        var isValid = rule(force || false);

        if (force) {
            if (step === 1) {
                qsa('.mdotcar-car-item').forEach(function (item) {
                    var cd = getCarData(item.getAttribute('data-car-type'));
                    item.classList.toggle('invalid', !cd || cd.count === 0);
                });
            }
            if (step === 3) {
                qsa('.carwash-info-additional label').forEach(function (l) {
                    l.classList.toggle('invalid', !isValid);
                });
            }
            if (step === 4) {
                var dateInput = qs('[data-field="service_date"]');
                var timeInput = qs('[data-field="service_time"]');
                if (dateInput) dateInput.classList.toggle('invalid', !formData.service_date);
                if (timeInput) timeInput.classList.toggle('invalid', !formData.time_slot);
            }
            if (step === 5) {
                var fn = qs('[data-field="full_name"]');
                var ph = qs('[data-field="phone"]');
                var ad = qs('[data-field="address"]');
                if (fn) fn.classList.toggle('invalid', !fn.value.trim());
                if (ph) ph.classList.toggle('invalid', !/^09\d{9}$/.test(ph.value.trim()));
                if (ad) ad.classList.toggle('invalid', !ad.value.trim());
            }
        }

        return isValid;
    }

    // ============================================================
    // SHOW STEP
    // ============================================================

    function showStep(step) {
        qsa('.mdotcar-step').forEach(function (s) { s.classList.remove('active'); });

        var activeStep = qs('.mdotcar-step[data-step="' + step + '"]');
        if (activeStep) activeStep.classList.add('active');

        if (formContainer) {
            formContainer.className = formContainer.className
                .replace(/\bstep-[\w\-]+\b/g, '').trim();
            formContainer.classList.add('step-' + String(step).replace('.', '-'));
        }

        currentStep = step;

        if (step === 4) setTimeout(initializeDatePicker, 200);

        var nav       = qs('.mdotcar-nav');
        var isSummary = isSummaryStep(step);

        if (!isSummary) {
            if (nav) nav.style.display = 'block';

            var orderForDisplay = getStepOrder().filter(function (s) { return !isSummaryStep(s); });
            var stepIndex       = orderForDisplay.indexOf(step) + 1;
            var totalSteps      = orderForDisplay.length;

            if (navTitle)    navTitle.textContent    = stepTitles[step] || '';
            if (navProgress) navProgress.textContent = 'مرحله ' + stepIndex + ' از ' + totalSteps;

            qsa('.mdotcar-nav-step').forEach(function (dot) {
                var dotStep = parseInt(dot.getAttribute('data-step'), 10);
                var dotIdx  = orderForDisplay.indexOf(dotStep);
                var curIdx  = orderForDisplay.indexOf(step);
                dot.classList.toggle('completed', dotIdx !== -1 && dotIdx <= curIdx);
            });

            if (stepsCompleted) {
                stepsCompleted.className = 'steps-completed step-' + String(step).replace('.', '-');
            }

        } else {
            if (nav) nav.style.display = 'none';
            updateSummary();
        }

        if (prevBtn) {
            prevBtn.disabled = step === FIRST_STEP;
            prevBtn.classList.toggle('step-1', step === FIRST_STEP);
        }

        if (nextBtn) {
            nextBtn.style.display = isSummary ? 'none' : 'flex';
            nextBtn.disabled      = false;
        }

        if (submitBtn) {
            submitBtn.style.display = isSummary ? 'flex' : 'none';
        }

        validateStep(step);
    }

    // ============================================================
    // SUMMARY
    // ============================================================

    function updateSummary() {
        var summaryInfo     = qs('.mdotcar-summary-Information');
        var selectedService = qs('.selected-periodic-service');
        if (!summaryInfo || !selectedService) return;

        var orgTitle = formData.organization_type && orgMap[formData.organization_type]
            ? orgMap[formData.organization_type].title
            : '';

        var carsDisplay = formData.selected_cars.map(function (car) {
            return (carTypeNames[car.type] || car.type) + ' × ' + car.count;
        }).join('، ') || 'موردی انتخاب نشده';

        var servicesDisplay = formData.selected_services.map(function (s) {
            return (serviceNames[s.type] || s.type) + ' × ' + s.count;
        }).join('، ') || 'موردی انتخاب نشده';

        var timeDisplay = '';
        var timeItem = qs('.Periodic-service-hours .mdotcar-time-item[data-value="' + formData.time_slot + '"]');
        if (timeItem) timeDisplay = timeItem.textContent.trim();

        var dateDisplay = formData.service_date
            ? moment(formData.service_date, 'jYYYY/jMM/jDD').locale('fa').format('dddd jDD jMMMM jYYYY')
            : '';

        summaryInfo.innerHTML =
            '<h3>جزئیات سفارش</h3>' +
            '<div class="mdotcar-summary-details">' +
            (orgTitle ? '<div><span>سازمان</span><span>' + orgTitle + '</span></div>' : '') +
            '<div><span>نام و نام خانوادگی</span><span>' + (formData.full_name || '') + '</span></div>' +
            '<div><span>خودروها</span><span>' + carsDisplay + '</span></div>' +
            '<div><span>نوع خدمت</span><span>کارواش</span></div>' +
            '<div><span>خدمات تکمیلی</span><span>' + servicesDisplay + '</span></div>' +
            '<div><span>شماره تماس</span><span>' + (formData.phone || '') + '</span></div>' +
            '<div><span>زمان سرویس</span><span>' + dateDisplay + (timeDisplay ? '، ' + timeDisplay : '') + '</span></div>' +
            '<div><span>آدرس</span><span>' + (formData.address || '') + '</span></div>' +
            '</div>';

        var totalCars = 0;
        var carHTML   = '';
        formData.selected_cars.forEach(function (car) {
            var price = carTypePrices[car.type] || 0;
            var total = price * car.count;
            totalCars += total;
            carHTML += '<div><span>شستشو ' + (carTypeNames[car.type] || car.type) + ' × ' + car.count + '</span>' +
                       '<span>' + total.toLocaleString('fa-IR') + ' <strong>تومان</strong></span></div>';
        });

        var totalSvcs = 0;
        var svcHTML   = '';
        formData.selected_services.forEach(function (s) {
            var price = servicePrices[s.type] || 0;
            var total = price * s.count;
            totalSvcs += total;
            svcHTML += '<div><span>' + (serviceNames[s.type] || s.type) + ' × ' + s.count + '</span>' +
                       '<span>' + total.toLocaleString('fa-IR') + ' <strong>تومان</strong></span></div>';
        });

        var grandTotal = totalCars + totalSvcs + LABOR_FEE;

        selectedService.innerHTML =
            '<span>فاکتور هزینه</span>' +
            '<div class="carwash-service-items">' +
            carHTML + svcHTML +
            '<div><span>هزینه اجرت و ایاب و ذهاب</span>' +
            '<span>' + LABOR_FEE.toLocaleString('fa-IR') + ' <strong>تومان</strong></span></div>' +
            '<div><span>جمع کل</span>' +
            '<span>' + grandTotal.toLocaleString('fa-IR') + ' <strong>تومان</strong></span></div>' +
            '</div>';
    }

    // ============================================================
    // RESET
    // ============================================================

    function resetForm() {
        formData = {
            selected_cars:        [],
            selected_services:    [],
            info_additional_type: '',
            organization_type:    '',
            org_allowed_days:     null,
            service_date:         '',
            time_slot:            '',
            full_name:            '',
            phone:                '',
            address:              ''
        };

        interactedFields = {};
        isSubmitting     = false;
        selectedDate     = '';
        selectedTimeSlot = '12:00-16:00';

        qsa('.mdotcar-input').forEach(function (i) { i.value = ''; i.classList.remove('invalid'); });
        qsa('input[type="radio"]').forEach(function (r) { r.checked = false; });
        qsa('.selected').forEach(function (el) { el.classList.remove('selected'); });
        qsa('.invalid').forEach(function (el) { el.classList.remove('invalid'); });
        qsa('.mdotcar-car-item').forEach(updateCarCounter);
        qsa('.mdotcar-service-item').forEach(updateServiceCounter);

        var errorDiv = qs('.car-count-error');
        if (errorDiv) errorDiv.classList.remove('show');

        if (datePickerVm) { datePickerVm.$destroy(); datePickerVm = null; }
        var app = qs('#date-picker-modal-app');
        if (app) app.innerHTML = '';

        if (submitBtn)      { submitBtn.disabled = false; }
        if (hoursModal)       hoursModal.style.display      = 'none';
        if (datePickerModal)  datePickerModal.style.display = 'none';
    }

    function resetAndGoToFirst() {
        resetForm();
        showStep(FIRST_STEP);
    }

    // ============================================================
    // AJAX
    // ============================================================

    function sendAjax(action, onSuccess) {
        if (isSubmitting) return;
        isSubmitting = true;

        if (typeof carwash_ajax === 'undefined' || !carwash_ajax.ajaxurl || !carwash_ajax.nonce) {
            isSubmitting = false;
            alert('خطای داخلی: ارتباط با سرور برقرار نشد.');
            return;
        }

        fetch(carwash_ajax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: [
                'action=' + encodeURIComponent(action),
                'nonce='  + encodeURIComponent(carwash_ajax.nonce),
                'data='   + encodeURIComponent(JSON.stringify(formData)),
                'step='   + encodeURIComponent(String(currentStep))
            ].join('&')
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) { onSuccess(data); }
            else { alert(data.data && data.data.message ? data.data.message : 'خطایی رخ داد.'); }
        })
        .catch(function () { alert('مشکل ارتباط با سرور.'); })
        .finally(function () { isSubmitting = false; });
    }

    // ============================================================
    // EVENT LISTENERS
    // ============================================================

    function setupEventListeners() {

        // رادیو اطلاعات تکمیلی
        qsa('.carwash-info-additional label').forEach(function (label) {
            label.addEventListener('click', function (e) {
                if (e.target.closest('a')) return;
                var radio = label.querySelector('input[type="radio"]');
                if (!radio) return;
                radio.checked = true;
                qsa('.carwash-info-additional label').forEach(function (l) {
                    l.classList.remove('selected', 'invalid');
                });
                label.classList.add('selected');
                formData.info_additional_type            = radio.value;
                interactedFields['info_additional_type'] = true;
                validateStep(3);
            });
        });

        // input تاریخ و ساعت
        var serviceDateInput = qs('[data-field="service_date"]');
        if (serviceDateInput) serviceDateInput.addEventListener('click', showDatePickerModal);

        var serviceTimeInput = qs('[data-field="service_time"]');
        if (serviceTimeInput) serviceTimeInput.addEventListener('click', showHoursModal);

        // مودال ساعت
        if (hoursModal) {
            var hClose    = hoursModal.querySelector('.close-modal');
            var hCancel   = hoursModal.querySelector('.cancel-time');
            var hSave     = hoursModal.querySelector('.save-time');
            var hBackdrop = hoursModal.querySelector('.modal-backdrop');
            if (hClose)    hClose.addEventListener('click', hideHoursModal);
            if (hCancel)   hCancel.addEventListener('click', hideHoursModal);
            if (hSave)     hSave.addEventListener('click', saveSelectedTime);
            if (hBackdrop) hBackdrop.addEventListener('click', hideHoursModal);

            hoursModal.querySelectorAll('.Periodic-service-hours .mdotcar-time-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    hoursModal.querySelectorAll('.Periodic-service-hours .mdotcar-time-item').forEach(function (i) {
                        i.classList.remove('selected');
                    });
                    item.classList.add('selected');
                    selectedTimeSlot = item.getAttribute('data-value');
                });
            });
        }

        // مودال تاریخ
        if (datePickerModal) {
            var dClose    = datePickerModal.querySelector('.close-modal');
            var dCancel   = datePickerModal.querySelector('.cancel-date');
            var dSave     = datePickerModal.querySelector('.save-date');
            var dBackdrop = datePickerModal.querySelector('.modal-backdrop');
            if (dClose)    dClose.addEventListener('click', cancelSelectedDate);
            if (dCancel)   dCancel.addEventListener('click', cancelSelectedDate);
            if (dSave)     dSave.addEventListener('click', saveSelectedDate);
            if (dBackdrop) dBackdrop.addEventListener('click', cancelSelectedDate);
        }

        // inputهای متنی
        qsa('.mdotcar-input').forEach(function (input) {
            ['focus', 'input', 'change'].forEach(function (evt) {
                input.addEventListener(evt, function () {
                    var field = input.getAttribute('data-field');
                    if (field) {
                        interactedFields[field] = true;
                        validateStep(currentStep);
                    }
                });
            });
        });

        // دکمه ادامه
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (!validateStep(currentStep, true)) return;
                showStep(getNextStep(currentStep));
            });
        }

        // دکمه برگشت
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                showStep(getPrevStep(currentStep));
            });
        }

        // دکمه انصراف
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                if (currentStep === SUMMARY_STEP) {
                    sendAjax('mdotcar_cancel_carwash_form', resetAndGoToFirst);
                } else {
                    resetAndGoToFirst();
                }
            });
        }

        // دکمه ثبت نهایی
        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                if (isSubmitting) return;
                submitBtn.disabled = true;
                var textNode = submitBtn.childNodes[0];
                if (textNode && textNode.nodeType === 3) textNode.textContent = 'در حال پردازش...';

                sendAjax('mdotcar_submit_carwash_form', function () {
                    var successModal = qs('.carwash-successful-request');
                    if (successModal) successModal.style.display = 'flex';
                    submitBtn.disabled = false;
                    if (textNode && textNode.nodeType === 3) textNode.textContent = 'ثبت نهایی سرویس';
                });
            });
        }

        // بستن مودال موفقیت
        var closeSuccessBtn = qs('.close-success-modal');
        if (closeSuccessBtn) {
            closeSuccessBtn.addEventListener('click', function () {
                var successModal = qs('.carwash-successful-request');
                if (successModal) successModal.style.display = 'none';
                resetAndGoToFirst();
            });
        }
    }

    // ============================================================
    // INIT
    // ============================================================

    setupOrgStep();
    setupCounters();
    setupEventListeners();
    showStep(FIRST_STEP);

});