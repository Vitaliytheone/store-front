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
    'header.menu_settings_integrations' => 'Integrations',
    'header.menu_settings_notifications' => 'Notifications',
    'header.menu_settings_languages' => 'Languages',
    'header.menu_pages' => 'Pages',

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
    'payments.payment_method_all' => 'All',
    'payments.payment_method_deleted' => 'All',

    'payments.t_id' => 'Payment ID',
    'payments.t_order_id' => 'Order ID',
    'payments.t_customer' => 'Customer',
    'payments.t_amount' => 'Amount',
    'payments.t_method' => 'Method',
    'payments.t_fee' => 'Fee',
    'payments.t_memo' => 'Memo',
    'payments.t_status' => 'Status',
    'payments.t_date' => 'Date',

    'products.page_title' => 'Products',
    'products.create_product' => 'Create new product',
    'products.edit_product' => 'Edit',
    'products.add_package' => 'Add package',
    'products.edit_package' => 'Edit',
    'products.delete_package' => 'Delete',
    'products.no_products_message' => 'No products were found!',

    'product.error.can_not_save' => 'Can not save product',
    'products.create_product.header' => 'Create new product',
    'products.create_product.cancel_btn' => 'Cancel',
    'products.create_product.submit_btn' => 'Create product',
    'products.create_product.name' => 'Product name',
    'products.create_product.create_page' => 'Create new product page',
    'products.create_product.url' => 'Url',
    'products.edit_product.name' => 'Product name',
    'products.duplicate_package' => 'Duplicate',
    'products.packages.column.name' => 'Name',
    'products.packages.column.provider' => 'Provider',
    'products.packages.column.price' => 'Price',

    'products.edit_product.header' => 'Edit product',
    'products.edit_product.cancel_btn' => 'Cancel',
    'products.edit_product.submit_btn' => 'Save changes',

    'products.create_package.header' => 'Create package',
    'products.create_package.name' => 'Package name *',
    'products.create_package.price' => 'Price *',
    'products.create_package.quantity' => 'Quantity *',
    'products.create_package.best' => 'Best package',
    'products.create_package.link' => 'Link Type',
    'products.create_package.availability' => 'Availability',
    'products.create_package.mode' => 'Mode',
    'products.create_package.provider_service' => 'Provider service',
    'products.create_package.provider' => 'Provider',

    'products.create_package.availability_enabled' => 'Enabled',
    'products.create_package.availability_disabled' => 'Disabled',

    'products.create_package.link_default' => 'None',

    'products.create_package.best_enabled' => 'Enabled',
    'products.create_package.best_disabled' => 'Disabled',

    'products.create_package.mode_manual' => 'Manual',
    'products.create_package.mode_auto' => 'Auto',

    'products.create_package.cancel_btn' => 'Cancel',
    'products.create_package.submit_btn' => 'Add package',

    'products.edit_package.header' => 'Edit package',
    'products.edit_package.cancel_btn' => 'Cancel',
    'products.edit_package.submit_btn' => 'Save changes',
    'products.edit_package.cancel_link' => 'Cancel',
    'products.edit_package.delete_link' => 'Delete',
    'products.edit_package.delete_description' => 'Are you sure you want to <br> <b>delete</b> this package?',

    'products.duplicate_package.confirm' => 'Are you sure you want to <br> <b>duplicate</b> this package?',
    'products.duplicate_package.error' => 'Can not duplicate package',
    'products.message_package_duplicated' => 'Package was successfully duplicated!',


    'products.package_create' => 'Create package',
    'products.package_service_default' => 'Choose service',

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

    'pages.name' => 'Page name',
    'pages.url' => 'URL',
    'pages.title' => 'Page title',
    'pages.description' => 'Meta description',
    'pages.keywords' => 'Meta keywords',
    'pages.chars' => ' of {count} characters used',
    'pages.visibility' => 'Visibility',
    'pages.cancel' => 'Cancel',
    'pages.delete' => 'Delete',
    'pages.add' => 'Add page',
    'pages.new' => 'New page',
    'pages.update' => 'Update page',
    'pages.edit_seo' => 'Edit website SEO',
    'pages.search_preview' => 'Search engine listing preview',
    'pages.modal.are_you_sure' => 'Are you sure you want to<br><b>delete</b> this page?',
    'pages.create_page' => 'Create new page',
    'pages.status.draft' => 'Changes not published',
    'pages.status' => 'Status',
    'pages.last_updated' => 'Last updated',
    'pages.duplicate' => 'Duplicate',
    'pages.settings' => 'Settings',
    'pages.editor' => 'Editor',
    'pages.confirm_message' => 'Are you sure?',
    'pages.duplicate_confirm' => '<p>Are your sure that your want to <br><strong>duplicate</strong> this page?</p>',
    'pages.is_duplicated' => 'Page success duplicated!',
    'pages.link_invalid' => 'URL is invalid! Allows only a-z, 0-9 and "-"',
    'pages.link_exist' => 'URL is already exist!',


    'settings.page_title' => 'Settings',
    'settings.left_menu_general' => 'General',
    'settings.left_menu_payments' => 'Payments',
    'settings.left_menu_providers' => 'Providers',
    'settings.left_menu_integrations' => 'Integrations',
    'settings.left_menu_pages' => 'Pages',
    'settings.left_menu_notifications' => 'Notifications',
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
    'settings.general_currency_change_approving' => 'If the currency has been changed, payment methods with this currency will be deleted.',
    'settings.general_save' => 'Save changes',

    'settings.general_delete_agree' => 'Are your sure that your want to delete this image?',
    'settings.general_delete_payments_agree' => 'This action will remove all unsupported payment methods and their settings. Are you sure?',
    'settings.general_delete_submit' => 'Delete',
    'settings.general_delete_cancel' => 'Cancel',

    'settings.message_settings_saved' => 'Changes successfully saved!',
    'settings.message_settings_updated' => 'Settings was successfully updated!',
    'settings.message_image_deleted' => 'Image was successfully deleted!',
    'settings.message_image_delete_error' => 'Error while deleting image!',
    'settings.message_cdn_upload_error' => 'Error uploading file to CDN!',

    'settings.payments_page_title' => 'Settings payments',
    'settings.payments_title' => 'Payments',
    'settings.payments_edit_method' => 'Edit',
    'settings.payments_save_method' => 'Save changes',
    'settings.payments_cancel_method' => 'Cancel',
    'settings.payments_add' => 'Add method',
    'settings.payments_modal_title' => 'Add payment',
    'settings.payments_modal_save' => 'Add method',
    'settings.payments_modal_cancel' => 'Cancel',
    'settings.payments_modal_payment_method' => 'Payment method',
    'settings.payments_test_mode' => 'Use test mode',

    'settings.payments_2checkout_account_number' => 'Account Number',
    'settings.payments_2checkout_secret_word' => 'Secret Word',
    'settings.payments_2checkout_test_mode' => 'Use test mode',

    'settings.payments_paypal_email' => 'Email',
    'settings.payments_paypal_username' => 'Api username',
    'settings.payments_paypal_password' => 'Api password',
    'settings.payments_paypal_signature' => 'Api signature',
    'settings.payments_paypal_test_mode' => 'Use test mode',

    'settings.payments_coinpayments_merchant_id' => 'Merchant ID',
    'settings.payments_coinpayments_ipn_secret' => 'IPN secret',

    'settings.payments_stripe_secret_key' => 'Secret key',
    'settings.payments_stripe_public_key' => 'Public key',
    'settings.payments_stripe_webhook_secret' => 'Webhook secret',

    'settings.payments_yandex_money_wallet_number' => 'Wallet number',
    'settings.payments_yandex_cards_wallet_number' => 'Wallet number',
    'settings.payments_yandex_money_secret_word' => 'Secret word',
    'settings.payments_yandex_cards_secret_word' => 'Secret word',

    'settings.payments_edit_freekassa' => 'Edit Free Kassa',
    'settings.payments_free_kassa_merchant_id' => 'Merchant ID',
    'settings.payments_free_kassa_secret_word' => 'Secret Word',
    'settings.payments_free_kassa_secret_word2' => 'Secret Word 2',

    'settings.payments_paytr_merchant_id' => 'Merchant id',
    'settings.payments_paytr_merchant_key' => 'Merchant key',
    'settings.payments_paytr_merchant_salt' => 'Merchant salt',
    'settings.payments_paytr_merchant_comission' => 'Merchant Comission',

    'settings.payments_paywant_apiKey' => 'API Key',
    'settings.payments_paywant_apiSecret' => 'API Secret',
    'settings.payments_paywant_fee' => 'Fee',

    'settings.payments_billplz_secret' => 'Billplz Secret key',
    'settings.payments_billplz_collectionId' => 'Billing Collection ID',

    'settings.payments_pagseguro_email' => 'Email',
    'settings.payments_pagseguro_token' => 'Token',

    'settings.payments_webmoney_purse' => 'WMR Purse',
    'settings.payments_webmoney_secret_key' => 'Secret Key',

    'settings.payments_authorize_merchant_login_id' => 'Merchant login ID',
    'settings.payments_authorize_merchant_transaction_id' => 'Merchant transaction ID',
    'settings.payments_authorize_merchant_client_key' => 'Merchant client key',

    'settings.payments_mercadopago_client_id' => 'Client ID',
    'settings.payments_mercadopago_secret' => 'Secret Key',
    'settings.payments_mercadopago_course' => 'Course',
    'settings.payments_mercadopago_test_mode' => 'Use test mode',

    'settings.payments_edit_mollie' => 'Edit Mollie',
    'settings.payments_mollie_api' => 'Enter your API key',

    'settings.payments.multi_input.add_description' => 'Add descriptions',


    'settings.providers_page_title' => 'Providers',
    'settings.providers_add' => 'Add provider',
    'settings.providers_save' => 'Save changes',
    'settings.providers_m_title' => 'Add provider',
    'settings.providers_m_name' => 'Exact link',
    'settings.providers_m_add' => 'Add provider',
    'settings.providers_m_cancel' => 'Cancel',
    'settings.providers_no_providers' => 'No providers',

    'settings.message_provider_updated' => 'Provider successfully updated',
    'settings.message_provider_created' => 'Provider successfully created',

    'providers.f_id' => 'ID',
    'providers.f_site' => 'Site',
    'providers.f_protocol' => 'Protocol',
    'providers.f_type' => 'Type',
    'providers.f_created_at' => 'Created At',

    'settings.pages_page_title' => 'Pages',
    'settings.pages_add' => 'Add page',
    'settings.pages_edit_page' => 'Edit page',
    'settings.pages_update_never' => 'Never',

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
    'settings.languages_section_product' => 'Product',
    'settings.languages_section_result' => 'Result',
    'settings.languages_section_view_order' => 'View order',
    'settings.languages_section_orders' => 'Orders',
    'settings.errors_providers_exist' => 'Provider already exist.',
    'settings.errors_providers_vaild' => 'Provider not valid',

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
    'account.btn_save' => 'Save changes',

    'account.message_wrong_new_password_pair' => '"New password" and "Confirm new password" must be the same!',
    'account.message_wrong_new_password' => 'The new password must be different from the old one!',
    'account.message_wrong_current_password' => 'Invalid current password!',
    'account.message_password_changed' => 'Password successfully updated!',

    'component.file_validator.message' => 'File upload failed.',
    'component.file_validator.uploadRequired' => 'Please upload a file.',
    'component.file_validator.tooFew' => 'You should upload at least {limit, number} {limit, plural, one{file} other{files}}.',
    'component.file_validator.tooBig' => 'The file "{file}" is too big. Its size cannot exceed {formattedLimit}.',
    'component.file_validator.tooSmall' => 'The file "{file}" is too small. Its size cannot be smaller than {formattedLimit}.',
    'component.file_validator.wrongMimeType' => 'Only files with these MIME types are allowed: {mimeTypes}.',
    'component.file_validator.wrongExtension' => 'Only files with these extensions are allowed: {extensions}.',

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

    'cdn.error.common' => 'CDN error, try again later',
    'cdn.error.bad_upload' => 'Error uploading file to CDN!',

    'settings.integrations_page_title' => 'Integrations',
    'settings.integrations_edit_title' => 'Edit',
    'settings.integrations_chats_title' => 'Online chats',
    'settings.integrations_analytics_title' => 'Analytics',
    'settings.integrations_edit.code_label' => 'Code snippet',
    'settings.integrations_edit.cancel_button' => 'Cancel',
    'settings.integrations_edit.save_button' => 'Save changes',

    '404.title' => 'Page Not Found - 404',
    '404.text' => 'The requested page was not found on this server',
    'frozen.title' => 'Store temporarily unavailable',
    'frozen.text' => 'Store temporarily unavailable',
];