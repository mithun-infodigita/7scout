<?php

return [
    'customsAreas' => [
        'eu'    =>  [
            'key'                       =>   'eu',
            'name'                      =>   'European Union',
            'type'                      =>   'value',
            'tax_by_values'             =>    [
                0
            ],
            'tax_units'                 =>    [
                'EUR'
            ],
            'default_tax_by_value'      =>    0,
            'default_tax_unit'          =>    'EUR'
        ],

        'ch'    => [
            'key'                       =>   'ch',
            'name'                      =>    'Switzerland',
            'type'                      =>    'weight',
            'tax_by_values'             =>      [
                100
            ],
            'tax_units'                 =>    [
                'kg'
            ],
            'default_tax_by_value'      =>    100,
            'default_tax_unit'          =>    'kg'
        ]
    ]
];
