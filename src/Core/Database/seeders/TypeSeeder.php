<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Type\TypeRepositoryInterface;
use Illuminate\Container\Container;

class TypeSeeder
{
    private TypeRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        // Form Inputs
        ['type' => 'AutoComplete', 'sort_order' => 1],
        ['type' => 'CascadeSelect', 'sort_order' => 2],
        ['type' => 'Checkbox', 'sort_order' => 3],
        ['type' => 'ColorPicker', 'sort_order' => 4],
        ['type' => 'DatePicker', 'sort_order' => 5],
        ['type' => 'Editor', 'sort_order' => 6],
        ['type' => 'FileUpload', 'sort_order' => 7],
        ['type' => 'InputMask', 'sort_order' => 11],
        ['type' => 'InputNumber', 'sort_order' => 12],
        ['type' => 'InputOtp', 'sort_order' => 13],
        ['type' => 'InputText', 'sort_order' => 14],
        ['type' => 'Knob', 'sort_order' => 16],
        ['type' => 'Listbox', 'sort_order' => 17],
        ['type' => 'MultiSelect', 'sort_order' => 18],
        ['type' => 'Password', 'sort_order' => 19],
        ['type' => 'RadioButton', 'sort_order' => 20],
        ['type' => 'Rating', 'sort_order' => 21],
        ['type' => 'Select', 'sort_order' => 22],
        ['type' => 'SelectButton', 'sort_order' => 23],
        ['type' => 'Slider', 'sort_order' => 24],
        ['type' => 'Textarea', 'sort_order' => 25],
        ['type' => 'ToggleButton', 'sort_order' => 26],
        ['type' => 'ToggleSwitch', 'sort_order' => 27],
        ['type' => 'FileUpload', 'sort_order' => 28],
        ['type' => 'JSON', 'sort_order' => 29],
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TypeRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
} 