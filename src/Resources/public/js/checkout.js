document.addEventListener('DOMContentLoaded', (e) => {

    let $form = document.querySelector('form[name=sylius_checkout_select_payment]');
    if(!$form){
        return;
    }

    let $paymentMethods = $form.querySelectorAll(' input[type=radio]');
    let $adyenLayers = $form.querySelectorAll('.adyen-method-grid, .dropin-container');
    let $paymentSubmit = null;
    let isAdyenSelected = false;

    let showAdyenGrid = (code) => {
        let adyenMethod = $form.querySelector('[data-code=' + code + ']');

        if (!adyenMethod) {
            return;
        }

        isAdyenSelected = true;
        $paymentSubmit.classList.add('adyen');
        adyenMethod.querySelector('.adyen-method-grid, .dropin-container').style.display = '';
    }

    let hideAdyen = () => {
        isAdyenSelected = false;

        $paymentSubmit.classList.remove('adyen')

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