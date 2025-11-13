'use strict';

document.addEventListener('DOMContentLoaded', function (event) {
  (function () {
    const priceDurationToggler = document.querySelector('.price-duration-toggler'),
      priceMonthlyList = [].slice.call(document.querySelectorAll('.price-monthly')),
      priceYearlyList = [].slice.call(document.querySelectorAll('.price-yearly'));

    function togglePrice() {
      if (priceDurationToggler.checked) {
        // If checked
        priceYearlyList.map(function (yearEl) {
          yearEl.classList.remove('d-none');
        });
        priceMonthlyList.map(function (monthEl) {
          monthEl.classList.add('d-none');
        });
      } else {
        // If not checked
        priceYearlyList.map(function (yearEl) {
          yearEl.classList.add('d-none');
        });
        priceMonthlyList.map(function (monthEl) {
          monthEl.classList.remove('d-none');
        });
      }
    }
    // togglePrice Event Listener
    togglePrice();

    priceDurationToggler.onchange = function () {
      togglePrice();
    };
  })();
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('div.modal[id^="buyModal"]').forEach(function(modalEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {

            const triggerButton         = event.relatedTarget;
            const productTime           = triggerButton?.dataset?.productTime ?? modalEl.dataset.productTime;
            const productUuid           = triggerButton?.dataset?.productUuid ?? modalEl.dataset.productUuid;
            const methodSelect          = modalEl.querySelector('#payment_method_' + productUuid);
            const installmentsSelect    = modalEl.querySelector('#payment_installments_' + productUuid);

            if (!methodSelect || !installmentsSelect) return;

            function populateInstallments(method) {
                if (method === 'PIX') {
                    installmentsSelect.innerHTML = '<option value="1">1x</option>';
                    installmentsSelect.disabled = true;
                } else if (method === 'CREDIT_CARD') {
                    let max = 1;
                    switch (productTime) {
                        case 'monthly': max = 1; break;
                        case 'semi-annual': max = 6; break;
                        case 'yearly': max = 12; break;
                        case 'lifetime': max = 12; break;
                        default: max = 1;
                    }

                    let options = '';
                    for (let i = 1; i <= max; i++) {
                        options += `<option value="${i}">${i}x</option>`;
                    }
                    installmentsSelect.innerHTML = options;
                    installmentsSelect.disabled = false;
                } else {
                    
                    installmentsSelect.innerHTML = '<option value="1">1x</option>';
                    installmentsSelect.disabled = true;
                }
            }

            populateInstallments(methodSelect.value);
            const onMethodChange = function () {
                populateInstallments(this.value);
            };

            methodSelect.addEventListener('change', onMethodChange);
            const onHidden = function () {
                methodSelect.removeEventListener('change', onMethodChange);
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
            };

            modalEl.addEventListener('hidden.bs.modal', onHidden);
        });
    });
});