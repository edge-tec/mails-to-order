/* Server Provisioning System — Main Client JS */

document.addEventListener('DOMContentLoaded', function () {
    // Payment method selector toggle
    const paymentRadios = document.querySelectorAll('input[name="payment_method_code"]');
    if (paymentRadios.length > 0) {
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.payment-instructions').forEach(el => el.classList.add('d-none'));
                const selectedMethod = this.value;
                const targetBox = document.getElementById('instructions-' + selectedMethod);
                if (targetBox) {
                    targetBox.classList.remove('d-none');
                }
            });
        });
    }
});

function copyToClipboard(text, btnElement) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHTML = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fa-solid fa-check text-success"></i> Copied!';
        setTimeout(() => {
            btnElement.innerHTML = originalHTML;
        }, 2000);
    }).catch(err => {
        alert('Failed to copy text: ' + err);
    });
}

function revealServerPassword(serverId, csrfToken, targetElementId) {
    const el = document.getElementById(targetElementId);
    if (!el) return;

    if (el.dataset.revealed === "true") {
        el.innerText = '••••••••••••';
        el.dataset.revealed = "false";
        return;
    }

    fetch('/ajax/reveal-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body: `_csrf_token=${encodeURIComponent(csrfToken)}&server_id=${encodeURIComponent(serverId)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            el.innerText = data.password;
            el.dataset.revealed = "true";
        } else {
            alert(data.message || 'Could not retrieve password.');
        }
    })
    .catch(err => {
        alert('Error communicating with server.');
    });
}
