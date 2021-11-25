/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

(() => {
    const instantiate = async ($container) => {

        let checkout = null;
        let configuration = {};
        let $form = $container.closest('form');

        const _toggleLoader = (show) => {
            const $form = $container.closest('form');
            show ? $form.classList.add('loading') : $form.classList.remove('loading');
        }

        const _loadConfiguration = async (url) => {
            _toggleLoader(true);
            const request = await fetch(url);
            const configuration = await request.json();
            _toggleLoader(false);

            if (typeof configuration['redirect'] == 'string') {
                _toggleLoader(true);
                window.location.replace(configuration['redirect']);
            }

            return configuration;
        }

        const _successfulFetchCallback = (dropin, data) => {
            if (data.action) {
                _toggleLoader(false);
                dropin.handleAction(data.action);
                return;
            }

            window.location.replace(data.redirect)
        }

        const _onSubmitHandler = (e) => {
            if ($container.classList.contains('hidden')) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();
        };

        const submitHandler = (state, dropin, url) => {
            const options = {
                method: 'POST',
                body: JSON.stringify(state.data),
                headers: {
                    'Content-Type': 'application/json'
                }
            }

            _toggleLoader(true);

            fetch(url, options)
                .then((response) => {
                    if(response.status>=400 && response.status<600){
                        return Promise.reject(response.body());
                    }

                    return Promise.resolve(response.json())
                })
                .then(data => {
                    _successfulFetchCallback(dropin, data);
                })
                .catch(error => {
                    alert(configuration.translations['bitbag_sylius_adyen_plugin.runtime.payment_failed_try_again']);
                    _toggleLoader(false);
                })
            ;
        };

        const injectOnSubmitHandler = () => {

            if (!$form) {
                return;
            }

            const $buttons = $form.querySelectorAll('[type=submit]');

            $form.addEventListener('submit', _onSubmitHandler, true);

            $buttons.forEach(($btn) => {
                $btn.addEventListener('click', _onSubmitHandler, true);
            });
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
            injectOnSubmitHandler();

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

        configuration = await _loadConfiguration($container.attributes['data-config-url'].value);
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
