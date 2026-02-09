<?php
class Brevo_Subscription {

    private $apiInstance;

    private $apiKey = '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';
    function __construct() {
        $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);

        $this->apiInstance = new SendinBlue\Client\Api\ContactsApi(
            new GuzzleHttp\Client(),
            $config
        );

        add_action( 'woocommerce_created_customer', [$this, 'subscribe_new_customer_to_marketing_list'], 100, 3 );
        add_action( 'woocommerce_new_order', [$this, 'subscribe_to_order_list'], 100, 2 );

    }

    function subscribe_to_order_list($order_id, $order): void
    {
        $customer_email = $order->get_billing_email();
        $this->addContactToList($customer_email, 11);
    }

    function addContactToList($email, int $listId): void
    {
        try {
            // The user was existed, We will move the user to the list
            $userContact = $this->apiInstance->getContactInfo($email);
            $contactIdentifiers = new \SendinBlue\Client\Model\AddContactToList();
            $contactIdentifiers['emails'] = array($email);
            $this->apiInstance->addContactToList($listId, $contactIdentifiers);


        } catch (Exception $e) {

            // If user didn't exist, we will create contact and add to the list, the code is 404
            if ( $e->getCode() === 404 ) {
                // Create and add to list
                $createContact = new \SendinBlue\Client\Model\CreateContact(); // Values to create a contact
                $createContact['email'] = $email;
                $createContact['listIds'] = [$listId];
                try {
                    $this->apiInstance->createContact($createContact);
                } catch (Exception $e) {
                    echo 'Exception when calling ContactsApi->getContactInfo: ', $e->getMessage(), PHP_EOL;
                }
            }
        }
    }

    function subscribe_new_customer_to_marketing_list($customer_id, $new_customer_data, $password_generated): void
    {
        if(isset($_POST['receive_newsletters'])) {
            $this->addContactToList($new_customer_data['user_email'], 6);
        }
    }
}


