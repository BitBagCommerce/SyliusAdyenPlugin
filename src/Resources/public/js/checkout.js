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
    const $selectorSubmitButton = $form.querySelector('#sylius-pay-link');

    const toggleSubmitButton = (show) => {
        if(!$selectorSubmitButton){
            return;
        }

        const classList = $selectorSubmitButton.classList;
        show ? classList.remove('hidden') : classList.add('hidden');
    }

    const showAdyenGrid = (code) => {
        const adyenMethod = $form.querySelector('[data-code=' + code + ']');

        if (!adyenMethod) {
            return;
        }

        toggleSubmitButton(false);

        adyenMethod.querySelector('.adyen-method-grid, .dropin-container').classList.remove('hidden');
    }

    const hideAdyen = () => {
        toggleSubmitButton(true);

        $adyenLayers.forEach((adyenLayer) => {
            adyenLayer.classList.add('hidden');
        });
    }

    const init = () => {

        if($selectorSubmitButton){
            $selectorSubmitButton.classList.add('adyen-submit');
        }

        hideAdyen();

        $paymentMethods.forEach(($paymentMethod) => {
            $paymentMethod.addEventListener('change', (e) => {
                hideAdyen();
                showAdyenGrid(e.currentTarget.value);
            });

            if ($paymentMethod.checked) {
                showAdyenGrid($paymentMethod.value);
            }
        });
    }

    init();
});
