<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\App\KernelCli;
use App\Core\Models\Product\Product;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ModelQueryBuilderTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
  

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductRepositoryInterface::class);
    }

    public function testQueryBuilder(): void
    {
        //Simple Select * query

        // $selectAll = $this->repository->getModel()->where('product_id', '=', 61)->first();

        // $this->assertInstanceOf(Product::class, $selectAll);
        // $this->assertEquals($selectAll->product_id, 61);

        // //Query with selected Fields

        // $selectFields = $this->repository->getModel()->where('product_id', '=', 61)->select(['product_id'])->first();
        // $this->assertInstanceOf(Product::class, $selectFields);
        // $this->assertEquals($selectFields->product_id, 61);

        //Query with where clause

        // $whereClause = $this->repository->getModel()
        // ->where('product_id', '=', 61)
        // ->where('model', '=', 'SM-G998B')
        // ->select(['product_id', 'model'])
        // ->first();
        // $this->assertInstanceOf(Product::class, $whereClause);
        // $this->assertEquals($whereClause->product_id, 61);
        // $this->assertEquals($whereClause->model, 'SM-G998B');
        // //Query with where in clause

        // $whereInClause = $this->repository->getModel()->whereIn('model', ['SM-G998B', 'iPhone-14-Pro'])->findAll();
        // $this->assertCount(2, $whereInClause);
        // $this->assertEquals($whereInClause[0]['model'], 'SM-G998B');
        // $this->assertEquals($whereInClause[1]['model'], 'iPhone-14-Pro');

        // //Query with where not in clause

        // $whereNotInClause = $this->repository->getModel()->whereNotIn('model', ['SM-G998B', 'iPhone-14-Pro'])->whereIn('product_id', [61, 66])->findAll();
        // $this->assertIsArray($whereNotInClause);

        // //Query with where like clause

        // $whereLikeClause = $this->repository->getModel()->whereLike('sku', '%APPLE-%')->findAll();
        // $this->assertIsArray($whereLikeClause);
        
        
        // //Query using with method using hasOne relation

        // $withClause = $this->repository->getModel()->where('product_id', '=', 61)->with(['content'])->first();
        // $this->assertInstanceOf(Product::class, $withClause);

        // //Query using with method with hasMany relation

        // $withClause = $this->repository->getModel()->where('product_id', '=', 61)->with(['content', 'attributes'])->first();
        // $this->assertInstanceOf(Product::class, $withClause);
        // //Query using with method with belongsTo relation

        // $withClause = $this->repository->getModel()->where('product_id', '=', 61)->with(['content', 'attributes', 'vendor'])->first();
        // $this->assertInstanceOf(Product::class, $withClause);


        // //Query using with method with belongsToMany relation

        // $withClause = $this->repository->getModel()
        // ->where('product_id', '=', 61)
        // ->with(['content', 'attributes', 'vendor', 'relatedProducts'])->first();
        // $this->assertInstanceOf(Product::class, $withClause);

        // $product = $this->repository->getModel()->where('product_id', '=', 61)->select(['product_id', 'model'])->first();
        // $this->assertInstanceOf(Product::class, $product);

        // //Query implementing with method and using hasOne relation with callback function selecting particular columns 

        // $withClause = $this->repository->getModel()->where('product_id', '=', 61)
        // ->with(['content' => function($query){
        //     return $query->select(['product_id', 'name']);
        // }])->first();
        // $this->assertInstanceOf(Product::class, $withClause);

        $withClause = $this->repository->getModel()->where('product_id', '=', 61)
        ->with([
            'content' => function($query){
                return $query->select(['product_id', 'name']);
            },
            'attributes' => function($query){
                return $query->select(['product_id', 'value']);
            },
            'vendor' => function($query){
                return $query->select(['vendor_id', 'name']);
            },
            'relatedProducts' => function($query){
                return $query
                ->with(['content as relatedProductsContent' => function($q){
                    return $q->select(['product_id', 'name']);
                }])
                ->select(['product_id', 'model'])
                ->where('product_id', '=', 64);
            }
        ])
        ->select(['product_id', 'model'])
        ->first();

        $this->assertInstanceOf(Product::class, $withClause);
        

        // //Query using join method

        // $joinClause = $this->repository->getModel()->where('product_id', '=', 1)->join('product_content', 'product_content.product_id', '=', 'product.product_id')->getQuery();

        //Use join method with clause

        
       
    }

    protected function tearDown(): void
    {
        $this->kernel->reset();
        parent::tearDown();
    }
} 