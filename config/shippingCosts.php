<?php

use App\Models\Indices\PartIndexDe;

return [

    'max_total_weight'              =>  50,
    'parcel_max_weight'             =>  26,
    'single_part_max_weight'        =>  26,

    'part_index_fallback'           =>  PartIndexDe::class,

    'shipping_shippers' => [
        [
            'shipper_name'          =>  'DHL Parcel',
            'shipper_id'            =>  'dhlparcel',
            'sender_country'        =>  'DE',
            'receiver_country'      =>  'DE',
            'currency'              =>  'EUR',
            'weight_unit'           =>  'kg',
            'max_total_weight'      =>  50,
            'parcel_max_weight'     =>  26,
            'single_part_max_weight'=>  26,
            'customs_administration'      =>  0,
            'included_custom_tariff_number'      =>  999,
            'additional_tariff_number_costs'     =>  0,
            'duties_percentage'    =>   0,
            'weight_and_costs'     =>  [
                [
                    'gross_weight'  =>  30,
                    'max_net_weight'=>  26,
                    'true_price'    =>  13.8,
                    'shipping_costs'=>  15
                ],
                [
                    'gross_weight'  =>  20,
                    'max_net_weight'=>  17,
                    'true_price'    =>  11.8,
                    'shipping_costs'=>  13
                ],
                [
                    'gross_weight'  =>  10,
                    'max_net_weight'=>  8,
                    'true_price'    =>  7.75,
                    'shipping_costs'=>  9
                ],
                [
                    'gross_weight'  =>  5,
                    'max_net_weight'=>  4,
                    'true_price'    =>  5.8,
                    'shipping_costs'=>  7
                ]
            ]
        ],

        [
            'shipper_name'          =>  'DHL Europake',
            'shipper_id'            =>  'dhleuro',
            'sender_country'        =>  'DE',
            'receiver_country'      =>  'CH',
            'currency'              =>  'EUR',
            'weight_unit'           =>  'kg',
            'max_total_weight'      =>  50,
            'parcel_max_weight'     =>  26,
            'single_part_max_weight'=>  26,
            'customs_administration'      =>  24,
            'included_custom_tariff_number'      =>  3,
            'additional_tariff_number_costs'     =>  4.00,
            'duties_percentage'    =>   2,
            'weight_and_costs'     =>  [
                [
                    'gross_weight'  =>  30,
                    'max_net_weight'=>  26,
                    'true_price'    =>  36.64,
                    'shipping_costs'=>  38.14
                ],
                [
                    'gross_weight'  =>  20,
                    'max_net_weight'=>  17,
                    'true_price'    =>  28.64,
                    'shipping_costs'=>  30.14
                ],
                [
                    'gross_weight'  =>  10,
                    'max_net_weight'=>  8,
                    'true_price'    =>  20.64,
                    'shipping_costs'=>  22.14
                ],
                [
                    'gross_weight'  =>  5,
                    'max_net_weight'=>  4,
                    'true_price'    =>  16.64,
                    'shipping_costs'=>  18.14
                ]

            ]

        ],


        [
            'shipper_name'          =>  'CH Post',
            'shipper_id'            =>  'ch_post',
            'sender_country'        =>  'CH',
            'receiver_country'      =>  'CH',
            'currency'              =>  'CHF',
            'weight_unit'           =>  'kg',
            'max_total_weight'      =>  26,
            'parcel_max_weight'     =>  26,
            'single_part_max_weight'=>  26,
            'customs_administration'      =>  0,
            'included_custom_tariff_number'      =>  0,
            'additional_tariff_number_costs'     =>  0,
            'duties_percentage'    =>   0,
            'weight_and_costs'     =>  [
                [
                    'gross_weight'  =>  30,
                    'max_net_weight'=>  26,
                    'true_price'    =>  22,
                    'shipping_costs'=>  24
                ],
                [
                    'gross_weight'  =>  20,
                    'max_net_weight'=>  17,
                    'true_price'    =>  15,
                    'shipping_costs'=>  17
                ],
                [
                    'gross_weight'  =>  10,
                    'max_net_weight'=>  8,
                    'true_price'    =>  10,
                    'shipping_costs'=>  11
                ],
                [
                    'gross_weight'  =>  5,
                    'max_net_weight'=>  4,
                    'true_price'    =>  9,
                    'shipping_costs'=>  10
                ]
            ]
        ],

        [
            'shipper_name'          =>  'CH Post',
            'shipper_id'            =>  'ch_post',
            'sender_country'        =>  'CH',
            'receiver_country'      =>  'LI',
            'currency'              =>  'CHF',
            'weight_unit'           =>  'kg',
            'max_total_weight'      =>  26,
            'parcel_max_weight'     =>  26,
            'single_part_max_weight'=>  26,
            'customs_administration'      =>  0,
            'included_custom_tariff_number'      =>  0,
            'additional_tariff_number_costs'     =>  0,
            'duties_percentage'    =>   0,
            'weight_and_costs'     =>  [
                [
                    'gross_weight'  =>  30,
                    'max_net_weight'=>  26,
                    'true_price'    =>  22,
                    'shipping_costs'=>  24
                ],
                [
                    'gross_weight'  =>  20,
                    'max_net_weight'=>  17,
                    'true_price'    =>  15,
                    'shipping_costs'=>  17
                ],
                [
                    'gross_weight'  =>  10,
                    'max_net_weight'=>  8,
                    'true_price'    =>  10,
                    'shipping_costs'=>  11
                ],
                [
                    'gross_weight'  =>  5,
                    'max_net_weight'=>  4,
                    'true_price'    =>  9,
                    'shipping_costs'=>  10
                ]
            ]
        ],

        [
            'shipper_name'          =>  'CH Post',
            'shipper_id'            =>  'ch_post',
            'sender_country'        =>  'LI',
            'receiver_country'      =>  'CH',
            'currency'              =>  'CHF',
            'weight_unit'           =>  'kg',
            'max_total_weight'      =>  26,
            'parcel_max_weight'     =>  26,
            'single_part_max_weight'=>  26,
            'customs_administration'      =>  0,
            'included_custom_tariff_number'      =>  0,
            'additional_tariff_number_costs'     =>  0,
            'duties_percentage'    =>   0,
            'weight_and_costs'     =>  [
                [
                    'gross_weight'  =>  30,
                    'max_net_weight'=>  26,
                    'true_price'    =>  22,
                    'shipping_costs'=>  24
                ],
                [
                    'gross_weight'  =>  20,
                    'max_net_weight'=>  17,
                    'true_price'    =>  15,
                    'shipping_costs'=>  17
                ],
                [
                    'gross_weight'  =>  10,
                    'max_net_weight'=>  8,
                    'true_price'    =>  10,
                    'shipping_costs'=>  11
                ],
                [
                    'gross_weight'  =>  5,
                    'max_net_weight'=>  4,
                    'true_price'    =>  9,
                    'shipping_costs'=>  10
                ]
            ]
        ],

        [
            'shipper_name'          =>  'CH Post',
            'shipper_id'            =>  'ch_post',
            'sender_country'        =>  'LI',
            'receiver_country'      =>  'LI',
            'currency'              =>  'CHF',
            'weight_unit'           =>  'kg',
            'max_total_weight'      =>  26,
            'parcel_max_weight'     =>  26,
            'single_part_max_weight'=>  26,
            'customs_administration'      =>  0,
            'included_custom_tariff_number'      =>  0,
            'additional_tariff_number_costs'     =>  0,
            'duties_percentage'    =>   0,
            'weight_and_costs'     =>  [
                [
                    'gross_weight'  =>  30,
                    'max_net_weight'=>  26,
                    'true_price'    =>  22,
                    'shipping_costs'=>  24
                ],
                [
                    'gross_weight'  =>  20,
                    'max_net_weight'=>  17,
                    'true_price'    =>  15,
                    'shipping_costs'=>  17
                ],
                [
                    'gross_weight'  =>  10,
                    'max_net_weight'=>  8,
                    'true_price'    =>  10,
                    'shipping_costs'=>  11
                ],
                [
                    'gross_weight'  =>  5,
                    'max_net_weight'=>  4,
                    'true_price'    =>  9,
                    'shipping_costs'=>  10
                ]
            ]
        ],
    ]

];
