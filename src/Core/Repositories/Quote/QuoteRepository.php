<?php

declare(strict_types=1);

namespace App\Core\Repositories\Quote;

use PDO;
use App\Core\Models\Quote\Quote;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Quote\QuoteData;
use App\Core\Models\Quote\QuoteItem;
use App\Core\Models\Quote\QuoteResponse;
use App\Core\Validation\QuoteDataValidation;
use League\Csv\Reader;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;

class QuoteRepository extends BaseRepository implements QuoteRepositoryInterface
{
    private $quoteItem;
    private CustomerRepositoryInterface $customerRepository;
    
    public function __construct(PDO $db, QuoteItem $quoteItem, CustomerRepositoryInterface $customerRepository)
    {
        parent::__construct($db, 'quote', Quote::class);
        $this->quoteItem = $quoteItem;
        $this->quoteItem->setDb($db);
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get all quotes
     *
     * @return array
     */
    public function all(): array
    {
        return $this->model->orderBy('created_at', 'DESC')->findAll();
    }

    public function findAll(): array
    {
        $quotes = $this->model
            ->join('customer', 'customer.customer_id', '=', 'quote.customer_id')
            ->with(['item'])
            ->whereNull('quote.deleted_at')
            ->orderBy('created_at', 'DESC')
            ->select(['quote.*', 'customer.name as customer_name'])
            ->findAll();
        return $quotes;
    }
    // public function findAllQuotes(): array
    // {
    //     $quotes = $this->model
    //         ->with(['item'])
    //         ->whereNull('quote.deleted_at')
    //         ->orderBy('created_at', 'DESC')
    //         ->findAll();

    //     // Convert each quote array to QuoteResponse
    //     $results = [];
    //     foreach ($quotes as $quoteData) {
    //         $quoteObj = (object) $quoteData;
    //         $results[] = new QuoteResponse($quoteObj);
    //     }

    //     return $results;
    // }

    // public function findAllQuotes(): array
    // {
    //     $quotes = $this->model
    //         ->with(['item'])
    //         ->whereNull('quote.deleted_at')
    //         ->orderBy('created_at', 'DESC')
    //         ->findAll();

    //     $results = [];
    //     foreach ($quotes as $quoteData) {
    //         $quoteObj = (object) $quoteData;

    //         // decode the eager-loaded items (JSON string → PHP array)
    //         $items = json_decode($quoteData['item'] ?? '[]', true) ?? [];

    //         // normalise each item if you need camelCase keys for Vue
    //         $items = array_map(static function (array $item) {
    //             return [
    //                 'quoteItemId' => $item['quote_item_id'] ?? null,
    //                 'productId'   => $item['product_id'] ?? null,
    //                 'description' => $item['description'] ?? '',
    //                 'quantity'    => (int) ($item['quantity'] ?? 0),
    //                 'unitPrice'   => (float) ($item['unit_price'] ?? 0),
    //                 'totalPrice'  => (float) ($item['total_price'] ?? 0),
    //                 'photo'       => $item['photo'] ?? null,
    //             ];
    //         }, $items);

    //         $results[] = [
    //             'quote' => new QuoteResponse($quoteObj),
    //             'items' => $items,
    //         ];
    //     }

    //     return $results;
    // }



    /**
     * Get quotes by company ID
     *
     * @param int $companyId
     * @return array
     */
    public function findByCompanyId(int $companyId): array
    {
        return $this->model->where('company_id', '=', $companyId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get quotes by user ID
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        return $this->model->where('user_id', '=', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get quotes by user ID
     *
     * @param int $userId
     * @return array
     */
    public function getIdByUuid(string $uuid): ?Quote
    {
        return $this->model->where('uuid', '=', $uuid)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Get quote by reference number
     *
     * @param string $referenceNumber
     * @return Quote|null
     */
    public function findByReferenceNumber(string $referenceNumber): ?Quote
    {
        return $this->model->where('reference_number', '=', $referenceNumber)->first();
    }

    public function createQuote(QuoteData $quoteData): Quote
    {
        $quoteDataArray = $quoteData->toArray();
        $quoteDataArray['uuid'] = $this->generateUuid();
        $quote = $this->model->create($quoteDataArray);
        return $quote;
    }

    public function updateQuote(QuoteData $quoteData): Quote
    {
        $quoteDataArray = $quoteData->toArray();
        $quote = $this->model->find($quoteDataArray['quote_id']);
        $quote = $quote->update($quoteDataArray);

        return $quote;
    }

    // public function showQuote(int $quoteId): array
    // {
    //     $quote = $this->model->where('quote_id', '=', $quoteId)
    //         ->first();

    //     return $quote->toArray();
    // }

    private function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }

    public function insertQuotes(array $data): bool
    {
        $this->db->beginTransaction();
        $this->model->insert($data['quotes']);
        $this->quoteItem->insert($data['quoteItems']);
        $this->db->commit();
        return true;
    }

    public function getQuotePaymentData(int $quoteId): array
    {
        $quote = $this->model->where('quote_id', '=', $quoteId)->first();
        $quoteItems = $this->quoteItem->where('quote_id', '=', $quoteId)->findAll();

        if (!$quote) {
            throw new \Exception("Quote not found with ID: {$quoteId}");
        }

        // Calculate totals from quote items
        $subTotal = 0;
        foreach ($quoteItems as $item) {
            $subTotal += $item['total_price'];
        }

        // Calculate GST (assuming 10% GST rate)
        $gstRate = 0.10;
        $gstAmount = $subTotal * $gstRate;
        $totalIncGst = $subTotal + $gstAmount;

        // Calculate deposit amount based on deposit_percentage
        $depositPercentage = $quote->deposit_percentage ?? 50;
        $depositAmount = $totalIncGst * ($depositPercentage / 100);

        $results = [];
        $results['order_number'] = $quote->reference_number ?? '#' . $quoteId;
        $results['sub_total'] = number_format($subTotal, 2) . ' $';
        $results['gst'] = number_format($gstAmount, 2) . ' $';
        $results['total_inc_gst'] = number_format($totalIncGst, 2) . ' $';

        $results['payment'] = [
            [
                'type' => 'Credit Card',
                'active' => true,
                'choices' => [
                    [
                        'label' => $depositPercentage . '% Deposit Payment Due',
                        'amount' => number_format($depositAmount, 2) . ' $',
                        'checked' => true,
                    ],
                    [
                        'label' => 'Full Payment',
                        'amount' => number_format($totalIncGst, 2) . ' $',
                        'checked' => false,
                    ],
                ],
                'to_pay_now' => number_format($depositAmount, 2) . ' $',
                'supported_cards' => ['Mastercard', 'Visa'],
                'pay_now_url' => 'contact.html',
            ],
            [
                'type' => 'Direct Deposit',
                'active' => false,
                'details' => 'Direct Deposit Payment Details...',
            ],
            [
                'type' => 'B Pay',
                'active' => false,
                'details' => 'B Pay Payment Details...',
            ],
            [
                'type' => 'Cheque',
                'active' => false,
                'details' => 'Cheque Payment Details...',
            ]
        ];
        $results['powered_by_image'] = '/img/modal/paypal-img.png';

        // Map billing information from database
        $results['contact_information_name'] = $quote->organisation_name ?? 'N/A';
        $results['contact_information_email'] = 'contact@example.com'; // This field doesn't exist in quote table
        $results['shipping_phone'] = '(207) 555-0119'; // This field doesn't exist in quote table
        $results['shipping_address'] = $quote->ship_address ?? 'N/A';
        $results['shipping_suburb'] = $quote->ship_suburb ?? 'N/A';
        $results['shipping_state'] = $quote->ship_state ?? 'N/A';
        $results['shipping_country'] = $quote->ship_country ?? 'N/A';
        $results['billing_phone'] = '(207) 555-0119'; // This field doesn't exist in quote table
        $results['billing_suburb'] = $quote->bill_suburb ?? 'N/A';
        $results['billing_state'] = $quote->bill_state ?? 'N/A';
        $results['billing_country'] = $quote->bill_country ?? 'N/A';

        return [
            'quote' => $quote,
            'quoteItems' => $quoteItems,
            'paymentData' => $results
        ];
    }

    public function getActiveQuotes(int $customerId): array
    {
        $quotes = $this->model->where('user_id', '=', $customerId)
            ->where('quote_status_id', '=', 1) // Assuming status_id 1 is active
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if (empty($quotes)) {
            return [
                'page_title' => 'Active Quotes',
                'quotes' => [],
                'message' => 'No active quotes found'
            ];
        }

        $results = [
            'page_title' => 'Active Quotes',
            'quotes' => []
        ];

        foreach ($quotes as $quote) {
            // Get quote items for this quote
            $quoteItems = $this->quoteItem->where('quote_id', '=', $quote->quote_id)->findAll();

            // Calculate totals
            $subTotal = 0;
            $items = [];

            foreach ($quoteItems as $item) {
                $itemTotal = $item->quantity * $item->unit_price;
                $subTotal += $itemTotal;

                $items[] = [
                    'image' => $item->photo ?? '/img/datatable/default-item.png',
                    'alt' => $item->description,
                    'description' => $item->description,
                    'quantity' => (string)$item->quantity,
                    'unit_price' => '$' . number_format($item->unit_price, 2),
                    'item_total' => '$' . number_format($itemTotal, 2),
                    'comment_icon' => '/img/datatable/comment-icon.png'
                ];
            }

            // Calculate GST and total
            $gstRate = 0.10; // 10% GST
            $gstAmount = $subTotal * $gstRate;
            $totalIncGst = $subTotal + $gstAmount;

            $quoteData = [
                'quote_summary' => [
                    'title' => $quote->job_title ?? 'Quote #' . $quote->reference_number,
                    'id' => $quote->reference_number ?? '#' . $quote->quote_id,
                    'description' => $quote->quote_description ?? 'No description available',
                    'account' => $quote->organisation_name ?? 'N/A',
                    'amount' => '$' . number_format($totalIncGst, 2),
                    'created_date' => $quote->created_at ? date('F jS, Y', strtotime($quote->created_at)) : 'N/A',
                    'actions' => [
                        [
                            'text' => 'View Quote',
                            'url' => 'quote/view/' . $quote->quote_id,
                            'class' => 'th-btn-gray text-capitalize'
                        ],
                        [
                            'text' => 'Accept Quote',
                            'url' => 'quote/accept/' . $quote->quote_id,
                            'class' => 'th-btn-primary text-capitalize'
                        ]
                    ]
                ],
                'table' => [
                    'headers' => ['Item', 'Description', 'QTY', 'Unit Price', 'Total', 'Section Total'],
                    'section_title' => 'Items',
                    'section_total' => $subTotal,
                    'items' => $items
                ],
                'footer' => [
                    'sub_total' => '$' . number_format($subTotal, 2),
                    'gst' => '$' . number_format($gstAmount, 2),
                    'total_inc_gst' => '$' . number_format($totalIncGst, 2)
                ],
                'team_managers' => [
                    'title' => 'Team Managers',
                    'members' => [
                        [
                            'image' => '/img/contact/member-1.png',
                            'name' => 'Account Manager', // This could be pulled from account_manager_id if we had a users table
                            'position' => 'Account Manager',
                            'phone_icon' => 'fa-solid fa-phone',
                            'email_icon' => 'fa-solid fa-envelope'
                        ],
                        [
                            'image' => '/img/contact/member-avatar.png',
                            'name' => 'Project Manager', // This could be pulled from project_manager_id if we had a users table
                            'position' => 'Project Manager',
                            'phone_icon' => 'fa-solid fa-phone',
                            'email_icon' => 'fa-solid fa-envelope'
                        ]
                    ]
                ],
                'quote_card' => [
                    'title' => $quote->job_title ?? 'Quote #' . $quote->reference_number,
                    'id' => $quote->reference_number ?? '#' . $quote->quote_id,
                    'description' => $quote->quote_description ?? 'No description available',
                    'account' => $quote->organisation_name ?? 'N/A',
                    'amount' => '$' . number_format($totalIncGst, 2),
                    'created_date' => $quote->created_at ? date('F jS, Y', strtotime($quote->created_at)) : 'N/A',
                    'add_comment_url' => 'quote/comment/' . $quote->quote_id,
                    'view_quote_url' => 'quote/view/' . $quote->quote_id,
                    'accept_quote_url' => 'quote/accept/' . $quote->quote_id
                ]
            ];

            $results['quotes'][] = $quoteData;
        }

        return $results;
    }

    // import data
    public function importCSVs(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new \Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();

        $validData = [
            'quote' => [],
        ];
        $showFrontendValidData = ['quote' => []];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['quote_id', 'uuid'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'quote_id', 'uuid');
        $existingGroupIds = array_values($existingGroupMap);

        $existingDataMaps = [
            'quoteMap' => $existingGroupMap,
            'quoteIds' => $existingGroupIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                

               
                $validator = new QuoteDataValidation($record, $existingDataMaps);
                $validated = $validator->validate();

                // If validation fails, store record and error info in $invalid
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV row count starts at 1 and includes header
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getQuoteUniqueIdentifier();

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->quote;
                    $showFrontendExistingData[] = $record;
                } else {
                    $validData['quote'][] = (array) ['uuid' => $validated->quote->uuid]; // insert data 
                    $validData['billingAddress'][] = (array) [];
                    $validData['customerDetails'][] = (array) [];
                    $validData['quoteDetails'][] = (array) [];
                    $validData['quoteTotals'][] = (array) [];
                    $validData['shippingAddress'][] = (array) [];



                    $contentData = (array) $validated->lengthType;

                    $showFrontendValidData['length_type'][] = $contentData;
                }
                
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        // $result = $this->attributeGroupAndContentInsertorUpdate($validData, $languageMap);
        try {
            $this->db->beginTransaction();

            if (count($validData['quote']) > 0) {
                $this->model->upsert($validData['quote'], ['uuid']);
                $quoteCodes = array_column($validData['quote'], 'uuid');
                $this->model->clearQuery();
                $this->model->softDelete(false);

                $quoteData = $this->model->whereIn('code', $quoteCodes)->select(['quote_id', 'uuid'])->findAll(false);
                $quoteData = array_column($quoteData, 'quote_id', 'uuid');
            }

            // if(count($validData['length_type_content']) > 0){
            //     foreach($validData['length_type_content'] as &$content){
            //         $content['length_type_id'] = $quoteData[$content['name']];
            //         // unset($content['code']);
            //     }
            //     $this->lengthTypeContent->upsert($validData['length_type_content'], ['length_type_id', 'language_id']);
            // }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update quote: " . $e->getMessage());
        }
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['quote']),
            'valid_data' => $showFrontendValidData['quote'],
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'quotes' => [
                'inserted_count' => count($validData['quote']),
                'valid_data' => $validData['quote']
            ],
            'quotes' => [
                'inserted_count' => count($showFrontendValidData['quote']),
                'valid_data' => $showFrontendValidData['quote']
            ],
            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['quote']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'quote_processed' => count($validData['quote']),
                'content_records_created' => $validData['quote'],
                'errors' => count($invalid),
            ],

        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['language_id'] = 1;
        $defaultFields['length_type_id'] = null;

        return $defaultFields;
    }
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['length_type_id']) && $record['length_type_id'] ? $record : array_merge($defaultFields, $record);
    }
    // 6. dashboard/recent-quotes-widget
    /**
     * Get the recent quotes widget data
     *
     * @param int $limit
     * @return array
     */
    public function getRecentQuotesWidget($limit = 20): array
    {
        // Quote Reference, Description, Customer, Status, Created Date, Updated Date, Amount
        // SQL (prepared once, reused) // parameters: limit.
        $quotes = $this->model
        ->join('user', 'user.user_id', '=', 'quote.user_id')
        ->join('customer', 'customer.customer_id', '=', 'quote.customer_id')
        ->select([
            'quote.quote_id as id',
            'customer.name as customer_name',
            'quote.reference_number as reference',
            'quote.quote_description as description',
            'DATE_FORMAT(quote.created_at, "%M %d, %Y") as date',
            'quote.total_sp_inc_gst as amount',
            'CONCAT(user.first_name," ", user.last_name) as manager_name',
            'quote.updated_at as updated_at',
            '(CASE
                WHEN quote.quote_status_id = 1 THEN "pending"
                WHEN quote.quote_status_id = 2 THEN "accepted"
                WHEN quote.quote_status_id = 3 THEN "rejected"
                ELSE "no status"
            END) as status'
         ])
        // ->where('quote_status_id', '=', 1) // pending status id = 1
        ->orderBy('quote.created_at', 'DESC')
        ->limit($limit)
        ->findAll();
        // return the recent quotes widget data
        return $quotes;
    }

    /**
     * Get the quote by ID
     *
     * @param int $id
     * @return array
     */
    public function getQuoteById(int $id): array
    {
        $quotes = $this->model
        ->join('user', 'user.user_id', '=', 'quote.user_id')
        ->join('customer', 'customer.user_id', '=', 'user.user_id')
        ->select(['quote.quote_id as id','customer.name as customer_name','quote.reference_number as reference', 'quote.quote_description as description',
            'DATE_FORMAT(quote.created_at, "%M %d, %Y") as date','quote.total_sp_inc_gst as amount','CONCAT(user.first_name," ", user.last_name) as manager_name', 'quote.updated_at as updated_at',
            '(CASE
                WHEN quote.quote_status_id = 1 THEN "pending"
                WHEN quote.quote_status_id = 2 THEN "accepted"
                WHEN quote.quote_status_id = 3 THEN "rejected"
                ELSE "no status"
            END) as status'
         ])
        ->where('quote.quote_id', '=', $id)
        ->orderBy('quote.created_at', 'DESC')
        ->findAll();
        $quote = $quotes[0];
        $items = $this->quoteItem
        ->select(['quote_item.*', 'product.product_code','product.description','product.price as product_price','product.image'])
        ->join('product', 'product.product_id', '=', 'quote_item.product_id')
        ->where('quote_id', '=', $id)
        ->findAll();

        foreach ($items as &$item) {
            if (!empty($item['image'])) {
                $item['image'] = json_decode($item['image'], true);
    
                if (is_array($item['image']) && isset($item['image'][0]['objectURL'])) {
                    $item['image_url'] = $item['image'][0]['objectURL'];
                }
            } else {
                $item['image'] = [];
            }
            unset($item['image']);
        }

        $quote['items'] = $items;
        return $quote;
    }

    public function getCustomerQuotesForComponent(array $params): array
    {
        $userId = $params['user_id'];
        // $customer = $this->customerRepository->findByUserId($userId);
        // $customerId = $customer['customer_id'];

        // $customerId = $params['customer_id'] ?? '1';
        $sort_field = '?sort=created_at&order=desc';
        $query = $this->model
            ->join('quote_item', 'quote_item.quote_id', '=', 'quote.quote_id')
            ->join('product', 'product.product_id', '=', 'quote_item.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('quote.user_id', '=', $userId)
            ->select([
                'quote.quote_id',
                'quote.uuid',
                'quote.job_title',
                'quote.reference_number',
                'quote.quote_description',
                'quote.total_sp_inc_gst',
                'product.image',
                'product.product_code',
                'product_content.name',
                'product.description',
                'quote_item.quantity',
                'quote.created_at',
                'quote.quote_status_id',
            ]);
            if(isset($params['sort']) && isset($params['order'])){
                $sort_field = '?sort='.strtolower($params['sort']).'&order='.strtolower($params['order']);
                $query->orderBy('quote.'.$params['sort'], $params['order']);
            }
            $query->groupBy('quote.quote_id');
            $quotes = $query->findAll();


            $quotes_data = [];
            foreach($quotes as $quote){
                $quote_id = isset($quote['quote_id']) ? $quote['quote_id'] : null;
                $quote_uuid = isset($quote['uuid']) ? $quote['uuid'] : null;
                $quote_job_title = isset($quote['job_title']) ? $quote['job_title'] : null;
                $product_image = isset($quote['image']) ? json_decode($quote['image'], true) : null;
                $product_name = isset($quote['name']) ? $quote['name'] : null;
                $product_code = isset($quote['product_code']) ? $quote['product_code'] : null;
                $product_color = isset($quote['color']) ? $quote['color'] : null;
                $quantity = isset($order['quantity']) ? $order['quantity'] : null;
                $product_image = isset($product_image[0]['objectURL']) ? $product_image[0]['objectURL'] : null;
    
                $quotes_data[] = [
                    'id' => $quote_id,
                    'uuid' => $quote_uuid,
                    'job_title' => $quote_job_title,
                    'image' => $product_image ?? '/img/account-dashboard/recent-order1.png',
                    'alt' => $product_name ?? 'quote-item',
                    'product_name' => $product_code . ' - ' . $quote['description'],
                    'item_code' => $product_code ?? 'N/A',
                    'color' => $product_color ?? 'N/A',
                    'quantity' => $quantity ?? '0',
                    'track_order_url' => '#',
                    'track_order_target' => '#offcanvasRightTop',
                    'view_details_url' => '/account/quotes/'.$quote_uuid,
                    'view_details_target' => '#offcanvasRightTop',
                    'offcanvas_id' => 'offcanvasRightTop',
                    'created_date' => date('M d, Y', strtotime($quote['created_at'])),
                    'account' => $quote['reference_number'],
                    'description' => $quote['quote_description'],
                    'amount' => number_format((float)$quote['total_sp_inc_gst'], 2, '.', ','),
                    'quote_status_id' => $quote['quote_status_id'],
                    'status' => $quote['quote_status_id'] == 2 ? 'Accepted' : 'Accept Quote',
                ];
            }

            $sort_options  = $this->getSortOptions();
            $sort_text = $sort_options[array_search($sort_field, array_column($sort_options, 'url'))]['text'] ?? 'Sort';

            if (empty($quotes)) {
                return [
                    'page_title' => 'Active Quotes',
                    'sort_options' => $sort_options ,
                    'sort_button_text' => $sort_text,
                    'quotes' => [],
                    'message' => 'No quotes found'
                ];
            }

            $results = [
                'page_title' => 'Active Quotes',
                'sort_options' => $sort_options ,
                'sort_button_text' => $sort_text,
                'quotes' => $quotes_data
            ];

        

        return $results;
    }

    private function getSortOptions(): array
    {
        return [
            [
                'text' => 'Sort by Date (Newest)',
                'url' => '?sort=created_at&order=desc'
            ],
            [
                'text' => 'Sort by Date (Oldest)',
                'url' => '?sort=created_at&order=asc'
            ],
            [
                'text' => 'Sort by Status',
                'url' => '?sort=quote_status_id&order=asc'
            ]
        ];
    }

    public function showQuote(string $uuid): array
    {
        $quote = $this->model->where('uuid', '=', $uuid)->first();

        $quote_items = $this->quoteItem
            ->join('product', 'product.product_id', '=', 'quote_item.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('quote_item.quote_id', '=', $quote->quote_id)
            ->select([
                'quote_item.*',
                'product.product_id',
                'product.image',
                'product.product_code',
                'product_content.name'
            ])
            ->findAll();

        if(!$quote){
            return [
                'page_title' => 'Show Quote',
                'quote_summary' => [],
                'table' => [
                    'items' => [],
                    'section_title' => '',
                    'section_total' => 0
                ],
                'footer' => [
                    'sub_total' => 0,
                    'gst' => 0,
                    'total_inc_gst' => 0
                ]
            ];
        }
                
        // Quote items
        $quoteData = $quote->data;
    
        // Calculate totals
        $subTotal = 0;
        $quoteItems = [];
        foreach($quote_items as $item){
            $image = isset($item['image']) ? json_decode($item['image'], true) : null;
            $photo = isset($image[0]['objectURL']) ? $image[0]['objectURL'] : null;
            $quoteItems[] = [
                'image' => $photo,
                'alt' => $item['name'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => '$' . $item['unit_price'],
                'item_total' => '$' . $item['total_price'],
                'comment_icon' => '/img/datatable/comment-icon.png'
            ];
            $subTotal += $item['total_price'];
        }

        $gst = $subTotal * 0.10; // 10% GST example
        $grandTotal = $subTotal + $gst;
    
        $quote_summary = [
            'title' => isset($quoteData->job_title) ? $quoteData->job_title : 'Quote Title',
            'id' => '#' . $quoteData->quote_id,
            'uuid' => $quoteData->uuid,
            'quote_id' => $quoteData->quote_id,
            'quote_status_id' => isset($quoteData->quote_status_id) ? $quoteData->quote_status_id : null,
            'quote_status_text' => isset($quoteData->quote_status_id) ? ($quoteData->quote_status_id == 2 ? 'Accepted' : 'Accept Quote') : 'Accept Quote',
            'quote_status_class' => isset($quoteData->quote_status_id) ? ($quoteData->quote_status_id == 2 ? 'th-btn text-capitalize btn-disabled' : 'th-btn text-capitalize bg-secondary text-white quote-track-btn') : 'th-btn text-capitalize bg-secondary text-white quote-track-btn',
            'description' => $quoteData->quote_description,
            'account' => $quoteData->reference_number,
            'amount' => '$' . number_format($grandTotal, 2),
            'created_date' => date('M d, Y', strtotime($quoteData->created_at)),
        ];

        return [
            'page_title' => 'Show Quote',
            'quote_summary' => $quote_summary,
            'table' => [
                'section_title' => 'Items',
                'section_total' => '$' . number_format($subTotal, 2),
                'items' => $quoteItems
            ],
            'footer' => [
                'sub_total' => '$' . number_format($subTotal, 2),
                'gst' => '$' . number_format($gst, 2),
                'total_inc_gst' => '$' . number_format($grandTotal, 2)
            ],
        ];
    }

    public function getQuoteAcceptance(string $quoteUuid): array
    {
        try {
            $quote = $this->model->where('uuid', '=', $quoteUuid)->first();
            if(!$quote){
                return ['success' => false, 'message' => 'Quote not found'];
            }
            $quote = $quote->update([
                'quote_status_id' => 2,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return ['success' => true, 'message' => 'Quote accepted successfully', 'data' => (array) $quote->data ?? []];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getQuoteByUuid(string $uuid): array
    {
        $quote = $this->model->where('uuid', '=', $uuid)->first();
        if(!$quote){
            return [];
        }
        return (array) $quote->data;
    }
}
