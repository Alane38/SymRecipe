<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\Mapping\Entity;

class IngredientController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Displaying all ingredients
     *
     * @param IngredientRepository $IngredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(
        Request $request,
        IngredientRepository $IngredientRepository,
        PaginatorInterface $paginator,
    ): Response {

        // dd($ingredient);
        $ingredients = $paginator->paginate(
            $IngredientRepository->findAll(), /*query*/
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * Create new ingredient with form
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient/create', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());

            $ingredient_name = $form->get('name')->getData();

            $this->manager->persist($ingredient);
            $this->manager->flush();

            $this->addFlash(
                'success',
                'L\'ingrédient ' . $ingredient_name . ' à bien été crée avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/edit/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    #[Entity('Ingredient', expr: 'repository.find(id)')]
    public function edit(
        Request $request,
        Ingredient $ingredient,
    ): Response {
        dd($ingredient);
        
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());

            $ingredient = $form->getData();
            $ingredient_name = $form->get('name')->getData();

            $this->manager->persist($ingredient);
            $this->manager->flush();

            $this->addFlash(
                'success',
                'L\'ingrédient ' . $ingredient_name . ' à bien été modifié avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
