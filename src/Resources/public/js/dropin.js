(() => {
    let instantiate = (container) => {

        let checkout = null;
        let configuration = {}

        let _successfulFetchCallback = (dropin, data) => {
            if (data.action) {
                dropin.handleAction(data.action);
                return;
            }

            window.location.replace(data.redirect)
        }

        let onSubmitHandler = (state, dropin) => {
            const options = {
                method: 'POST',
                body: JSON.stringify(state.data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }

            fetch(configuration.path.payments, options)
                .then(response => response.json())
                .then(data => {
                    _successfulFetchCallback(dropin, data);
                })
                .catch(error => {
                    alert(error);
                })
            ;
        };

        let onAdditionalDetailsHandler = (state, dropin) => {
            const options = {
                method: 'POST',
                body: JSON.stringify(state.data),
                headers: {
                    'Content-Type': 'application/json'
                }
            };

            fetch(configuration.path.paymentDetails, options)
                .then(response => response.json())
                .then(data => {
                    _successfulFetchCallback(dropin, data);
                })
                .catch(error => {
                    alert(error);
                })
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
                onSubmit: onSubmitHandler,
                onAdditionalDetails: onAdditionalDetailsHandler
            });
        };

        configuration = JSON.parse(container.attributes['data-dropin'].value);
        checkout = init();

        checkout
            .create('dropin')
            .mount(container);
    };

    document.addEventListener('DOMContentLoaded', (e) => {
        document.querySelectorAll('.dropin-container').forEach(instantiate);
    })
})();