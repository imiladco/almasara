/**
 * Car Wash Admin — JavaScript پنل مدیریت
 * Version: 2.0.0
 *
 * File: assets/js/car-wash-admin.js
 */

jQuery(document).ready(function ($) {

    if (typeof carwash_admin === 'undefined') {
        console.error('carwash_admin object is not defined');
        return;
    }

    // ============================================================
    // به‌روزرسانی وضعیت سفارش
    // ============================================================

    $(document).on('click', '.update-call-center-status', function (e) {
        e.preventDefault();

        var orderId = $(this).data('order-id');
        var source  = $(this).data('source');

        Swal.fire({
            title: 'تغییر وضعیت سفارش',
            text: 'وضعیت جدید را انتخاب کنید:',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'تایید شده',
            denyButtonText: 'در حال بررسی',
            cancelButtonText: 'کنسل شده',
            reverseButtons: true
        }).then(function (result) {
            var status    = '';
            var needReason = false;

            if (result.isConfirmed) {
                status = 'completed';
            } else if (result.isDenied) {
                status = 'pending';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                status    = 'canceled_after_call';
                needReason = true;
            }

            if (status === '') return;

            if (needReason) {
                Swal.fire({
                    title: 'دلیل کنسلی',
                    input: 'textarea',
                    inputLabel: 'لطفا دلیل کنسل شدن را وارد کنید:',
                    inputPlaceholder: 'دلیل...',
                    showCancelButton: true,
                    confirmButtonText: 'ثبت',
                    cancelButtonText: 'انصراف',
                    inputValidator: function (value) {
                        if (!value) return 'لطفا دلیل را وارد کنید!';
                    }
                }).then(function (reasonResult) {
                    if (reasonResult.isConfirmed) {
                        updateOrderStatus(orderId, status, source, reasonResult.value);
                    }
                });
            } else {
                updateOrderStatus(orderId, status, source, '');
            }
        });
    });

    // ============================================================
    // حذف سفارش
    // ============================================================

    $(document).on('click', '.delete-order', function (e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');

        Swal.fire({
            title: 'حذف سفارش؟',
            text: 'آیا مطمئن هستید که می‌خواهید این سفارش را حذف کنید؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، حذف شود',
            cancelButtonText: 'انصراف',
            confirmButtonColor: '#d33',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                deleteOrder(orderId);
            }
        });
    });

    // ============================================================
    // جستجو در جدول (اختیاری)
    // ============================================================

    $('#search-orders').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // ============================================================
    // فیلتر بر اساس تاریخ (اختیاری)
    // ============================================================

    $('#filter-date').on('change', function () {
        var selectedDate = $(this).val();
        $('table tbody tr').each(function () {
            var rowDate = $(this).find('td:nth-child(2)').text().trim();
            $(this).toggle(selectedDate === '' || rowDate.includes(selectedDate));
        });
    });

    // ============================================================
    // توابع داخلی
    // ============================================================

    function updateOrderStatus(orderId, status, source, reason) {
        showLoading();

        $.ajax({
            url:  carwash_admin.ajaxurl,
            type: 'POST',
            data: {
                action:   'mdotcar_update_call_center_status_carwash',
                nonce:    carwash_admin.nonce,
                order_id: orderId,
                status:   status,
                source:   source,
                reason:   reason
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'موفق!',
                        text: response.data.message || 'وضعیت با موفقیت به‌روزرسانی شد',
                        confirmButtonText: 'باشه'
                    }).then(function () { location.reload(); });
                } else {
                    showError(response.data && response.data.message ? response.data.message : 'خطایی رخ داد');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                showError('خطا در ارتباط با سرور');
            }
        });
    }

    function deleteOrder(orderId) {
        showLoading();

        $.ajax({
            url:  carwash_admin.ajaxurl,
            type: 'POST',
            data: {
                action:   'mdotcar_delete_carwash_order',
                nonce:    carwash_admin.nonce,
                order_id: orderId
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'حذف شد!',
                        text: 'سفارش با موفقیت حذف شد',
                        confirmButtonText: 'باشه'
                    }).then(function () { location.reload(); });
                } else {
                    showError(response.data && response.data.message ? response.data.message : 'خطایی رخ داد');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                showError('خطا در ارتباط با سرور');
            }
        });
    }

    function showLoading() {
        Swal.fire({
            title: 'در حال پردازش...',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); }
        });
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'خطا!',
            text: message,
            confirmButtonText: 'باشه'
        });
    }

});
