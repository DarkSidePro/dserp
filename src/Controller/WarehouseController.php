<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentOperation;
use App\Entity\Product;
use App\Entity\ProductOperation;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarehouseController extends AbstractController
{
    /**
     * @Route("/panel/warehouse", name="warehouse")
     */
    public function index(DataTableFactory $dataTableFactory, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $product_table = $dataTableFactory->create()
            ->setName('product_table')
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true])
            ->add('state', NumberColumn::class, ['label' => 'State', 'className' => 'bold', 'searchable' => true])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Product::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('p.id')
                        ->addSelect('p.product_name')
                        ->addSelect('po.state')
                        ->from(Product::class, 'p')
                        ->leftJoin(ProductOperation::class, 'po', Join::WITH, 'po.product = p.id AND NOT EXISTS (SELECT 1 FROM App\Entity\ProductOperation p1 WHERE p1.product = p.id AND p1.id > po.id)')
                        ->groupBy('p.id');
                }
            ]);
        $product_table->handleRequest($request);
        
        if ($product_table->isCallback()) {
            return $product_table->getResponse();
        } 

        $component_table = $dataTableFactory->create()
            ->setName('component_table')
            ->add('id', NumberColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('component_name', TextColumn::class, ['label' => 'Component name', 'className' => 'bold', 'searchable' => true])
            ->add('state', NumberColumn::class, ['label' => 'State', 'className' => 'bold', 'searchable' => true])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Component::class,
                'hydrate' => Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) {
                    $builder->select('c.id');
                    $builder->addSelect('c.component_name');
                    $builder->addSelect('co.state');
                    $builder->from(Component::class, 'c');
                    $builder->leftJoin(ComponentOperation::class, 'co', Join::WITH, 'co.component = c.id AND NOT EXISTS (SELECT 1 FROM App\Entity\ComponentOperation p1 WHERE p1.component = c.id AND p1.id > co.id)'); 
                    $builder->groupBy('c.id');
                        
                }
            ]);
        $component_table->handleRequest($request);
        
        if ($component_table->isCallback()) {
            return $component_table->getResponse();
        } 

        return $this->render('warehouse/index.html.twig', [
            'controller_name' => 'WarehouseController',
            'product_table' => $product_table,
            'component_table' => $component_table
        ]);
    }
}
