<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\Product;
use App\Entity\Recipe;
use App\Entity\RecipeDetail;
use App\Form\RecipeDetailType;
use App\Form\RecipeType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
    * Holds a private string
    * @var string
    */
    private $tmpId;
    
    /**
     * @Route("/panel/recipe", name="recipe")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => '#', 'className' => 'bold', 'searchable' => true])
            ->add('recipe_name', TextColumn::class, ['label' => 'Recipe name', 'className' => 'bold', 'searchable' => true])
            ->add('product_name', TextColumn::class, ['label' => 'Product name', 'className' => 'bold', 'searchable' => true, 'field' => 'product.product_name', 'orderField' => 'product.product_name'])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => false, 'template' => 'recipe/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Recipe::class
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
        }

        return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
            'datatable' => $table,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/panel/recipe/details/{id}", name="recipe_details")
     */
    public function recipeDetails(
            Request $request, 
            DataTableFactory $dataTableFactory, $id, 
            EntityManagerInterface $em,
            Recipe $recipe
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $this->tmpId = $id;

        $table = $dataTableFactory->create([])
            ->add('amount', TextColumn::class, ['label' => 'Amount', 'className' => 'bold', 'searchable' => true])
            ->add('component', TextColumn::class, ['label' => 'Component name', 'className' => 'bold', 'searchable' => true, 'field' => 'c.component_name'])
            ->add('actions', TwigColumn::class, ['label' => 'Actions', 'className' => 'bold', 'searchable' => true, 'template' => 'recipe/details/_partials/table/actions.html.twig'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => RecipeDetail::class,
                'hydrate' => \Doctrine\ORM\Query::HYDRATE_ARRAY,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('rd')
                        ->from(RecipeDetail::class, 'rd');
                        $builder->addSelect('r');
                        $builder->addSelect('c');
                        $builder->addSelect('p');
                        $builder->leftJoin('rd.recipe', 'r');
                        $builder->leftJoin('rd.component', 'c');
                        $builder->leftJoin('r.product', 'p');
                },
                'criteria' => [
                    function (QueryBuilder $builder) {
                        $builder->andWhere($builder->expr()->eq('rd.recipe', ':test'))->setParameter('test', $this->tmpId);
                    },
                    new SearchCriteriaProvider(),
                ]
            ]);
        $table->handleRequest($request);
        
        if ($table->isCallback()) {
            return $table->getResponse();
        } 

        $recipeDetail = new RecipeDetail;
        $form = $this->createForm(RecipeDetailType::class, $recipeDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeDetail->setRecipe($this->getDoctrine()->getRepository(Recipe::class)->findOneBy(['id' => $id]));
            $em->persist($recipeDetail);
            $em->flush();

            return $this->redirectToRoute('recipe');
        }

        return $this->render('recipe/details/index.html.twig', [
            'controller_name' => 'RecipeController',
            'datatable' => $table,
            'form' => $form->createView(),
            'recipe_name' => $recipe->getRecipeName(),
            'product_name' => $recipe->getProduct()->getProductName()
        ]);
    }

    /**
     * @Route("/panel/recipe/update/{id}", name="recipe_update")
     */
    public function updateRecipe(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('recipe');
        }

        return $this->render('recipe/update.html.twig', [
            'controller_name' => 'RecipeController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/recipe/delete/{id}", name="recipe_remove")
     */
    public function deleteRecipe(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($recipe);
        $em->flush();

        return $this->redirectToRoute('recipe');
    }

    /**
     * @Route("/admin/recipe/detail/delete/{id}", name="recipe_detail_remove")
     */
    public function deleteRecipeDetail(RecipeDetail $recipeDetail, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $id = $recipeDetail->getRecipe()->getId();
        $em->remove($recipeDetail);
        $em->flush();

        return $this->redirectToRoute('recipe_details', ['id' => $id]);
    }

    /**
     * @Route("/panel/recipe/detail/{id}", name="recipe_detail_update")
     */
    public function updateRecipeDetail(RecipeDetail $recipeDetail, EntityManagerInterface $em, Request $request): Response
    {
        $id = $recipeDetail->getRecipe()->getId();
        $form = $this->createForm(RecipeDetailType::class, $recipeDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeDetail->setRecipe($this->getDoctrine()->getRepository(Recipe::class)->findOneBy(['id' => $id]));
            $em->persist($recipeDetail);
            $em->flush();

            return $this->redirectToRoute('recipe_details', ['id' => $id]);
        }

        return $this->render('recipe/details/update.html.twig', [
            'controller_name' => 'RecipeController',
            'form' => $form->createView()
        ]);
    }
}
