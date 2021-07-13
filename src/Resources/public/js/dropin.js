(() => {
    let instantiate = (container) => {

        let checkout = null;
        let configuration = {}

        let _successfulFetchCallback = (dropin, data) => {
            if (data.action) {
                dropin.handleAction(data.action);
                return;
            }

            const $form = container.closest('form');
            if ($form) {
                $form.classList.add('loading');
            }

            window.location.replace(data.redirect)
        }

        let submitHandler = (state, dropin, url) => {
            const options = {
                method: 'POST',
                body: JSON.stringify(state.data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }

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

        let disableStoredPaymentMethodHandler = (storedPaymentMethod, resolve, reject) => {
            let options = {
                method: 'DELETE'
            };

            let url = configuration.path.deleteToken.replace('_REFERENCE_', storedPaymentMethod);

            fetch(url, options)
                .then(resolve)
                .catch(reject)
            ;
        };

        let init = () => {
            return new AdyenCheckout({
                paymentMethodsResponse: configuration.paymentMethods,
                paymentMethodsConfiguration: {
                    card: {
                        hasHolderName: true,
                        holderNameRequired: true,
                        enableStoreDetails: true,
                        data: {
                            holderName: `${configuration.billingAddress.firstName} ${configuration.billingAddress.lastName}`,
                        }
                    }
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

        configuration = JSON.parse(container.attributes['data-dropin'].value);
        console.log(configuration);

        checkout = init();

        checkout
            .create('dropin', {
                showRemovePaymentMethodButton: true,
                onDisableStoredPaymentMethod: disableStoredPaymentMethodHandler
            })
            .mount(container);
    };

    document.addEventListener('DOMContentLoaded', (e) => {
        document.querySelectorAll('.dropin-container').forEach(instantiate);
    })
})();