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
        'required_class'      => 'required'

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
    'select2'       => 'medKitTheme::forms.fields.select2',
    'translatable'  => 'medKitTheme::forms.fields.translatable',
    'ck_editor'     => 'medKitTheme::forms.fields.ck_editor',

    // Remove the laravel-form-builder:: prefix above when using template_prefix
    'template_prefix'   => '',

    'default_namespace' => '',

    // Set CKEditor as default for translatable textareas
    'translatable_textarea_ck_editor' => false,

    'custom_fields' => [
        'select2' =>'\MediactiveDigital\MedKit\Forms\Fields\Select2Type',
        'translatable' =>'\MediactiveDigital\MedKit\Forms\Fields\TranslatableType'
    ]
];
