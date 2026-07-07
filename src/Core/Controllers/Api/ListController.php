<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Type\TypeRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Repositories\Product\ManufacturerRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\Product\VendorRepositoryInterface;
use App\Core\Repositories\Post\PostRepositoryInterface;

class ListController extends ApiController
{
    private TypeRepositoryInterface $typeRepository;
    private SiteRepositoryInterface $siteRepository;
    private ManufacturerRepositoryInterface $manufacturerRepository;
    private VendorRepositoryInterface $vendorRepository;
    private ProductRepositoryInterface $productRepository;
    private PostRepositoryInterface $postRepository;

    public function __construct(
        TypeRepositoryInterface $typeRepository,
        SiteRepositoryInterface $siteRepository,
        ManufacturerRepositoryInterface $manufacturerRepository,
        VendorRepositoryInterface $vendorRepository,
        ProductRepositoryInterface $productRepository,
        PostRepositoryInterface $postRepository
    )
    {
        parent::__construct();
        $this->typeRepository = $typeRepository;
        $this->siteRepository = $siteRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->vendorRepository = $vendorRepository;
        $this->productRepository = $productRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function types(Request $request): Response
    {
        $types = $this->typeRepository->getTypes();
        return $this->renderResponse($types);
    }

    public function sites(Request $request): Response
    {
        $sites = $this->siteRepository->findAll();
        return $this->renderResponse($sites);
    }

    public function manufacturers(Request $request): Response
    {
        $manufacturers = $this->manufacturerRepository->findAll();
        return $this->renderResponse($manufacturers);
    }

    public function vendors(Request $request): Response
    {
        $vendors = $this->vendorRepository->findAll();
        return $this->renderResponse($vendors);
    }

    public function productTags(Request $request): Response
    {
        $tags = $this->productRepository->getTags();
        return $this->renderResponse($tags);
    }

    public function productFinishes(Request $request): Response
    {
        $finishes = $this->productRepository->getFinishes();
        return $this->renderResponse($finishes);
    }

    public function postTags(Request $request): Response
    {
        $tags = $this->postRepository->getTags();
        return $this->renderResponse($tags);
    }
    
}
