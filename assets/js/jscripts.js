document.addEventListener('DOMContentLoaded', function() {
    // برسی وضعیت کاربر
    var userStatus = localStorage.getItem('userVerificationStatus');
    if (!userStatus) {
        jQuery.ajax({
            url: ajaxurl, // باید از ajax_object.ajaxurl استفاده کنید
            type: 'post',
            data: { action: 'check_verification_status' },
            success: function(response) {
                var status = response.data.status;
                localStorage.setItem('userVerificationStatus', status);
                handleUserStatus(status);
            }
        });
    } else {
        handleUserStatus(userStatus);
    }

    // مدیریت کلیک بر روی دکمه‌های افزودن به سبد خرید
    jQuery('.add_to_cart_button').on('click', function(e) {
        e.preventDefault();
        var button = this;
        var userStatus = localStorage.getItem('userVerificationStatus');
        handleUserStatus(userStatus, button);
    });
});

function handleUserStatus(status, button = null) {
    switch(status) {
        case 'verified':
            if (button) window.location.href = button.href;
            break;
        case 'not_verified':
            showModal('شما تایید نشده‌اید. لطفاً ابتدا حساب خود را تایید کنید.');
            break;
        case 'pending':
            showModal('وضعیت تایید شما در حال بررسی است. لطفاً صبر کنید.');
            break;
        case 'guest':
        default:
            showModal('برای خرید، ابتدا وارد حساب کاربری خود شوید.');
            break;
    }
}

function showModal(message) {
    var modal = document.getElementById('verifyModal');
    if (modal) {
        modal.querySelector('.modal-message').innerText = message;
        modal.style.display = 'flex';
    } else {
        console.error('Modal element not found');
    }
}

function closeModal() {
    var modal = document.getElementById('verifyModal');
    if (modal) {
        modal.style.display = 'none';
    } else {
        console.error('Modal element not found');
    }
}
