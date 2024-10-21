<?php

return [

    [
        'name' => __('Leads'),
        'description' => __('Leads'),
        'permissions' => [
            'view-all-leads'    =>['system.lead.index'],
            'view-one-lead'    =>['system.lead.show'],
            'delete-one-leads'  =>['system.lead.destroy'],
            'create-leads'      =>['system.lead.create','system.lead.store'],
        ]
    ],

    [
        'name' => __('Staff'),
        'description' => __('Staff Permissions'),
        'permissions' => [
            'view-all-staff'    =>['system.staff.index'],
            'view-one-staff'    =>['system.staff.show'],
            'delete-one-staff'  =>['system.staff.destroy'],
            'create-staff'      =>['system.staff.create','system.staff.store'],
            'update-staff'      =>['system.staff.edit','system.staff.update']
        ]
    ],


    [
        'name' => __('Clients'),
        'description' => __('Clients Permissions'),
        'permissions' => [
            'view-all-client'    =>['system.client.index'],
            'view-one-client'    =>['system.client.show'],
            'delete-one-client'  =>['system.client.destroy'],
            'create-client'      =>['system.client.create','system.client.store'],
            'update-client'      =>['system.client.edit','system.client.update'],
            'client-manage-all'  =>['client-manage-all']

        ]
    ],


    [
        'name' => __('Property Type'),
        'description' => __('Property Type Permissions'),
        'permissions' => [
            'view-all-property-type'    =>['system.property-type.index'],
            'view-one-property-type'    =>['system.property-type.show'],
            'delete-one-property-type'  =>['system.property-type.destroy'],
            'create-property-type'      =>['system.property-type.create','system.property-type.store'],
            'update-property-type'      =>['system.property-type.edit','system.property-type.update']
        ]
    ],

    [
        'name' => __('Property Status'),
        'description' => __('Property Status Permissions'),
        'permissions' => [
            'view-all-property-status'    =>['system.property-status.index'],
            'view-one-property-status'    =>['system.property-status.show'],
            'delete-one-property-status'  =>['system.property-status.destroy'],
            'create-property-status'      =>['system.property-status.create','system.property-status.store'],
            'update-property-status'      =>['system.property-status.edit','system.property-status.update']
        ]
    ],

    [
        'name' => __('Property Model'),
        'description' => __('Property Model Permissions'),
        'permissions' => [
            'view-all-property-model'    =>['system.property-model.index'],
            'view-one-property-model'    =>['system.property-model.show'],
            'delete-one-property-model'  =>['system.property-model.destroy'],
            'create-property-model'      =>['system.property-model.create','system.property-model.store'],
            'update-property-model'      =>['system.property-model.edit','system.property-model.update']
        ]
    ],

    [
        'name' => __('Request Status'),
        'description' => __('Request Status Permissions'),
        'permissions' => [
            'view-all-request-status'    =>['system.request-status.index'],
            'view-one-request-status'    =>['system.request-status.show'],
            'delete-one-request-status'  =>['system.request-status.destroy'],
            'create-request-status'      =>['system.request-status.create','system.request-status.store'],
            'update-request-status'      =>['system.request-status.edit','system.request-status.update']
        ]
    ],

    [
        'name' => __('Data Source'),
        'description' => __('Data Source Permissions'),
        'permissions' => [
            'view-all-data-source'    =>['system.data-source.index'],
            'view-one-data-source'    =>['system.data-source.show'],
            'delete-one-data-source'  =>['system.data-source.destroy'],
            'create-data-source'      =>['system.data-source.create','system.data-source.store'],
            'update-data-source'      =>['system.data-source.edit','system.data-source.update']
        ]
    ],

    [
        'name' => __('Purpose'),
        'description' => __('Purpose Permissions'),
        'permissions' => [
            'view-all-purpose'    =>['system.purpose.index'],
            'view-one-purpose'    =>['system.purpose.show'],
            'delete-one-purpose'  =>['system.purpose.destroy'],
            'create-purpose'      =>['system.purpose.create','system.purpose.store'],
            'update-purpose'      =>['system.purpose.edit','system.purpose.update']
        ]
    ],

    [
        'name' => __('Property'),
        'description' => __('Property Permissions'),
        'permissions' => [
            'view-all-property'    => ['system.property.index'],
            'view-one-property'    => ['system.property.show'],
            'delete-one-property'  => ['system.property.destroy'],
            'create-property'      => ['system.property.create','system.property.store','system.property.remove-image','system.property.image-upload'],
            'update-property'      => ['system.property.edit','system.property.update'],
            'upload-excel'         => ['system.property.upload-excel','system.property.upload-excel-store'],
            'property-manage-all'  => ['property-manage-all']
        ]
    ],

    [
        'name' => __('Request'),
        'description' => __('Request Permissions'),
        'permissions' => [
            'view-all-request'    =>['system.request.index'],
            'view-one-request'    =>['system.request.show'],
            'delete-one-request'  =>['system.request.destroy'],
            'create-request'      =>['system.request.create','system.request.store'],
            'update-request'      =>['system.request.edit','system.request.update'],
            'share-request'      =>['system.request.share'],
            'close-share-request'=>['system.request.close-share'],
            'request-manage-all'=> ['request-manage-all']
        ]
    ],



    [
        'name' => __('Area Type'),
        'description' => __('Area Type Permissions'),
        'permissions' => [
            'view-all-area-type'    =>['system.area-type.index'],
            'view-one-area-type'    =>['system.area-type.show'],
            'delete-one-area-type'  =>['system.area-type.destroy'],
            'create-area-type'      =>['system.area-type.create','system.area-type.store'],
            'update-area-type'      =>['system.area-type.edit','system.area-type.update']
        ]
    ],


    [
        'name' => __('Area'),
        'description' => __('Area Permissions'),
        'permissions' => [
            'view-all-area'    =>['system.area.index'],
            'view-one-area'    =>['system.area.show'],
            'delete-one-area'  =>['system.area.destroy'],
            'create-area'      =>['system.area.create','system.area.store'],
            'update-area'      =>['system.area.edit','system.area.update']
        ]
    ],


    [
        'name' => __('Setting'),
        'description' => __('Setting Permissions'),
        'permissions' => [
            'manage-setting'    =>['system.setting.index','system.setting.update']
        ]
    ],

    [
        'name' => __('Parameters'),
        'description' => __('Parameters Permissions'),
        'permissions' => [
            'view-all-parameter'    =>['system.parameter.index'],
            'view-one-parameter'    =>['system.parameter.show'],
            'delete-one-parameter'  =>['system.parameter.destroy'],
            'create-parameter'      =>['system.parameter.create','system.parameter.store'],
            'update-parameter'      =>['system.parameter.edit','system.parameter.update']
        ]
    ],





    [
        'name' => __('Call Purpose'),
        'description' => __('Call Purpose Permissions'),
        'permissions' => [
            'view-all-call-purpose'    =>['system.call-purpose.index'],
            'view-one-call-purpose'    =>['system.call-purpose.show'],
            'delete-one-call-purpose'  =>['system.call-purpose.destroy'],
            'create-call-purpose'      =>['system.call-purpose.create','system.call-purpose.store'],
            'update-call-purpose'      =>['system.call-purpose.edit','system.call-purpose.update']
        ]
    ],



    [
        'name' => __('Call Status'),
        'description' => __('Call Status Permissions'),
        'permissions' => [
            'view-all-call-status'    =>['system.call-status.index'],
            'view-one-call-status'    =>['system.call-status.show'],
            'delete-one-call-status'  =>['system.call-status.destroy'],
            'create-call-status'      =>['system.call-status.create','system.call-status.store'],
            'update-call-status'      =>['system.call-status.edit','system.call-status.update']
        ]
    ],


    [
        'name' => __('Calls'),
        'description' => __('Calls Permissions'),
        'permissions' => [
            'view-all-call'    =>['system.call.index'],
            'view-one-call'    =>['system.call.show'],
            'delete-one-call'  =>['system.call.destroy'],
            'create-call'      =>['system.call.create','system.call.store'],
            'update-call'      =>['system.call.edit','system.call.update'],
            'call-manage-all'  =>['call-manage-all']

        ]
    ],

    [
        'name' => __('Importer'),
        'description' => __('Importer Permissions'),
        'permissions' => [
            'view-all-importer'    =>['system.importer.index'],
            'view-one-importer'    =>['system.importer.show'],
            'delete-one-importer'  =>['system.importer.destroy'],
            'create-importer'      =>['system.importer.create','system.importer.store'],
            'update-importer'      =>['system.importer.edit','system.importer.update'],
            'importer-manage-all' => ['importer-manage-all']
        ]
    ],



    [
        'name' => __('Permission Group'),
        'description' => __('Permission Group Permissions'),
        'permissions' => [
            'view-all-permission-group'    =>['system.permission-group.index'],
            'view-one-permission-group'    =>['system.permission-group.show'],
            'delete-one-permission-group'  =>['system.permission-group.destroy'],
            'create-permission-group'      =>['system.permission-group.create','system.permission-group.store'],
            'update-permission-group'      =>['system.permission-group.edit','system.permission-group.update']
        ]
    ],



    [
        'name' => __('Calendar'),
        'description' => __('Calendar Permissions'),
        'permissions' => [
            'manage-calendar' => [
                'system.calendar.index',
                'system.calendar.ajax',
                'system.calendar.show',
                'system.calendar.store'
            ],
        ]
    ],

    [
        'name' => __('Auth Sessions'),
        'description' => __('Auth Sessions'),
        'permissions' => [
            'view-auth-session'=>['system.staff.auth-sessions'],
            'delete-auth-session'=>['system.staff.delete-auth-sessions'],
        ]
    ],

    [
        'name' => __('Activity Log'),
        'description' => __('Activity Log'),
        'permissions' => [
            'view-activity-log'=>['system.activity-log.index'],
            'view-one-activity-log'=>['system.activity-log.show'],
        ]
    ],
    


];