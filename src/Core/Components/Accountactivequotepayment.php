<?php

/**
 * SA Technology
 *
 * Copyright (C) 2025  Shofiul Alam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace App\Core\Components;

use App\Core\System\Component\ComponentBase;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\System\Event;
use App\Core\Repositories\Quote\QuoteRepositoryInterface;

class Accountactivequotepayment extends ComponentBase {
    public static $defaultOptions = [
        'site_id'     => null,
        'language_id' => null,
    ];

    protected $options = [];

    public $cacheExpire = 0; //seconds
    private ComponentRepositoryInterface $componentRepository;
    private QuoteRepositoryInterface $quoteRepository;


    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        QuoteRepositoryInterface $quoteRepository,
        array $options = []
    ) {
        parent::__construct($options);
        $this->componentRepository = $componentRepository;
        $this->quoteRepository = $quoteRepository;
    }

    function cacheKey() {
        //disable caching
        return false;
    }
    public static function getComponentMeta()
    {
        return [
            'name' => 'accountactivequotepayment',
            'class' => self::class,
            'validOptions' => [
                'component_id',
                'quote_id'
            ],
            'filePath' => __FILE__,
            'cacheKey' => null,
            'data' => [],
            'designOnly' => false
        ];
    }

    function results($params = []) {

        $results = [];
        // $results['order_number'] = '#907526';
        // $results['sub_total'] = '59.89 $';
        // $results['gst'] = '9.89 $';
        // $results['total_inc_gst'] = '69.89 $';
        // $results['powered_by_image'] = '/img/modal/paypal-img.png';

        // $results['contact_information_name'] = 'Courtney Henry';
        // $results['contact_information_email'] = 'courtney.henry@example.com';
        // $results['shipping_phone'] = '(207) 555-0119';
        // $results['shipping_address'] = '3890 Poplar Dr.';
        // $results['shipping_suburb'] = 'Preston';
        // $results['shipping_state'] = 'NC';
        // $results['shipping_country'] = 'United States';
        // $results['billing_phone'] = '(207) 555-0119';
        // $results['billing_suburb'] = 'Preston';
        // $results['billing_state'] = 'NC';
        // $results['billing_country'] = 'United States';

        // $results['payment'] = [
        //     [
        //         'type' => 'Credit Card',
        //         'active' => true,
        //         'choices' => [
        //             [
        //                 'label' => '50% Deposit Payment Due',
        //                 'amount' => '599.89 $',
        //                 'checked' => true,
        //             ],
        //             [
        //                 'label' => 'Full Payment',
        //                 'amount' => '1299.89 $',
        //                 'checked' => false,
        //             ],
        //         ],
        //         'to_pay_now' => '599.89 $',
        //         'supported_cards' => ['Mastercard', 'Visa'],
        //         'pay_now_url' => 'contact.html',
        //     ],
        //     [
        //         'type' => 'Direct Deposit',
        //         'active' => false,
        //         'details' => 'Direct Deposit Payment Details...',
        //     ],
        //     [
        //         'type' => 'B Pay',
        //         'active' => false,
        //         'details' => 'B Pay Payment Details...',
        //     ],
        //     [
        //         'type' => 'Cheque',
        //         'active' => false,
        //         'details' => 'Cheque Payment Details...',
        //     ]
        // ];


        $component = $this->componentRepository->getComponentByName('accountactivequotepayment');
        $quoteId = $this->options['quote_id']?? null;
        if($quoteId){
            $results = $this->quoteRepository->getQuotePaymentData($quoteId);
        }
        list($component) = Event::trigger(__CLASS__,__FUNCTION__, $component);
        return $results['paymentData']?? [];
    }
}
