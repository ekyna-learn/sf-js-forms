<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Event;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * Constructor.
     *
     * @param CountryRepository $countryRepository
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var Event $data */
            $data = $event->getData();

            $country = null;
            if ($city = $data->getCity()) {
                $country = $city->getCountry();
            }

            $this->addCountryForm($form, $country);
            $this->addCityForm($form, $country);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var array $data */
            $data = $event->getData();

            $country = $this->countryRepository->find($data['country']);

            $this->addCountryForm($form, $country);
            $this->addCityForm($form, $country);
        });
    }

    private function addCountryForm(FormInterface $form, Country $country = null)
    {
        $form->add('country', EntityType::class, [
            'class'  => Country::class,
            'mapped' => false,
            'data'   => $country,
        ]);
    }

    private function addCityForm(FormInterface $form, Country $country = null)
    {
        $form
            ->add('city', EntityType::class, [
                'class'         => City::class,
                'query_builder' => function (EntityRepository $repository) use ($country) {
                    $qb = $repository->createQueryBuilder('c');
                    $qb->addOrderBy('c.name', 'ASC');

                    if ($country) {
                        $qb
                            ->andWhere($qb->expr()->eq('c.country', ':country'))
                            ->setParameter('country', $country);
                    }

                    return $qb;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
