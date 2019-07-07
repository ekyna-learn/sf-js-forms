<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CollectionType
 * @package App\Form
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CollectionType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'allow_add'             => $options['allow_add'],
            'allow_delete'          => $options['allow_delete'],
            'allow_sort'            => $options['allow_sort'],
            'add_button_text'       => $options['add_button_text'],
            'add_button_class'      => $options['add_button_class'],
            'delete_button_text'    => $options['delete_button_text'],
            'delete_button_confirm' => $options['delete_button_confirm'],
            'delete_button_class'   => $options['delete_button_class'],
            'sub_widget_col'        => $options['sub_widget_col'],
            'button_col'            => $options['button_col'],
            'prototype_name'        => $options['prototype_name'],
        ]);

        if (false === $view->vars['allow_delete']) {
            $view->vars['sub_widget_col'] += $view->vars['button_col'];
        }

        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }
    }


    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $allowNormalizer = function (Options $options, $value) {
            if ($options['disabled']) {
                return false;
            }

            return $value;
        };

        $resolver
            ->setDefaults([
                'by_reference'          => false,
                'allow_add'             => false,
                'allow_delete'          => false,
                'allow_sort'            => false,
                'prototype'             => true,
                'prototype_name'        => '__name__',
                'add_button_text'       => 'Add',
                'add_button_class'      => 'btn btn-primary btn-sm',
                'delete_button_text'    => 'Delete',
                'delete_button_confirm' => 'Confirm deletion ?',
                'delete_button_class'   => 'btn btn-danger btn-sm',
                'sub_widget_col'        => 10,
                'button_col'            => 2,
            ])
            ->setNormalizer('allow_add', $allowNormalizer)
            ->setNormalizer('allow_delete', $allowNormalizer)
            ->setNormalizer('allow_sort', $allowNormalizer);;
    }

    public function getParent()
    {
        return BaseType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'app_collection';
    }
}
