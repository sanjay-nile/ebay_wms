<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Las :atributo debe ser aceptado.',
    'active_url'           => 'Las :atributo no es una URL válida.',
    'after'                => 'Las :atributo debe ser una fecha después de: fecha.',
    'alpha'                => 'Las :atributo solo puede contener letras.',
    'alpha_dash'           => 'Las :atributo solo puede contener letras, números y guiones.',
    'alpha_num'            => 'Las :atributo solo puede contener letras y numeros.',
    'array'                => 'Las :atributo debe ser una matriz.',
    'before'               => 'Las :atributo debe ser una fecha anterior a: fecha.',
    'between'              => [
                                'numeric' => 'Las :el atributo debe estar entre: min y: max.',
                                'file'    => 'Las :el atributo debe estar entre: min y: max kilobytes.',
                                'string'  => 'Las :atributo debe estar entre min y: max caracteres.',
                                'array'   => 'Las :el atributo debe tener entre: min y: max elementos.',
    ],
    'boolean'              => 'Las :campo de atributo debe ser verdadero o falso.',
    'confirmed'            => 'Las :la confirmación del atributo no coincide.',
    'date'                 => 'Las :atributo no es una fecha válida.',
    'date_format'          => 'Las :atributo no coincide con el formato: formato.',
    'different'            => 'Las :atributo y: otro debe ser diferente.',
    'digits'               => 'Las :atributo debe ser: dígitos dígitos.',
    'digits_between'       => 'Las :el atributo debe estar entre: min y: max dígitos.',
    'email'                => 'Las :atributo debe ser una dirección de correo electrónico válida.',
    'exists'               => 'Las seleccionado: el atributo no es válido.',
    'filled'               => 'Las :campo de atributo es obligatorio.',
    'image'                => 'Las :atributo debe ser una imagen.',
    'in'                   => 'Las seleccionado: el atributo no es válido.',
    'integer'              => 'Las :atributo debe ser un número entero.',
    'ip'                   => 'Las :atributo debe ser una dirección IP válida.',
    'json'                 => 'Las :atributo debe ser una cadena JSON válida.',
    'max'                  => [
                                'numeric' => 'Las :atributo no puede ser mayor que: max.',
                                'file'    => 'Las :atributo no puede ser mayor que: max kilobytes.',
                                'string'  => 'Las :atributo no puede ser mayor que: max caracteres.',
                                'array'   => 'Las :atributo no puede tener más de: max artículos.',
    ],
    'mimes'                => 'Las :atributo debe ser un archivo de tipo:: valores.',
    'min'                  => [
                                'numeric' => 'Las :atributo debe ser al menos: min.',
                                'file'    => 'Las :atributo debe ser al menos: kilobytes min.',
                                'string'  => 'Las :atributo debe ser al menos: min caracteres.',
                                'array'   => 'Las :atributo debe tener al menos: min elementos.',
    ],
    'not_in'               => 'Las seleccionado: el atributo no es válido.',
    'numeric'              => 'Las :atributo debe ser un número.',
    'regex'                => 'Las :attribute format is invalid.',
    'required'             => 'Las :attribute field is required.',
    'required_if'          => 'Las :attribute field is required when :other is :value.',
    'required_with'        => 'Las :attribute field is required when :values is present.',
    'required_with_all'    => 'Las :attribute field is required when :values is present.',
    'required_without'     => 'Las :attribute field is required when :values is not present.',
    'required_without_all' => 'Las :attribute field is required when none of :values are present.',
    'same'                 => 'Las :attribute and :other must match.',
    'size'                 => [
                                'numeric' => 'Las :attribute must be :size.',
                                'file'    => 'Las :attribute must be :size kilobytes.',
                                'string'  => 'Las :attribute must be :size characters.',
                                'array'   => 'Las :attribute must contain :size items.',
    ],
    'string'               => 'Las :attribute must be a string.',
    'timezone'             => 'Las :attribute must be a valid zone.',
    'unique'               => 'Las :attribute has already been taken.',
    'url'                  => 'Las :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
    'whoops'                                                  =>  'Whoops!',
    'input_error'                                             =>  'There were some problems with your input.',
    'billing_fill_first_name_field'                           =>  'please fill billing First Name field',
    'shipping_fill_first_name_field'                          =>  'please fill shipping First Name field',
    'billing_fill_last_name_field'                            =>  'please fill billing Last Name field',
    'billing_fill_phone_number_field'                         =>  'please fill billing Phone Number field',
    'shipping_fill_last_name_field'                           =>  'please fill shipping Last Name field',
    'billing_fill_email_field'                                =>  'please fill billing Email field',
    'shipping_fill_email_field'                               =>  'please fill shipping Email field',
    'billing_fill_valid_email_field'                          =>  'please fill valid Email in billing field',
    'shipping_fill_valid_email_field'                         =>  'please fill valid Email in shipping field',
    'billing_country_name_field'                              =>  'please select billing Country Name field',
    'shipping_country_name_field'                             =>  'please select shipping Country Name field',
    'billing_address_line_1_field'                            =>  'please fill billing Address Line 1 field',
    'shipping_address_line_1_field'                           =>  'please fill shipping Address Line 1 field',
    'billing_fill_town_city_field'                            =>  'please fill billing Town Or City field',
    'shipping_fill_town_city_field'                           =>  'please fill shipping Town Or City field',
    'billing_fill_zip_postal_field'                           =>  'please fill billing Zip Or Postal Code field',
    'shipping_fill_zip_postal_field'                          =>  'please fill shipping Zip Or Postal Code field',
    'shipping_fill_phone_number_field'                        =>  'please fill shipping Phone Number field',
    'fill_payment_gateway'                                    =>  'please select Payment Gateway',
    'stripe_required_msg'                                     =>  'Stripe token key need to continue the process',
    'twocheckout_required_msg'                                =>  'TwoCheckout token key need to continue the process',		
    'display_name_required'                                   =>  'Please fill Display Name field',
    'user_name_required'                                      =>  'Please fill User Name field',
    'user_name_unique'                                        =>  'User name field is unique, try with another one',
    'email_required'                                          =>  'Please fill Email Address field',
    'email_unique'                                            =>  'Email address field is unique, try with another one',
    'email_is_email'                                          =>  'Please fill with correct email address',
    'password_required'                                       =>  'Please fill Password field',
    'password_confirmation_required'                          =>  'Please fill Password Confirmation field',
    'secret_key_required'                                     =>  'Please fill Secret Key field',
    'g_recaptcha_response_required'                           =>  'Please manage recaptcha response',
    'new_password_required'                                   =>  'Please fill New Password field',
    'account_bill_first_name'                                 =>  'Please fill Billing First Name field',
    'account_bill_last_name'                                  =>  'Please fill Billing Last Name field',
    'account_bill_phone_number_name'                          =>  'Please fill Billing Phone Number field',
    'account_bill_select_country'                             =>  'Please Select Country Name field',
    'account_bill_adddress_line_1'                            =>  'Please fill Address Line 1 field',
    'account_shipping_first_name'                             =>  'Please fill Shipping First Name field',
    'account_shipping_last_name'                              =>  'Please fill Shipping Last Name field',
    'account_bill_email_address'                              =>  'please fill Billing Email field',
    'account_bill_email_address_is_email'                     =>  'Please fill Billing Email field with correct email address',
    'account_bill_select_country'                             =>  'please Select Billing Country Name field',
    'account_bill_adddress_line_1'                            =>  'please fill Billing Address Line 1 field',
    'account_bill_town_or_city'                               =>  'please fill Billing Town Or City field',
    'account_bill_zip_or_postal_code'                         =>  'please fill Billing Zip Or Postal Code field',
    'account_shipping_email_address'                          =>  'please fill Shipping Email field',
    'account_shipping_email_address_is_email'                 =>  'Please fill Shipping Email field with correct email address',
    'account_shipping_select_country'                         =>  'please Select Shipping Country Name field',
    'account_shipping_adddress_line_1'                        =>  'please fill Shipping Address Line 1 field',
    'account_shipping_town_or_city'                           =>  'please fill Shipping Town Or City field',
    'account_shipping_zip_or_postal_code'                     =>  'please fill Shipping Zip Or Postal Code field',
    'account_shipping_phone_number_name'                      =>  'Please fill Shipping Phone Number field',
    'select_rating'                                           =>  'please select a rating',
    'write_review'                                            =>  'please write your review',
    'coupon_removed_from_cart_msg'                            =>  'Coupon has been removed from cart for some condition false',
    'vendor_reg_store_name'                                   =>  'please fill Store Name',
    'vendor_reg_address_line_1'                               =>  'please fill Address Line 1',
    'vendor_reg_city'                                         =>  'please fill City',
    'vendor_reg_state'                                        =>  'please fill State',
    'vendor_reg_country'                                      =>  'please fill Country',
    'vendor_reg_zip_code'                                     =>  'please fill Zip Code',
    'vendor_reg_phone_number'                                 =>  'please fill Phone Number',
    'vendor_reg_secret_key'                                   =>  'please fill Secret Key',
    't_and_c'                                                 =>  'please read Terms and Conditions and select',
    'all_vendor_max_products'                                 =>  'please enter max number of products',
    'vendor_expired_type'                                     =>  'Please select vendor expired type',
    'vendor_commission'                                       =>  'Please enter vendor commission',
    'payment_options'                                         =>  'Please select vendor payment withdraw options',
    'package_type'                                            =>  'Please select package type',
    'vendor_custom_expired_date'                              =>  'Please enter custom expired date',
    'vendor_package_type_unique_msg'                          =>  'Your given package type has already been taken.',
    'select_vendor_payment_type_msg'                          =>  'Select payment type',
    'select_vendor_payment_method_msg'                        =>  'Select payment method',
    'enter_single_payment_custom_value'                       =>  'Please enter single payment custom value'
];
