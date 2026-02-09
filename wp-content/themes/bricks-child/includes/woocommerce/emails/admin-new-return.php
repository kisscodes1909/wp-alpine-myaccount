<?php
class WC_Admin_New_Return extends WC_Email {
    protected string $return_id;

    public function __construct() {
        // Set up ID, title, and description
        $this->id             = 'admin_new_return';
        $this->title          = 'New Return Request Notification for Admin';
        $this->description    = 'An email sent to the admin when a new return request is made by a customer.';

        // Define the email heading and subject
        $this->heading        = 'New Return Request Received';
        $this->subject        = 'A New Return Request has been Submitted';

        // Specify the location and load the email template for HTML emails
        $this->template_html  = 'emails/admin-new-return.php';

        // Trigger the email send
        add_action('send_admin_new_return_notification', array($this, 'trigger'), 10, 3);

        parent::__construct();

        $this->email_type = 'html';

        // Initialize email settings options
        $this->init_form_fields();
    }

    // Trigger function to send the email
    public function trigger($order_id, $order = false, $requestId = false) {
        if (!$order_id) {
            return;
        }

        // Get the order object
        $this->object = wc_get_order($order_id);

        if (!$this->object) {
            return;
        }

        if ($order) {
            $this->object = $order;
        }

        // Set the return request ID
        $this->setReturnId($requestId);

        // Retrieve the recipient email address from the settings
        $this->recipient = $this->get_option('recipient', get_option('admin_email'));

        // Check if email is enabled and recipient is set
        if (!$this->is_enabled() || !$this->get_recipient()) {
            return;
        }

        // Send the email
        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    // Initialize the form fields for email settings
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'         => 'Activate',
                'type'          => 'checkbox',
                'label'         => 'Activate this notification',
                'default'       => 'no',
            ),
            'recipient' => array(
                'title'       => 'Recipient',
                'type'        => 'text',
                'description' => 'Enter the recipient email address for notification emails.',
                'default'     => get_option('admin_email'),
            ),
            // Additional options can be added here
        );
    }

    // Retrieve the HTML content of the email
    public function get_content_html() {
        ob_start();
        // Process and fetch data for the email template here...
        wc_get_template($this->template_html, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'plain_text'    => false,
            'sent_to_admin' => true,
            'email'         => $this,
            'return_data'   => get_return_request_by_id($this->object->get_id(), $this->getReturnId())
        ));
        return ob_get_clean();
    }

    // Get the return request ID
    public function getReturnId() {
        return $this->return_id;
    }

    // Set the return request ID
    public function setReturnId($return_id): void {
        $this->return_id = $return_id;
    }
}

