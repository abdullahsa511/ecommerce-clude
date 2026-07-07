<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Job\JobRepositoryInterface;
use Illuminate\Container\Container;

class JobSeeder
{
    private JobRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        // Sample Jobs
        [
            'language_id' => 1,
            'type' => 'Fitout',
            'reference' => '7SEB6',
            'job_title' => 'Melbourne Cricket Club Fitout',
            'description' => 'Complete fitout project for Melbourne Cricket Club in East Melbourne. PO 299540.',
            'company' => 'Melbourne Cricket Club',
            'account_manager_id' => 1,
            'account_manager_name' => 'Ben Chow',
            'status' => 'active',
            'value' => 125000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Healthcare',
            'reference' => '6HLC2',
            'job_title' => 'Uniting Healthcare Facility',
            'description' => 'Healthcare facility fitout project for Uniting in Bateau Bay.',
            'company' => 'Uniting',
            'account_manager_id' => 2,
            'account_manager_name' => 'Aimee Hu',
            'status' => 'active',
            'value' => 95000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Commercial',
            'reference' => '3FRC1',
            'job_title' => 'Canopy Fitouts - Link Wealth Group',
            'description' => 'Commercial office fitout for Link Wealth Group in Kew East.',
            'company' => 'Canopy Fitouts',
            'account_manager_id' => 3,
            'account_manager_name' => 'Mike Davis',
            'status' => 'active',
            'value' => 180000.00
        ],
        [
            'language_id' => 1,
            'type' => 'E-commerce',
            'reference' => '8MJJ1',
            'job_title' => 'Web Order Processing',
            'description' => 'Online order processing and fulfillment for Jason Coffey.',
            'company' => 'Krost Online',
            'account_manager_id' => 1,
            'account_manager_name' => 'Ben Chow',
            'status' => 'active',
            'value' => 2500.00
        ],
        [
            'language_id' => 1,
            'type' => 'Furniture',
            'reference' => '2KBC7',
            'job_title' => 'Gareth Krost - Cane Chairs',
            'description' => 'Supply and installation of cane chairs for Gareth Krost project.',
            'company' => 'Gareth Krost',
            'account_manager_id' => 2,
            'account_manager_name' => 'Aimee Hu',
            'status' => 'active',
            'value' => 15000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Design',
            'reference' => '3NLR9',
            'job_title' => 'PC Design - European Energy',
            'description' => 'Design and fitout services for European Energy project.',
            'company' => 'PC Design',
            'account_manager_id' => 3,
            'account_manager_name' => 'Luciana Verdi',
            'status' => 'active',
            'value' => 75000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Commercial',
            'reference' => '8YXT9',
            'job_title' => 'Group GSA Office Fitout',
            'description' => 'Office fitout project for Group GSA at Level 2, 155 Clarence Street, Sydney.',
            'company' => 'Group GSA',
            'account_manager_id' => 1,
            'account_manager_name' => 'Gareth Krost',
            'status' => 'active',
            'value' => 220000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Retail',
            'reference' => '8WKN7',
            'job_title' => 'Senda Retail Fitout',
            'description' => 'Retail store fitout and furniture installation for Senda.',
            'company' => 'Senda',
            'account_manager_id' => 2,
            'account_manager_name' => 'Aimee Hu',
            'status' => 'active',
            'value' => 85000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Furniture',
            'reference' => '2PET3',
            'job_title' => 'Perfect Furniture Supply',
            'description' => 'Furniture supply and installation for Perfect project.',
            'company' => 'Perfect',
            'account_manager_id' => 3,
            'account_manager_name' => 'Mike Davis',
            'status' => 'active',
            'value' => 45000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Healthcare',
            'reference' => '3DOJ2',
            'job_title' => 'Area Interiors - Independent Living Village',
            'description' => 'Healthcare facility fitout for independent living village.',
            'company' => 'Area Interiors',
            'account_manager_id' => 1,
            'account_manager_name' => 'Ben Chow',
            'status' => 'active',
            'value' => 165000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Furniture',
            'reference' => '7YHU6',
            'job_title' => 'Homelife Furnishings - Chairs',
            'description' => 'Supply and installation of chairs for Homelife Furnishings.',
            'company' => 'Homelife Furnishings',
            'account_manager_id' => 2,
            'account_manager_name' => 'Caroline Sarafoglou',
            'status' => 'active',
            'value' => 28000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Design',
            'reference' => '3ONU9',
            'job_title' => 'Jheeno Mari Olidar - Cortex',
            'description' => 'Design and fitout services for Cortex project.',
            'company' => 'Jheeno Mari Olidar',
            'account_manager_id' => 3,
            'account_manager_name' => 'Stephen Watson',
            'status' => 'active',
            'value' => 95000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Commercial',
            'reference' => '7LIT6',
            'job_title' => 'KK Partners Group - Brighton',
            'description' => 'Commercial office fitout for KK Partners Group in Brighton.',
            'company' => 'KK Partners Group',
            'account_manager_id' => 1,
            'account_manager_name' => 'Ben Chow',
            'status' => 'active',
            'value' => 140000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Retail',
            'reference' => '7OKB9',
            'job_title' => 'Big W - 176 Plumpton NSW',
            'description' => 'Retail store fitout for Big W at 176 Plumpton, NSW 2761.',
            'company' => 'Big W',
            'account_manager_id' => 2,
            'account_manager_name' => 'Ben Chow',
            'status' => 'active',
            'value' => 185000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Retail',
            'reference' => '6DQD5',
            'job_title' => 'Big W - 391 Queen Vic',
            'description' => 'Retail store fitout for Big W at 391 Queen Victoria location.',
            'company' => 'Big W',
            'account_manager_id' => 3,
            'account_manager_name' => 'Ben Chow',
            'status' => 'active',
            'value' => 175000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Healthcare',
            'reference' => '3HQL1',
            'job_title' => 'Interite - Kalwun Burleigh Heads',
            'description' => 'Healthcare facility fitout at 14 Kortum Drive, Burleigh Heads.',
            'company' => 'Interite',
            'account_manager_id' => 1,
            'account_manager_name' => 'Lana Fonseca',
            'status' => 'active',
            'value' => 120000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Commercial',
            'reference' => '7IZB7',
            'job_title' => 'Vemi - GPT Hames Sharley',
            'description' => 'Commercial fitout for GPT Hames Sharley at Malvern Central.',
            'company' => 'Vemi',
            'account_manager_id' => 2,
            'account_manager_name' => 'Param Singh',
            'status' => 'active',
            'value' => 135000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Technology',
            'reference' => '2GOH4',
            'job_title' => 'AI Star Technology - Laverton North',
            'description' => 'Technology company fitout in Laverton North.',
            'company' => 'AI Star Technology',
            'account_manager_id' => 3,
            'account_manager_name' => 'Mike Davis',
            'status' => 'active',
            'value' => 110000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Commercial',
            'reference' => '6RHZ2',
            'job_title' => 'CBRE - CTM Office Fitout',
            'description' => 'Office fitout for CBRE - CTM at L13, 255 Elizabeth St.',
            'company' => 'CBRE',
            'account_manager_id' => 1,
            'account_manager_name' => 'Lana Fonseca',
            'status' => 'active',
            'value' => 195000.00
        ],
        [
            'language_id' => 1,
            'type' => 'Furniture',
            'reference' => '3MJX3',
            'job_title' => 'Wework - Tash Chair Member Pricing',
            'description' => 'Furniture supply for Wework with member pricing structure.',
            'company' => 'Wework',
            'account_manager_id' => 2,
            'account_manager_name' => 'Aimee Hu',
            'status' => 'active',
            'value' => 35000.00
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(JobRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
} 