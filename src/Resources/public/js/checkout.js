document.addEventListener('DOMContentLoaded', (e) => {

    const $ = document.querySelector.bind(document);

    let isAdyenChosen = false;

    const $paymentMethodRadio = $('form[name=sylius_checkout_select_payment] input[type=radio][value=adyen]');
    const $paymentMethodsLayer = $('.adyen-payment-container');

    const $paymentMethodRadioChangeHandler = (e) => {
        isAdyenChosen = e.currentTarget.getAttribute('checked');
        if(isAdyenChosen){
            $paymentMethodsLayer.style.display = '';
        }else{
            $paymentMethodsLayer.style.display = 'none';
        }
    }

    const initialize = () => {
        $paymentMethodRadio.addEventListener('change', $paymentMethodRadioChangeHandler)
        $paymentMethodRadio.dispatchEvent(
            new Event('change')
        );
    }

    initialize();
});