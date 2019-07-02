<?php

namespace App\Tests\Form;

use App\Entity\Country;
use App\Form\CountryType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class CityTypeTest
 * @package App\Tests\Form
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CityTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'France',
        ];

        $objectToCompare = new Country();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CountryType::class, $objectToCompare);

        $object = new Country();
        $object->setName('France');
        // ...populate $object properties with the data stored in $formData

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}