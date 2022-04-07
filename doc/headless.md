## Headless integration

### Introduction
As the endpoint exposed by the plugin generate JSON responses, it's possible to integrate the plugin with an arbitrary frontend of your choice. This means, it's possible to use it in a headless environment.

> :warning: **Notice**
>
> The plugin is not based on the standard Payum flow. Internally, it manipulates the state machine changes by other means. While from the frontend point of view it's not important what happens underneath, it's vital to understand that there are no standard Payum/Sylius endpoints to handle the transactions.

### Flow
The following scheme presents the general flow that the plugin utilizes to carry out the transaction process.

```
Fetching the dropin data
Initializing the transaction
Being redirect back to Sylius
```

#### Fetching the dropin data

The following endpoint is responsible for fetching the dropin's configuration:

`/payment/adyen/{code}/{orderToken}`

Params:

| **Param** | **Description** |
|-------|-----------------|
| code  | payment method code|
| orderToken | the current order's token value |

Sample response:

```json
{
  "billingAddress": {
    "firstName": "Test",
    "lastName": "Test",
    "countryCode": "PL",
    "province": null,
    "city": "Test",
    "postcode": "12-345"
  },
  "paymentMethods": {
    "paymentMethods": [
      {
        "issuers": [
          {
            "id": "66",
            "name": "Bank Nowy S.A."
          },
          {
            "id": "92",
            "name": "Bank Spółdzielczy w Brodnicy"
          },
          {
            "id": "11",
            "name": "Bank transfer / postal"
          },
          {
            "id": "74",
            "name": "Banki Spółdzielcze"
          },
          {
            "id": "73",
            "name": "BLIK"
          },
          {
            "id": "90",
            "name": "BNP Paribas - płacę z Pl@net"
          },
          {
            "id": "59",
            "name": "CinkciarzPAY"
          },
          {
            "id": "87",
            "name": "Credit Agricole PBL"
          },
          {
            "id": "76",
            "name": "Getin Bank PBL"
          },
          {
            "id": "81",
            "name": "Idea Cloud"
          },
          {
            "id": "7",
            "name": "ING Corporate customers"
          },
          {
            "id": "93",
            "name": "Kasa Stefczyka"
          },
          {
            "id": "44",
            "name": "Millennium - Płatności Internetowe"
          },
          {
            "id": "10",
            "name": "Millennium Corporate customers"
          },
          {
            "id": "68",
            "name": "mRaty"
          },
          {
            "id": "1",
            "name": "mTransfer"
          },
          {
            "id": "91",
            "name": "Nest Bank"
          },
          {
            "id": "80",
            "name": "Noble Pay"
          },
          {
            "id": "45",
            "name": "Pay with Alior Bank"
          },
          {
            "id": "36",
            "name": "Pekao24Przelew"
          },
          {
            "id": "70",
            "name": "Pocztowy24"
          },
          {
            "id": "6",
            "name": "Przelew24"
          },
          {
            "id": "46",
            "name": "Płacę z Citi Handlowy"
          },
          {
            "id": "38",
            "name": "Płacę z ING."
          },
          {
            "id": "2",
            "name": "Płacę z Inteligo"
          },
          {
            "id": "4",
            "name": "Płacę z iPKO"
          },
          {
            "id": "75",
            "name": "Płacę z Plus Bank"
          },
          {
            "id": "51",
            "name": "Płać z BOŚ"
          },
          {
            "id": "55",
            "name": "Raty z Alior Bankiem PLN"
          },
          {
            "id": "89",
            "name": "Santander"
          },
          {
            "disabled": true,
            "id": "83",
            "name": "EnveloBank"
          },
          {
            "disabled": true,
            "id": "50",
            "name": "Pay Way Toyota Bank"
          },
          {
            "disabled": true,
            "id": "52",
            "name": "SkyCash"
          }
        ],
        "name": "Szybkie przelewy online oraz BLIK",
        "type": "dotpay"
      },
      {
        "brands": [
          "visa",
          "mc",
          "amex",
          "maestro",
          "diners",
          "discover"
        ],
        "name": "Karta kredytowa",
        "type": "scheme"
      },
      {
        "brands": [
          "amex",
          "discover",
          "maestro",
          "mc",
          "visa"
        ],
        "configuration": {
          "merchantId": "XXXXXXXXXX",
          "merchantName": "XXXXXXXXXX"
        },
        "name": "Apple Pay",
        "type": "applepay"
      },
      {
        "name": "Blik",
        "type": "blik"
      },
      {
        "configuration": {
          "merchantId": "0",
          "gatewayMerchantId": "XXXXXXXXXX"
        },
        "name": "Google Pay",
        "type": "paywithgoogle"
      }
    ]
  },
  "clientKey": "XXXXXXXXXX",
  "locale": "pl_PL",
  "environment": "test",
  "canBeStored": false,
  "amount": {
    "currency": "USD",
    "value": 6779
  },
  "path": {
    "payments": "/pl_PL/payment/adyen/ADY",
    "paymentDetails": "/pl_PL/payment/adyen/details?code=ADY",
    "deleteToken": "/pl_PL/payment/adyen/ADY/token/_REFERENCE_"
  },
  "translations": {
    "bitbag_sylius_adyen_plugin.runtime.payment_failed_try_again": "Payment failed, please try again"
  }
}
```

This configuration can be further injected into the dropin configuration provided by [Adyen itself](https://docs.adyen.com/online-payments/web-drop-in)

#### Initializing the transaction

/payment/adyen/{code} - Carries out the payment. It fetches the order from the request itself with the given payment method code.

Params:

| **Param** | **Description** |
|-------|-----------------|
| code  | payment method code|

#### Being redirect back to Sylius

/payment/adyen/{code}/thanks - Custom thank-you page, that needs to be intercepted.

Params:

| **Param** | **Description** |
|-------|-----------------|
| code  | payment method code|

