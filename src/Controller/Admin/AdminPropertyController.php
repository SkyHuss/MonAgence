<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{

    private $repository;
    private $em;

    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }


    /**
     * @Route("/admin", name="admin.property.index")
     * @return Response
     */
    public function index(): Response
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }


    /**
     * @Route("/admin/property/create", name="admin.property.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($property); //puisque que l'entité viens d'etre créer elle n'est pas connue de l'em, il faut donc la faire persister
            $this->em->flush();
            $this->addFlash('success', 'Création effectuée avec succès');

            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return Response
     */
    public function edit(Property $property, Request $request): Response
    {

        /*$option = new Option();
        $property->addOption($option);*/


        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);//va verifier les modifications

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Modifications enregistrées avec succès !');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Property $property
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
     * @return Response
     */
    public function delete(Property $property, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(),$request->get('_token')))
        {
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Suppression effectuée avec succès');
        }
        return $this->redirectToRoute('admin.property.index');

    }
}