/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

document.addEventListener('DOMContentLoaded', (e) => {

    const $form = document.querySelector('form[name=sylius_checkout_select_payment]');
    if(!$form){
        return;
    }

    const $paymentMethods = $form.querySelectorAll(' input[type=radio]');
    const $adyenLayers = $form.querySelectorAll('.adyen-method-grid, .dropin-container');
    let $paymentSubmit = null;
    let isAdyenSelected = false;

    const showAdyenGrid = (code) => {
        const adyenMethod = $form.querySelector('[data-code=' + code + ']');

        if (!adyenMethod) {
            return;
        }

        isAdyenSelected = true;
        $paymentSubmit.classList.add('adyen');
        adyenMethod.querySelector('.adyen-method-grid, .dropin-container').style.display = '';
    }

    const hideAdyen = () => {
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

    const init = () => {
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