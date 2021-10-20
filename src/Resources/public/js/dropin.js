/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

(() => {
    const instantiate = ($container) => {

        let checkout = null;
        let configuration = {}
        const _showLoader = (show) => {
            const $form = $container.closest('form');
            show ? $form.classList.add('loading') : $form.classList.remove('loading');
        }

        const _successfulFetchCallback = (dropin, data) => {
            if (data.action) {
                _showLoader(false);
                dropin.handleAction(data.action);
                return;
            }

            window.location.replace(data.redirect)
        }

        const submitHandler = (state, dropin, url) => {
            const options = {
                method: 'POST',
                body: JSON.stringify(state.data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }

            _showLoader(true);

            fetch(url, options)
                .then(response => response.json())
                .then(data => {
                    _successfulFetchCallback(dropin, data);
                })
                .catch(error => {
                    alert(error);
                })
            ;
        };

        const disableStoredPaymentMethodHandler = (storedPaymentMethod, resolve, reject) => {
            const options = {
                method: 'DELETE'
            };

            let url = configuration.path.deleteToken.replace('_REFERENCE_', storedPaymentMethod);

            fetch(url, options)
                .then(resolve)
                .catch(reject)
            ;
        };

        const init = () => {
            return new AdyenCheckout({
                paymentMethodsResponse: configuration.paymentMethods,
                paymentMethodsConfiguration: {
                    card: {
                        hasHolderName: true,
                        holderNameRequired: true,
                        enableStoreDetails: configuration.canBeStored,
                        data: {
                            holderName: `${configuration.billingAddress.firstName} ${configuration.billingAddress.lastName}`,
                        }
                    },
                    paypal: {
                        environment: configuration.environment,
                        countryCode: configuration.billingAddress.countryCode,
                        amount: {
                            currency: configuration.amount.currency,
                            value: configuration.amount.value
                        }
                    },
                },
                clientKey: configuration.clientKey,
                locale: configuration.locale,
                environment: configuration.environment,
                showRemovePaymentMethodButton: true,

                onSubmit: (state, dropin) => {
                    submitHandler(state, dropin, configuration.path.payments)
                },
                onAdditionalDetails: (state, dropin) => {
                    submitHandler(state, dropin, configuration.path.paymentDetails)
                }
            });
        };

        configuration = JSON.parse($container.attributes['data-dropin'].value);

        checkout = init();

        checkout
            .create('dropin', {
                showRemovePaymentMethodButton: true,
                onDisableStoredPaymentMethod: disableStoredPaymentMethodHandler
            })
            .mount($container);
    };

    document.addEventListener('DOMContentLoaded', (e) => {
        document.querySelectorAll('.dropin-container').forEach(instantiate);
    })
})();