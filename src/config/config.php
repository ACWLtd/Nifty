<?php

    return [
        /*
         *If true, cms will look for extensions defined under cms_extensions
         */
        'has_extensions' => false,

        /*
         * An example of expected data; make sure the route name is defined first
         */
        'cms_extensions' => [
            'toolbox' => [
                'font-awesome-class' => 'fa fa-wrench',
                'children' => [
                    [
                        'name' => 'All Tools',
                        'route-name' => 'tools.index',
                    ],
                ],
            ],
        ],

    ];