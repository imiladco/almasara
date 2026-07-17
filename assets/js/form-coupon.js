document.addEventListener('DOMContentLoaded', function() {
    // مدیریت حذف کد تخفیف
    var removeCouponLink = document.querySelector('.remove-coupon');
    if (removeCouponLink) {
        removeCouponLink.addEventListener('click', function(e) {
            e.preventDefault();
            var couponCode = this.getAttribute('data-coupon');
            var messageDiv = document.getElementById('coupon_message');
            if (!couponCode || !messageDiv) {
                console.error('Coupon code or message div not found');
                return;
            }

            messageDiv.style.display = 'block';
            messageDiv.style.color = 'orange';
            messageDiv.textContent = 'در حال حذف کد تخفیف...';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var data = 'action=woocommerce_remove_coupon&coupon_code=' + encodeURIComponent(couponCode) + '&security=' + encodeURIComponent('<?php echo wp_create_nonce('remove-coupon'); ?>');
            xhr.send(data);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            messageDiv.style.color = 'green';
                            messageDiv.textContent = 'کد تخفیف با موفقیت حذف شد!';
                            document.body.dispatchEvent(new Event('update_checkout'));
                        } else {
                            messageDiv.style.color = 'red';
                            messageDiv.textContent = response.data.message || 'خطا در حذف کد تخفیف';
                        }
                    } catch (e) {
                        messageDiv.style.color = 'red';
                        messageDiv.textContent = 'خطا در پردازش پاسخ سرور';
                    }
                } else {
                    messageDiv.style.color = 'red';
                    messageDiv.textContent = 'خطا در ارتباط با سرور: ' + xhr.status;
                }

                setTimeout(function() {
                    messageDiv.style.display = 'none';
                }, 3000);
            };

            xhr.onerror = function() {
                messageDiv.style.color = 'red';
                messageDiv.textContent = 'خطا در ارتباط با سرور';
                setTimeout(function() {
                    messageDiv.style.display = 'none';
                }, 3000);
            };
        });
    }
});