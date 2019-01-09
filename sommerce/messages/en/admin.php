<?php
/**
 * Admin Translation map for en
 */
return [
    'header.menu_orders' => 'Orders',
    'header.menu_payments' => 'Payments',
    'header.menu_products' => 'Products',
    'header.menu_settings' => 'Settings',
    'header.menu_account' => 'Account',
    'header.menu_logout' => 'Logout',
    'header.menu_settings_general' => 'General',
    'header.menu_settings_payments' => 'Payments',
    'header.menu_settings_providers' => 'Providers',
    'header.menu_settings_navigation' => 'Navigation',
    'header.menu_settings_pages' => 'Pages',
    'header.menu_settings_themes' => 'Themes',
    'header.menu_settings_blocks' => 'Blocks',
    'header.menu_settings_languages' => 'Languages',

    'orders.page_title' => 'Orders',
    'orders.search_placeholder' => 'Search for...',
    'orders.filter_status_all' => 'All orders',
    'orders.filter_status_awaiting' => 'Awaiting',
    'orders.filter_status_pending' => 'Pending',
    'orders.filter_status_in_progress' => 'In progress',
    'orders.filter_status_completed' => 'Completed',
    'orders.filter_status_canceled' => 'Canceled',
    'orders.filter_status_failed' => 'Failed',
    'orders.filter_status_error' => 'Error',
    'orders.filter_product_all' => 'All',
    'orders.filter_mode_all' => 'All',
    'orders.filter_mode_manual' => 'Manual',
    'orders.filter_mode_auto' => 'Auto',
    'orders.action_title' => 'Actions',
    'orders.action_change_status' => 'Change status',
    'orders.action_resend' => 'Resend order',
    'orders.action_cancel' => 'Cancel',
    'orders.action_details' => 'Details',
    'orders.details_title' => 'Order {suborder_id} details',
    'orders.details_provider' => 'Provider',
    'orders.details_order_id' => 'Provider\'s order ID',
    'orders.details_response' => 'Provider\'s response',
    'orders.details_last_update' => 'Last update',
    'orders.message_status_changed' => 'Status was successfully changed!',
    'orders.message_canceled' => 'Order was successfully canceled!',
    'orders.message_resend' => 'Order was successfully resend!',
    'orders.message_copied' => 'Order data was successfully copied!',

    'orders.modal_change_status_message' => 'Are your sure that your want change status?',
    'orders.modal_change_status_submit' => 'Yes',
    'orders.modal_change_status_cancel' => 'No',

    'orders.modal_cancel_message' => 'Are your sure that your want cancel order?',
    'orders.modal_cancel_submit' => 'Yes',
    'orders.modal_cancel_cancel' => 'No',

    'orders.f_id' => 'ID',
    'orders.f_code' => 'Code',
    'orders.f_checkout_id' => 'Checkout ID',
    'orders.f_customer' => 'Customer',
    'orders.f_created_at' => 'Created at',

    'orders.t_id' => 'ID',
    'orders.t_customer' => 'Customer',
    'orders.t_amount' => 'Amount',
    'orders.t_link' => 'Link',
    'orders.t_product' => 'Product',
    'orders.t_quantity' => 'Quantity',
    'orders.t_status' => 'Status',
    'orders.t_date' => 'Date',
    'orders.t_mode' => 'Mode',

    'sorders.f_id' => 'ID',
    'sorders.f_order_id' => 'Order ID',
    'sorders.f_checkout_id' => 'Checkout ID',
    'sorders.f_link' => 'Link',
    'sorders.f_amount' => 'Amount',
    'sorders.f_package_id' => 'Package ID',
    'sorders.f_quantity' => 'Quantity',
    'sorders.f_status' => 'Status',
    'sorders.f_updated_at' => 'Updated At',
    'sorders.f_mode' => 'Mode',
    'sorders.f_provider_id' => 'Provider ID',
    'sorders.f_provider_service' => 'Provider Service',
    'sorders.f_provider_order_id' => 'Provider Order ID',
    'sorders.f_provider_charge' => 'Provider Charge',
    'sorders.f_provider_response' => 'Provider Response',

    'payments.page_title' => 'Payments',
    'payments.search_placeholder' => 'Search for...',
    'payments.payments_all' => 'All payments',
    'payments.status_awaiting' => 'Awaiting',
    'payments.status_completed' => 'Completed',
    'payments.status_failed' => 'Failed',
    'payments.status_refunded' => 'Refunded',
    'payments.action_title' => 'Actions',
    'payments.action_details' => 'Details',
    'payments.details_title' => 'Payment {payment_id} details',
    'payments.payment_status_1' => 'Awaiting',
    'payments.payment_status_2' => 'Completed',
    'payments.payment_status_3' => 'Failed',
    'payments.payment_status_4' => 'Refunded',
    'payments.payment_method_all' => 'All',
    'payments.payment_method_paypal' => 'Paypal',
    'payments.payment_method_2checkout' => '2Checkout',
    'payments.payment_method_bitcoin' => 'Bitcoin',
    'payments.payment_method_coinpayments' => 'CoinPayments',

    'payments.t_id' => 'ID',
    'payments.t_customer' => 'Customer',
    'payments.t_amount' => 'Amount',
    'payments.t_method' => 'Method',
    'payments.t_fee' => 'Fee',
    'payments.t_memo' => 'Memo',
    'payments.t_status' => 'Status',
    'payments.t_date' => 'Date',

    'products.page_title' => 'Products',
    'products.add_product' => 'Add product',
    'products.edit_product' => 'Edit',
    'products.add_package' => 'Add package',
    'products.edit_package' => 'Edit',
    'products.delete_package' => 'Delete',
    'products.no_products_message' => 'No products were found!',
    'products.product_title_create' => 'Create product',
    'products.product_title_edit' => 'Edit product',
    'products.product_save_title_create' => 'Add product',
    'products.product_save_title_save' => 'Save product',
    'products.product_cancel' => 'Cancel',
    'products.product_name' => 'Product name',
    'products.product_visibility' => 'Visibility',
    'products.product_visibility_enabled' => 'Enabled',
    'products.product_visibility_disabled' => 'Disabled',
    'products.product_color' => 'Color',
    'products.product_properties_title' => 'Properties',
    'products.product_properties_placeholder' => 'Add property',
    'products.product_properties_add' => 'Add',
    'products.product_properties_message' => 'Property can\'t be empty!',
    'products.product_seo_edit' => 'Edit website SEO',
    'products.product_seo_preview' => 'Search engine listing preview',
    'products.product_seo_page' => 'Page title',
    'products.product_seo_page_default' => 'Page title',
    'products.product_seo_page_chars_used' => ' of 70 characters used',
    'products.product_seo_meta' => 'Meta description',
    'products.product_seo_meta_default' => 'A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text',
    'products.product_seo_meta_chars_used' => ' of 160 characters used',
    'settings.product_seo_meta_keywords' => 'Meta keywords',
    'products.product_seo_url' => 'URL',

    'products.product_properties_copy' => ' Copy properties',
    'products.product_properties_copy_text' => 'Select the product from which you want to copy properties',
    'products.product_properties_create_new_1' => 'Create a new property or',
    'products.product_properties_create_new_2' => ' copy properties',
    'products.product_properties_create_new_3' => ' from another product',

    'products.package_create' => 'Create package',
    'products.package_edit' => 'Edit package',
    'products.package_save_create' => 'Add package',
    'products.package_save_save' => 'Save package',
    'products.package_cancel' => 'Cancel',
    'products.package_name' => 'Package name *',
    'products.package_price' => 'Price',
    'products.package_quantity' => 'Quantity',
    'products.package_quantity_overflow' => 'Overflow, %',
    'products.package_best' => 'Best package',
    'products.package_best_enabled' => 'Enabled',
    'products.package_best_disabled' => 'Disabled',
    'products.package_link' => 'Link Type',
    'products.package_link_default' => 'None',
    'products.package_availability' => 'Availability',
    'products.package_availability_enabled' => 'Enabled',
    'products.package_availability_disabled' => 'Disabled',
    'products.package_mode' => 'Mode',
    'products.package_mode_manual' => 'Manual',
    'products.package_mode_auto' => 'Auto',
    'products.package_provider' => 'Provider',
    'products.package_provider_default' => 'Chose providers',
    'products.package_service' => 'Provider service',
    'products.package_service_default' => 'Chose service',
    'products.package_delete_agree' => 'Are your sure that your want to delete this Package?',
    'products.package_delete_cancel' => 'Cancel',
    'products.package_delete_submit' => 'Yes, delete it!',
    'products.message_product_created' => 'Product was successfully created!',
    'products.message_product_updated' => 'Product was successfully updated!',
    'products.message_package_created' => 'Package was successfully created!',
    'products.message_package_updated' => 'Package was successfully updated!',
    'products.message_package_deleted' => 'Package was successfully deleted!',
    'products.message_choose_provider' => 'Choose provider!',
    'products.message_choose_service' => 'Choose service!',
    'products.message_api_error' => 'Loading error, try again later',
    'products.message_api_key_error' => 'Incorrect Apikey',
    'products.message_api_json_decode_error' => 'API response JSON decode errors!',
    'products.product_menu_header' => 'Confirm',
    'products.product_menu_message' => 'Do you want to create menu item {name}?',
    'products.product_menu_success' => 'Yes',
    'products.product_menu_cancel' => 'No',

    'products.f_id' => 'ID',
    'products.f_name' => 'Name',
    'products.f_position' => 'Position',
    'products.f_url' => 'Url',
    'products.f_properties' => 'Properties',
    'products.f_description' => 'Description',
    'products.f_visibility' => 'Visibility',
    'products.f_seo_title' => 'Seo Title',
    'products.f_seo_description' => 'Seo Description',
    'products.f_seo_keywords' => 'Seo Keywords',

    'settings.page_title' => 'Settings',
    'settings.left_menu_general' => 'General',
    'settings.left_menu_payments' => 'Payments',
    'settings.left_menu_providers' => 'Providers',
    'settings.left_menu_navigation' => 'Navigation',
    'settings.left_menu_pages' => 'Pages',
    'settings.left_menu_themes' => 'Themes',
    'settings.left_menu_blocks' => 'Blocks',
    'settings.left_menu_languages' => 'Language',

    'settings.general_title' => 'General',
    'settings.general_logo' => 'Logo',
    'settings.general_logo_upload' => 'Upload logo',
    'settings.general_logo_limits' => 'Image available types: jpg, png or gif. Maximum image size {fileSize}',
    'settings.general_favicon' => 'Favicon',
    'settings.general_favicon_upload' => 'Upload favicon',
    'settings.general_favicon_limits' => 'Image available types: jpg, png, gif or ico. Maximum image size {fileSize}',
    'settings.general_store_name' => 'Store name',
    'settings.general_store_name_placeholder' => 'Input store name',
    'settings.general_timezone' => 'Timezone',
    'settings.general_currency' => 'Currency',
    'settings.general_admin_email' => 'Admin email',
    'settings.general_admin_email_placeholder' => 'Store admin email',
    'settings.general_custom_header' => 'Custom header code',
    'settings.general_custom_header_placeholder' => '<style type="text/css">...</style>',
    'settings.general_custom_footer' => 'Custom footer code',
    'settings.general_custom_footer_placeholder' => '<script>...</script>',
    'settings.general_currency_change_approving' => 'If the currency has been changed, payment methods with this currency will be deleted.',


    'settings.general_seo' => 'Search engine listing preview',
    'settings.general_seo_edit' => 'Edit website SEO',
    'settings.general_seo_index' => 'Title index',
    'settings.general_seo_index_default' => 'About Us',
    'settings.general_seo_index_limits' => ' of 70 characters used',
    'settings.general_seo_meta' => 'Meta description',
    'settings.general_seo_meta_default' => 'A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text',
    'settings.general_seo_meta_limits' => ' of 160 characters used',
    'settings.general_seo_meta_keywords' => 'Meta keywords',
    'settings.general_save' => 'Save changes',

    'settings.general_delete_agree' => 'Are your sure that your want to delete this image?',
    'settings.general_delete_payments_agree' => 'This action will remove all unsupported payment methods and their settings. Are you sure?',
    'settings.general_delete_submit' => 'Delete',
    'settings.general_delete_cancel' => 'Cancel',

    'settings.message_settings_updated' => 'Settings was successfully updated!',
    'settings.message_payments_deleted' => 'Image was successfully deleted!',
    'settings.message_image_deleted' => 'Image was successfully deleted!',
    'settings.message_image_delete_error' => 'Error while deleting image!',
    'settings.message_cdn_upload_error' => 'Error uploading file to CDN!',

    'settings.payments_page_title' => 'Settings payments',
    'settings.payments_title' => 'Payments',
    'settings.payments_edit_method' => 'Edit',
    'settings.payments_save_method' => 'Save changes',
    'settings.payments_cancel_method' => 'Cancel',
    'settings.message_settings_saved' => 'Changes successfully saved!',
    'settings.payments_add' => 'Add method',
    'settings.payments_modal_title' => 'Add payment',
    'settings.payments_modal_method' => 'Method',
    'settings.payments_modal_select_item' => 'Please select…',
    'settings.payments_modal_save' => 'Add method',
    'settings.payments_modal_cancel' => 'Cancel',
    'settings.payments_modal_payment_method' => 'Payment method',
    'settings.payments_test_mode' => 'Use test mode',

    'settings.payments_edit_2checkout' => 'Edit 2Checkout',

    'settings.payments_stripe_guide_1' => 'Publishable key and Secret key you may find on {signup_url}',
    'settings.payments_stripe_guide_2' => 'Go to {url}',

    'settings.payments_stripe_guide_3' => ' and add endpoint for ',
    'settings.payments_stripe_guide_4' => 'Click on created webhook to get Signing secret',

    'settings.payments_2checkout_guide_1' => 'Login to your 2Checkout account.',
    'settings.payments_2checkout_guide_2' => 'Go to',
    'settings.payments_2checkout_guide_2-1-1' => 'Global URL:',
    'settings.payments_2checkout_guide_2-1-2' => '{store_site}/2checkout',
    'settings.payments_2checkout_guide_2-2-1' => 'Enable',
    'settings.payments_2checkout_guide_2-2-2' => 'Order Created:',
    'settings.payments_2checkout_guide_2-2-3' => '{store_site}/2checkout',
    'settings.payments_2checkout_guide_2-3-1' => 'Enable',
    'settings.payments_2checkout_guide_2-3-2' => 'Fraud Status Changed:',
    'settings.payments_2checkout_guide_2-3-3' => '{store_site}/2checkout',
    'settings.payments_2checkout_guide_2-4' => 'Click Save Settings',
    'settings.payments_2checkout_guide_3' => 'Go to',
    'settings.payments_2checkout_guide_3-1-1' => 'Demo Setting:',
    'settings.payments_2checkout_guide_3-1-2' => 'Off',
    'settings.payments_2checkout_guide_3-2-1' => 'Pricing Currency:',
    'settings.payments_2checkout_guide_3-2-2' => 'US Dollars',
    'settings.payments_2checkout_guide_3-3-1' => 'Direct Return:',
    'settings.payments_2checkout_guide_3-3-2' => 'Given links back to my website',
    'settings.payments_2checkout_guide_3-4-1' => 'Approved URL:',
    'settings.payments_2checkout_guide_3-4-2' => '{store_site}',
    'settings.payments_2checkout_guide_3-5-1' => 'Secret Word: set strong password',
    'settings.payments_2checkout_guide_3-6-1' => 'Click Save Settings',
    'settings.payments_2checkout_guide_4' => 'Enter your 2Checkout details below.',
    'settings.payments_2checkout_account_number' => 'Account Number',
    'settings.payments_2checkout_secret_word' => 'Secret Word',
    'settings.payments_2checkout_test_mode' => 'Use test mode',

    'settings.payments_edit_bitcoin' => 'Edit Bitcoin',
    'settings.payments_bitcoin_guide_1' => 'Sign up at {myselium_url}',
    'settings.payments_bitcoin_guide_2' => 'Create new gateway {mycelium_gateway_url}',
    'settings.payments_bitcoin_guide_2_1' => 'Callback url: {callback_url}',
    'settings.payments_bitcoin_guide_2_2' => 'After payment redirect to: {redirect_url}',
    'settings.payments_bitcoin_guide_2_3' => 'Back url: {back_url}',
    'settings.payments_bitcoin_guide_2_4' => 'Choose your panel currency',
    'settings.payments_bitcoin_guide_3' => 'Enter your Gateway secret and API Gateway ID below.',
    'settings.payments_bitcoin_gateway_id' => 'API Gateway ID',
    'settings.payments_bitcoin_gateway_secret' => 'Gateway secret',

    'settings.payments_edit_paypal' => 'Edit PayPal',
    'settings.payments_edit_paypalstandard' => 'Edit PayPal Standard',
    'settings.payments_paypal_guide_1' => 'Login to your PayPal account.',
    'settings.payments_paypal_guide_2' => 'Get your {api_credentials_url}.',
    'settings.payments_paypal_guide_3' => 'Enter your PayPal API details below.',
    'settings.payments_paypal_standard_guide_1' => 'Enter your PayPal Email address below.',
    'settings.payments_paypal_email' => 'Email',
    'settings.payments_paypal_username' => 'Api username',
    'settings.payments_paypal_password' => 'Api password',
    'settings.payments_paypal_signature' => 'Api signature',
    'settings.payments_paypal_test_mode' => 'Use test mode',

    'settings.payments_edit_coinpayments' => 'Edit CoinPayments',
    'settings.payments_coinpayments_merchant_id' => 'Merchant ID',
    'settings.payments_coinpayments_ipn_secret' => 'IPN secret',
    'settings.payments_coinpayments_guide_1' => 'Login to  {signup_url}',
    'settings.payments_coinpayments_guide_2' => 'Go to <strong>Account</strong> → <strong>Account settings</strong> → <strong>Merchant Settings</strong>',
    'settings.payments_coinpayments_guide_2_1' => 'Generate IPN secret',
    'settings.payments_coinpayments_guide_2_2' => 'Apply changes',
    'settings.payments_coinpayments_guide_3' => 'Enter your Merchant ID and IPN Secret bellow',

    'settings.payments_edit_stripe' => 'Edit Stripe',
    'settings.payments_stripe_secret_key' => 'Secret key',
    'settings.payments_stripe_public_key' => 'Public key',
    'settings.payments_stripe_webhook_secret' => 'Webhook secret',

    'settings.payments_edit_yandexmoney' => 'Edit Yandex.Money',
    'settings.payments_edit_yandexcards' => 'Edit Yandex.Cards',
    'settings.payments_yandex_money_wallet_number' => 'Wallet number',
    'settings.payments_yandex_cards_wallet_number' => 'Wallet number',
    'settings.payments_yandex_money_secret_word' => 'Secret word',
    'settings.payments_yandex_cards_secret_word' => 'Secret word',
    'settings.payments_yandex_money_guide_1' => 'Go to',
    'settings.payments_yandex_money_guide_2' => 'Enter login details.',
    'settings.payments_yandex_money_guide_2-1' => 'Secret word: set strong password',
    'settings.payments_yandex_money_guide_2-2' => 'HTTP-notices URL:',
    'settings.payments_yandex_money_guide_3' => 'Enter your Yandex money details below.',

    'settings.payments_edit_freekassa' => 'Edit Free Kassa',
    'settings.payments_free_kassa_merchant_id' => 'Merchant ID',
    'settings.payments_free_kassa_secret_word' => 'Secret Word',
    'settings.payments_free_kassa_secret_word2' => 'Secret Word 2',
    'settings.payments_free_kassa_guide_1' => 'Go to the Free Kassa settings page ',
    'settings.payments_free_kassa_guide_2' => 'Select the notification method ',
    'settings.payments_free_kassa_guide_2-1' => 'POST',
    'settings.payments_free_kassa_guide_3' => 'Select the integration mode ',
    'settings.payments_free_kassa_guide_3-1' => 'NO',
    'settings.payments_free_kassa_guide_4' => 'Site URL: ',
    'settings.payments_free_kassa_guide_5' => 'Notification URL: ',
    'settings.payments_free_kassa_guide_6' => 'Success URL: ',
    'settings.payments_free_kassa_guide_7' => 'Unsuccess URL: ',

    'settings.payments_edit_paytr' => 'Edit PayTR',
    'settings.payments_paytr_merchant_id' => 'Merchant id',
    'settings.payments_paytr_merchant_key' => 'Merchant key',
    'settings.payments_paytr_merchant_salt' => 'Merchant salt',
    'settings.payments_paytr_merchant_comission' => 'Merchant Comission',
    'settings.payments_paytr_guide_1' => 'Go to Merchant Settings',
    'settings.payments_paytr_guide_2' => 'Set callback url: ',

    'settings.payments_edit_paywant' => 'Edit PayWant',
    'settings.payments_paywant_apiKey' => 'API Key',
    'settings.payments_paywant_apiSecret' => 'API Secret',
    'settings.payments_paywant_fee' => 'Fee',
    'settings.payments_paywant_guide_1' => 'Store Site: ',
    'settings.payments_paywant_guide_2' => 'Ip address (Site): ',
    'settings.payments_paywant_guide_2-1' => '54.36.105.233',
    'settings.payments_paywant_guide_3' => 'API Return Address: ',

    'settings.payments_edit_billplz' => 'Edit Billplz',
    'settings.payments_billplz_secret' => 'Billplz Secret key',
    'settings.payments_billplz_collectionId' => 'Billing Collection ID',

    'settings.payments_edit_pagseguro' => 'Edit PagSeguro',
    'settings.payments_pagseguro_email' => 'Email',
    'settings.payments_pagseguro_token' => 'Token',

    'settings.payments_edit_webmoney' => 'Edit WebMoney',
    'settings.payments_webmoney_purse' => 'WMR Purse',
    'settings.payments_webmoney_secret_key' => 'Secret Key',
    'settings.payments_webmoney_guide_1' => 'To receive payments you must have minimum formal certificate with verified documents (or certificate of higher level).',
    'settings.payments_webmoney_guide_2' => 'Go to ',
    'settings.payments_webmoney_guide_3' => 'Enter login details.',
    'settings.payments_webmoney_guide_4' => 'Click change for WMR purse',
    'settings.payments_webmoney_guide_4-4-1-1' => 'Test/Work modes: ',
    'settings.payments_webmoney_guide_4-4-1-2' => ' work ',
    'settings.payments_webmoney_guide_4-4-2' => 'Merchant name: set your panel title',
    'settings.payments_webmoney_guide_4-4-3' => 'Secret Key: set strong password',
    'settings.payments_webmoney_guide_4-4-4' => 'Result URL: ',
    'settings.payments_webmoney_guide_4-4-5' => 'Success URL: ',
    'settings.payments_webmoney_guide_4-4-6' => 'Fail URL: ',
    'settings.payments_webmoney_guide_4-4-7' => 'Control sign forming method: ',
    'settings.payments_webmoney_guide_4-4-7-1' => 'SHA256',
    'settings.payments_webmoney_guide_5' => 'Enter your WebMoney details below.',

    'settings.payments_edit_authorize' => 'Edit Authorize',
    'settings.payments_authorize_merchant_login_id' => 'Merchant login ID',
    'settings.payments_authorize_merchant_transaction_id' => 'Merchant transaction ID',
    'settings.payments_authorize_merchant_client_key' => 'Merchant client key',
    'settings.payments_authorize_test_mode' => 'Use test mode',

    'settings.payments_edit_mercadopago' => 'Edit MercadoPago',
    'settings.payments_mercadopago_client_id' => 'Client ID',
    'settings.payments_mercadopago_secret' => 'Secret Key',
    'settings.payments_mercadopago_course' => 'Course',
    'settings.payments_mercadopago_test_mode' => 'Use test mode',

    'settings.payments.multi_input.add_description' => 'Add descriptions',
    'settings.payments_edit_mollie' => 'Edit Mollie',
    'settings.payments_mollie_api' => 'Enter your API key',
    'settings.payments_mollie_guide_1' => 'Go to Mollie website → {website_url} and get your <b>Live API key</b>',
    'settings.payments_mollie_guide_2' => 'If you want to test payment system, use <b>Test API key</b> instead.',

    'settings.providers_page_title' => 'Providers',
    'settings.providers_add' => 'Add provider',
    'settings.providers_save' => 'Save changes',
    'settings.providers_modal_title' => 'Add provider',
    'settings.providers_m_title' => 'Add provider',
    'settings.providers_m_name' => 'Exact link',
    'settings.providers_m_add' => 'Add provider',
    'settings.providers_m_cancel' => 'Cancel',

    'settings.blocks_page_title' => 'Blocks',
    'settings.customize_theme_pagetitle' => 'Customize {theme} theme ',
    'settings.edit_block_page_title' => 'Edit {block} block',

    'settings.message_provider_updated' => 'Provider successfully updated',
    'settings.message_provider_created' => 'Provider successfully created',

    'providers.f_id' => 'ID',
    'providers.f_site' => 'Site',
    'providers.f_protocol' => 'Protocol',
    'providers.f_type' => 'Type',
    'providers.f_created_at' => 'Created At',

    'settings.pages_page_title' => 'Pages',
    'settings.pages_add' => 'Add page',
    'settings.pages_edit' => 'Edit',
    'settings.pages_delete' => 'Delete',
    'settings.pages_create_page' => 'Create page',
    'settings.pages_edit_page' => 'Edit page',
    'settings.pages_title' => 'Title',
    'settings.pages_visibility' => 'Visibility',
    'settings.pages_visibility_visible' => 'Visible',
    'settings.pages_visibility_hidden' => 'Hidden',
    'settings.pages_update_never' => 'Never',
    'settings.pages_seo_edit' => 'Edit website SEO',
    'settings.pages_seo_preview' => 'Search engine listing preview',
    'settings.pages_seo_page' => 'Page title',
    'settings.pages_seo_page_default' => 'Page title',
    'settings.pages_seo_page_chars_used' => ' of 70 characters used',
    'settings.pages_seo_meta' => 'Meta description',
    'settings.pages_seo_meta_default' => 'A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text',
    'settings.pages_seo_meta_chars_used' => ' of 160 characters used',
    'settings.pages_seo_meta_keywords' => 'Meta keywords',
    'settings.pages_seo_url' => 'URL',
    'settings.pages_seo_url_default' => 'about-us',
    'settings.pages_save' => 'Save changes',
    'settings.pages_cancel' => 'Cancel',
    'settings.pages_delete_agree' => 'Are your sure that your want to delete this page?',
    'settings.pages_delete_cancel' => 'Cancel',
    'settings.pages_delete_submit' => 'Yes, delete it',
    'settings.pages_message_created' => 'Page was successfully created!',
    'settings.pages_message_updated' => 'Page was successfully updated!',
    'settings.pages_message_deleted' => 'Page was successfully deleted!',

    'settings.nav_page_title' => 'Navigation',
    'settings.nav_bt_add' => 'Add menu item',
    'settings.nav_bt_edit' => 'Edit',
    'settings.nav_bt_delete' => 'Delete',
    'settings.nav_delete_agree_text' => 'Are your sure that your want to delete this item?',
    'settings.nav_bt_delete_agree' => 'Yes, delete it!',
    'settings.nav_bt_delete_cancel' => 'Cancel',
    'settings.nav_add_modal_title' => 'Add menu item',
    'settings.nav_edit_modal_title' => 'Edit menu item',
    'settings.nav_edit_name' => 'Name',
    'settings.nav_edit_link' => 'Link',
    'settings.nav_edit_bt_add' => 'Add menu item',
    'settings.nav_edit_bt_cancel' => 'Cancel',
    'settings.nav_link_home_page' => 'Home page',
    'settings.nav_link_product' => 'Product',
    'settings.nav_link_page' => 'Page',
    'settings.nav_link_web_address' => 'Web address',
    'settings.nav_message_created' => 'Menu item was successfully created!',
    'settings.nav_message_updated' => 'Menu item was successfully updated!',
    'settings.nav_message_deleted' => 'Menu item was successfully deleted!',

    'settings.themes_page_title' => 'Themes',
    'settings.themes_active' => 'Active:',
    'settings.themes_add' => 'Add theme',
    'settings.themes_edit_code' => 'Edit code',
    'settings.themes_customize' => 'Customize',
    'settings.themes_activate' => 'Activate',
    'settings.themes_create_title' => 'Add theme',
    'settings.themes_theme_name' => 'Theme name',
    'settings.themes_create_save' => 'Save theme',
    'settings.themes_create_cancel' => 'Cancel',
    'settings.themes_edit_title' => 'Editing theme',
    'settings.themes_start_editing' => 'Pick a file from the right sidebar to start editing',
    'settings.themes_editing_save' => 'Save theme',
    'settings.themes_editing_cancel' => 'Cancel',
    'settings.themes_editing_reset' => 'Reset file',
    'settings.themes_modified' => 'Modified',

    'settings.themes_modal_submit_close_message' => 'All unsaved data will be lost!',
    'settings.themes_modal_submit_close_submit' => 'Yes, close!',
    'settings.themes_modal_submit_reset_message' => 'File will be reset to the original values!',
    'settings.themes_modal_submit_reset_submit' => 'Yes, reset!',
    'settings.themes_modal_cancel' => 'Cancel',

    'settings.themes_message_created' => 'Theme was successfully created!',
    'settings.themes_message_updated' => 'Theme was successfully updated!',
    'settings.themes_message_activated' => 'Theme "{theme_name}" was successfully activated!',
    'settings.themes_message_theme_not_exist' => 'Theme does not exist or has been deleted!',
    'settings.themes_message_reset' => 'Theme file was successfully reset!',

    'settings.languages_page_title' => 'Languages',
    'settings.languages_edit_page_title' => 'Edit language',
    'settings.languages_add' => 'Add language',
    'settings.languages_edit' => 'Edit',
    'settings.languages_modal_title' => 'Add language',
    'settings.languages_modal_language' => 'Language',
    'settings.languages_modal_select_item' => 'Please select…',
    'settings.languages_modal_save' => 'Save changes',
    'settings.languages_modal_cancel' => 'Cancel',
    'settings.languages_edit_language' => 'Language',
    'settings.languages_edit_save' => 'Save changes',
    'settings.languages_edit_cancel' => 'Cancel',
    'settings.languages_section_404' => '404',
    'settings.languages_section_cart' => 'Cart',
    'settings.languages_section_checkout' => 'Checkout',
    'settings.languages_section_contact' => 'Contact',
    'settings.languages_section_footer' => 'Footer',
    'settings.languages_section_order' => 'Order',
    'settings.languages_section_payment_result' => 'Payment result',
    'settings.languages_section_product' => 'Product',
    'settings.languages_section_result' => 'Result',
    'settings.languages_section_view_order' => 'View order',
    'settings.languages_section_orders' => 'Orders',

    'settings.languages_message_created' => 'Language was successfully created!',
    'settings.languages_message_updated' => 'Language was successfully updated!',

    'login.sign_in_page_title' => 'Sign in',
    'login.sign_in_header' => 'Sign In',
    'login.sign_in_username_placeholder' => 'Username',
    'login.sign_in_password_placeholder' => 'Password',
    'login.sign_in_submit_title' => 'Sign In',
    'login.message_bad_login' => 'Incorrect username or password!',
    'login.message_suspended' => 'Account suspended!',

    'account.page_title' => 'Account',
    'account.current_password' => 'Current password',
    'account.new_password' => 'New password',
    'account.confirm_password' => 'Confirm new password',

    'account.message_wrong_new_password_pair' => '"New password" and "Confirm new password" must be the same!',
    'account.message_wrong_new_password' => 'The new password must be different from the old one!',
    'account.message_wrong_current_password' => 'Invalid current password!',
    'account.message_password_changed' => 'Password successfully updated!',

    'component.file_validator.message' => 'File upload failed.',
    'component.file_validator.uploadRequired' => 'Please upload a file.',
    'component.file_validator.tooMany' => 'You can upload at most {limit, number} {limit, plural, one{file} other{files}}.',
    'component.file_validator.tooFew' => 'You should upload at least {limit, number} {limit, plural, one{file} other{files}}.',
    'component.file_validator.tooBig' => 'The file "{file}" is too big. Its size cannot exceed {formattedLimit}.',
    'component.file_validator.tooSmall' => 'The file "{file}" is too small. Its size cannot be smaller than {formattedLimit}.',
    'component.file_validator.wrongMimeType' => 'Only files with these MIME types are allowed: {mimeTypes}.',
    'component.file_validator.wrongExtension' => 'Only files with these extensions are allowed: {extensions}.',

    'settings.left_menu_notifications' => 'Notifications',
    'settings.notifications_page_title' => 'Notifications',
    'notifications.customers_block_title' => 'Customers notifications',
    'notifications.customers_block_description' => 'Get notified by email whenever a new order comes in',
    'notifications.admin_block_title' => 'Admin notifications',
    'notifications.admin_block_description' => 'Get notified by email whenever a new order comes in',
    'emails.block_title' => 'Admin emails',
    'emails.block_description' => 'Your customers will see Primary email address if you email them',
    'notifications.edit_button' => 'Edit',
    'emails.edit_button' => 'Edit',
    'emails.primary_label' => 'Primary',
    'emails.add_button' => 'Add email',

    'notifications.label.order_confirmation' => 'Order confirmation',
    'notifications.description.order_confirmation' => 'Sent to the customer after they place their order',
    'notifications.label.order_in_progress' => 'Order in progress',
    'notifications.description.order_in_progress' => 'Sent to the customer when their order become in progress',
    'notifications.label.order_completed' => 'Order completed',
    'notifications.description.order_completed' => 'Sent to the customer when their order is completed',
    'notifications.label.abandoned_checkout' => 'Abandoned checkout',
    'notifications.description.abandoned_checkout' => 'Sent to the customer 24 hours after they\'ve abandoned checkout',

    'notifications.label.new_auto_order' => 'New auto order',
    'notifications.description.new_auto_order' => 'Sent to admin when a customer places auto order.',
    'notifications.label.new_manual_order' => 'New manual order',
    'notifications.description.new_manual_order' => 'Sent to admin when a customer places manual order',
    'notifications.label.order_fail' => 'Order fail',
    'notifications.description.order_fail' => 'Sent to admin when order get Fail status',
    'notifications.label.order_error' => 'Order error',
    'notifications.description.order_error' => 'Sent to admin when order get Error status',

    'settings.notification_edit_page_title' => 'Edit notification template ({name}) ',
    'settings.edit_notification_submit_btn' => 'Save changes',
    'settings.edit_notification_cancel_btn' => 'Cancel',
    'settings.edit_notification_preview_btn' => 'Preview',
    'settings.edit_notification_send_test_btn' => 'Send test',
    'settings.edit_notification_reset_btn' => 'Reset',

    'settings.notification_has_been_updated' => 'Notification has been updated',
    'settings.emails_m_add' => 'Save changes',
    'settings.emails_m_cancel' => 'Cancel',
    'settings.emails_m_create_header' => 'Create email',
    'settings.emails_m_edit_header' => 'Edit email',
    'settings.message_admin_email_updated' => 'Email has been updated',
    'settings.message_admin_email_created' => 'Email has been created',
    'settings.confirm_delete_email' => 'Are your sure that your want to delete this email?',
    'settings.confirm_reset_email' => 'Are your sure that you want to reset this template?',
    'settings.notifications_confirm_btn' => 'Confirm',
    'settings.notifications_cancel_btn' => 'Cancel',

    'settings.send_test_m_cancel' => 'Cancel',
    'settings.send_test_m_confirm' => 'Send',
    'settings.send_test_m_header' => 'Send test',
    'settings.message_send_test_email_success' => 'Notification has been sent',
    'settings.message_send_test_email_error' => 'Can not send test notification',
    'settings.themes_can_not_customize' => 'Can not customize theme',

    'settings.notification_preview_m_header' => 'Preview',
    'settings.notification_preview_m_cancel' => 'Cancel',

    'addfunds.phone' => 'Phone',
    'addfunds.error.phone' => 'Phone error',

];