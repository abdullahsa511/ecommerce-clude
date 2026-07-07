<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Option\OptionRepositoryInterface;
use Illuminate\Container\Container;
use PDO;

class OptionSeeder
{
    private OptionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private PDO $pdo;
    
    // Options data for the 'option' table
    private array $options = [
        [
            'option_id' => 1,
            'type_id' => 4, // ColorPicker
            'type' => 'ColorPicker',
            'sort_order' => 1
        ],
        [
            'option_id' => 2,
            'type_id' => 4, // ColorPicker
            'type' => 'ColorPicker',
            'sort_order' => 2
        ],
        [
            'option_id' => 3,
            'type_id' => 4, // ColorPicker
            'type' => 'ColorPicker',
            'sort_order' => 3
        ],
        [
            'option_id' => 4,
            'type_id' => 21, // Select
            'type' => 'Select',
            'sort_order' => 4
        ],
        [
            'option_id' => 5,
            'type_id' => 21, // Select
            'type' => 'Select',
            'sort_order' => 5
        ],
        [
            'option_id' => 6,
            'type_id' => 21, // Select
            'type' => 'Select',
            'sort_order' => 6
        ]
    ];

    // Option content data for the 'option_content' table
    private array $optionContents = [
        // Material Color - option_id: 1
        [
            'option_id' => 1,
            'language_id' => 1, // Assuming English language_id = 1
            'name' => 'Material Color'
        ],
        [
            'option_id' => 1,
            'language_id' => 2, // Assuming Spanish language_id = 2
            'name' => 'Color del Material'
        ],

        // Edge Color - option_id: 2
        [
            'option_id' => 2,
            'language_id' => 1,
            'name' => 'Edge Color'
        ],
        [
            'option_id' => 2,
            'language_id' => 2,
            'name' => 'Color del Borde'
        ],

        // Color - option_id: 3
        [
            'option_id' => 3,
            'language_id' => 1,
            'name' => 'Color'
        ],
        [
            'option_id' => 3,
            'language_id' => 2,
            'name' => 'Color'
        ],

        // Texture - option_id: 4
        [
            'option_id' => 4,
            'language_id' => 1,
            'name' => 'Texture'
        ],
        [
            'option_id' => 4,
            'language_id' => 2,
            'name' => 'Textura'
        ],

        // Powder Coat - option_id: 5
        [
            'option_id' => 5,
            'language_id' => 1,
            'name' => 'Powder Coat'
        ],
        [
            'option_id' => 5,
            'language_id' => 2,
            'name' => 'Recubrimiento en Polvo'
        ],

        // Fabric - option_id: 6
        [
            'option_id' => 6,
            'language_id' => 1,
            'name' => 'Fabric'
        ],
        [
            'option_id' => 6,
            'language_id' => 2,
            'name' => 'Tela'
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(OptionRepositoryInterface::class);
        $this->pdo = $this->container->make(PDO::class);
    }

    public function seed(): void
    {    
        $this->seedOptions();
        $this->seedOptionContents();
    }

    private function seedOptions(): void
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO `option` (`option_id`, `type_id`, `type`, `sort_order`) 
                 VALUES (:option_id, :type_id, :type, :sort_order)
                 ON DUPLICATE KEY UPDATE 
                 `type_id` = VALUES(`type_id`), 
                 `type` = VALUES(`type`), 
                 `sort_order` = VALUES(`sort_order`)"
            );

            foreach ($this->options as $option) {
                $stmt->execute($option);
            }
            
            echo "Options seeded successfully.\n";
        } catch (\Exception $e) {
            echo "Error seeding options: " . $e->getMessage() . "\n";
        }
    }

    private function seedOptionContents(): void
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO `option_content` (`option_id`, `language_id`, `name`) 
                 VALUES (:option_id, :language_id, :name)
                 ON DUPLICATE KEY UPDATE 
                 `name` = VALUES(`name`)"
            );

            foreach ($this->optionContents as $content) {
                $stmt->execute($content);
            }
            
            echo "Option contents seeded successfully.\n";
        } catch (\Exception $e) {
            echo "Error seeding option contents: " . $e->getMessage() . "\n";
        }
    }
} 