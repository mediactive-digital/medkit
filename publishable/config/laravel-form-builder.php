<?php

return [
    'defaults'      => [
        'wrapper_class'       => 'form-group',
        'wrapper_error_class' => 'has-error',
        'label_class'         => 'control-label',
        'field_class'         => 'form-control',
        'field_error_class'   => '',
        'help_block_class'    => 'help-block',
        'error_class'         => 'text-danger',
        'required_class'      => 'required',
        
        'inputgroup' => [
            'wrapper_class' => 'input-group form-group',
        ],
        'onecustomcheckbox' => [
            'wrapper_class' => 'custom-control custom-checkbox form-group',
            'field_class'   => 'custom-control-input',
            'label_class'   => 'custom-control-label'
        ],
        'checkbox' => [
            'wrapper_class' => 'form-check',
            'field_class'   => 'form-check-input',
            'label_class'   => 'form-check-label',
            'choice_options' => [
                'wrapper_class' => 'custom-control custom-checkbox',
                'label_class'   => 'custom-control-label',
                'field_class'   => 'custom-control-input',
            ],
        ],
        'radio' => [
            'wrapper_class' => 'form-check',
            'field_class'   => 'form-check-input',
            'label_class'   => 'form-check-label',
            'choice_options' => [
                'wrapper_class' => 'custom-control custom-radio',
                'label_class'   => 'custom-control-label',
                'field_class'   => 'custom-control-input',
            ],
        ]

        // Override a class from a field.
        //'text'                => [
        //    'wrapper_class'   => 'form-field-text',
        //    'label_class'     => 'form-field-text-label',
        //    'field_class'     => 'form-field-text-field',
        //]
        //'radio'               => [
        //    'choice_options'  => [
        //        'wrapper'     => ['class' => 'form-radio'],
        //        'label'       => ['class' => 'form-radio-label'],
        //        'field'       => ['class' => 'form-radio-field'],
        //],
    ],
    // Templates
    'form'          => 'laravel-form-builder::form',
    'text'          => 'laravel-form-builder::text',
    'textarea'      => 'laravel-form-builder::textarea',
    'button'        => 'laravel-form-builder::button',
    'buttongroup'   => 'laravel-form-builder::buttongroup',
    'radio'         => 'laravel-form-builder::radio',
    'checkbox'      => 'laravel-form-builder::checkbox',
    'select'        => 'laravel-form-builder::select',
    'choice'        => 'laravel-form-builder::choice',
    'repeated'      => 'laravel-form-builder::repeated',
    'child_form'    => 'laravel-form-builder::child_form',
    'collection'    => 'laravel-form-builder::collection',
    'static'        => 'laravel-form-builder::static',

    // Remove the laravel-form-builder:: prefix above when using template_prefix
    'template_prefix'   => '',

    'default_namespace' => '',

    'custom_fields' => [
       'inputgroup' => App\Forms\Fields\InputGroupType::class,
       'onecustomcheckbox' => App\Forms\Fields\OneCustomCheckboxType::class
    ]
];
