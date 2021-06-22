document.addEventListener('DOMContentLoaded', (e) => {

    let $form = document.querySelector('form[name=sylius_checkout_select_payment]');
    let $paymentMethods = $form.querySelectorAll(' input[type=radio]');
    let $adyenLayers = $form.querySelectorAll('.adyen-method-grid');
    let $paymentSubmit = null;
    let isAdyenSelected = false;

    let showAdyenGrid = (code) => {
        let adyenMethod = $form.querySelector('[data-code=' + code + ']');

        if (adyenMethod) {
            isAdyenSelected = true;
            $paymentSubmit.disabled = true;
            $paymentSubmit.classList.remove('primary');
            adyenMethod.querySelector('.adyen-method-grid').style.display = '';
        }
    }

    let hideAdyen = () => {
        isAdyenSelected = false;

        $paymentSubmit.disabled = false;
        $paymentSubmit.classList.add('primary')

        $adyenLayers.forEach((adyenLayer) => {
            adyenLayer.style.display = 'none';
        });
    }

    $paymentMethods.forEach(($paymentMethod) => {
        $paymentMethod.addEventListener('change', (e) => {
            hideAdyen();
            showAdyenGrid(e.currentTarget.value);
        });
    });

    let init = () => {
        $paymentSubmit = $form.querySelector('#next-step, #sylius-pay-link')

        hideAdyen();
        $paymentMethods.forEach(($paymentMethod) => {
            if ($paymentMethod.checked) {
                showAdyenGrid($paymentMethod.value);
            }
        });
    }

    init();
});