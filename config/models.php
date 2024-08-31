<?php

return [
    '*' => [
        'path' => app_path('Models'),
        'namespace' => 'App\Models',
        'parent' => Illuminate\Database\Eloquent\Model::class,
        'connection' => true,
        'timestamps' => true,
        'soft_deletes' => [
            'enabled' => true,
            'field' => 'deleted_at',
        ],
        'date_format' => 'Y-m-d H:i:s',
        'per_page' => 5,
        'base_files' => true,
        'snake_attributes' => true,
        'indent_with_space' => 0,
        'qualified_tables' => false,
        'hidden' => [
            '*secret*', '*password', '*token',
        ],
        'guarded' => [
            'created_by', 'updated_by'
        ],
        'casts' => [
            '*_json' => 'json',
        ],
        'relation_name_strategy' => 'foreign_key',  // Utilisez foreign_key ou related selon votre préférence
        'with_property_constants' => false,
        'pluralize' => true,
        'hidden_in_base_files' => false,
        'fillable_in_base_files' => false,
        'enable_return_types' => false,
    ],
];
