(() => {
    let checkout = null;
    let configuration = {}

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
                if (data.action) {
                    dropin.handleAction(data.action);
                } else if (data.redirect) {
                    window.location.replace(data.redirect);
                }
            })
            .catch(error => {
                throw Error(error);
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
            .then(response => {
                if (response.action) {
                    dropin.handleAction(response.action);
                } else {
                    window.location.replace(configuration.path.thankYou)
                }
            })
            .catch(error => {
                throw Error(error);
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
                        billingAddress: {
                            street: configuration.billingAddress.street,
                            postalCode: configuration.billingAddress.postCode,
                            city: configuration.billingAddress.city,
                            country: configuration.billingAddress.countryCode,
                            stateOrProvince: configuration.billingAddress.province
                        }
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

    document.addEventListener('DOMContentLoaded', (e) => {

        const container = document.querySelector('#dropin-container');
        configuration = JSON.parse(container.attributes['data-dropin'].value);
        checkout = init();

        checkout
            .create('dropin')
            .mount('#dropin-container');
    })
})();