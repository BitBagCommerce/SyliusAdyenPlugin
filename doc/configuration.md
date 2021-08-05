## Configuration

1. Obtain an [Adyen](https://adyen.com) account. If you want to test the gateway, [register a test account](https://www.adyen.com/signup/).

2. Head to API Credentials page:
   
   ![API Credentials](adyen-api-credentials.png)
   
3. Choose an existing merchant account or create new.

4. Generate an API key + client key:
   
   ![API + client key](adyen-api-keys.png)
   
5. Add an origin; type your shop URL:
   
   ![Origin](adyen-allowed-origins.png)

6. Create a new Adyen payment method. Type a merchant account, API and client key obtained in step 4. Also, create a username and password to be used for notifications credentials. Save payment method:

   ![Adyen payment method](payment-method-form.png)

   Don't forget to choose a proper environment: `test` or `live`.   

7. Come back to the Adyen panel, create a standard webhook:
   
   ![Webhook](adyen-webhooks.png)
   ![Standard webhook](adyen-webhook-type.png)

8. Type username password created in step 6:

   ![Credentials](adyen-webhook-authentication.png)

9. Expand `Additional settings section` and generate HMAC key:

   ![HMAC](adyen-webhook-hmac.png)

10. Back to the payment method configuration and paste previously generated HMAC key.

11. Save payment method.

12. If you open a payment method again, additional box is being displayed. Copy the URL and paste here in Adyen panel:

   ![Adyen notifications endpoint](notifications-endpoint.png)
   ![Adyen webhook URL](adyen-webhook-hmac.png)
 
13. Now you're ready to save and test the webhook notification. If everything goes green, you're done and ready to go.