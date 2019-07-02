<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CountryControllerTest
 * @package App\Tests\Controller
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CountryControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/country/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @noinspection CssInvalidPseudoSelector */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Country index")')->count()
        );
    }

    public function testNew()
    {
        $client = static::createClient();

        $client->getKernel()->getContainer();

        $crawler = $client->request('GET', '/country/new');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form['country[name]'] = 'Test';

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        /** @noinspection CssInvalidPseudoSelector */
        $this->assertGreaterThan(
            0,
            $crawler->filter('html td:contains("Test")')->count()
        );
    }
}
