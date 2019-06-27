<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/city")
 */
class CityController extends AbstractController
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var CountryRepository
     */
    private $countryRepository;


    /**
     * Constructor.
     *
     * @param CityRepository    $cityRepository
     * @param CountryRepository $countryRepository
     */
    public function __construct(CityRepository $cityRepository, CountryRepository $countryRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Route("/", name="city_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $this->cityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/choices", name="city_choices", methods={"GET"})
     */
    public function choices(Request $request)
    {
        $criteria = [];
        if ($countryId = $request->query->get('countryId')) {
            $criteria['country'] = $this->countryRepository->find($countryId);
        }

        $cities = $this->cityRepository->findBy($criteria, ['name' => 'ASC']);

        $normalized = [];
        foreach ($cities as $city) {
            $normalized[] = [
                'id' => $city->getId(),
                'name' => $city->getName(),
            ];
        }

        return $this->json($normalized);
    }

    /**
     * @Route("/new", name="city_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index');
        }

        return $this->render('city/new.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_show", methods={"GET"})
     */
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('city_index', [
                'id' => $city->getId(),
            ]);
        }

        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_delete", methods={"DELETE"})
     */
    public function delete(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_index');
    }
}
