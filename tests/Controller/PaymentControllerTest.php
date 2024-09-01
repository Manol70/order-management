<?php

namespace App\Test\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PaymentRepository $repository;
    private string $path = '/payment/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Payment::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Payment index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'payment[number_order]' => 'Testing',
            'payment[paid]' => 'Testing',
            'payment[document]' => 'Testing',
            'payment[number_doc]' => 'Testing',
            'payment[createdAt]' => 'Testing',
            'payment[updatedAt]' => 'Testing',
            'payment[customer]' => 'Testing',
            'payment[user]' => 'Testing',
            'payment[type_montage]' => 'Testing',
            'payment[_order]' => 'Testing',
        ]);

        self::assertResponseRedirects('/payment/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setNumber_order('My Title');
        $fixture->setPaid('My Title');
        $fixture->setDocument('My Title');
        $fixture->setNumber_doc('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCustomer('My Title');
        $fixture->setUser('My Title');
        $fixture->setType_montage('My Title');
        $fixture->set_order('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Payment');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setNumber_order('My Title');
        $fixture->setPaid('My Title');
        $fixture->setDocument('My Title');
        $fixture->setNumber_doc('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCustomer('My Title');
        $fixture->setUser('My Title');
        $fixture->setType_montage('My Title');
        $fixture->set_order('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'payment[number_order]' => 'Something New',
            'payment[paid]' => 'Something New',
            'payment[document]' => 'Something New',
            'payment[number_doc]' => 'Something New',
            'payment[createdAt]' => 'Something New',
            'payment[updatedAt]' => 'Something New',
            'payment[customer]' => 'Something New',
            'payment[user]' => 'Something New',
            'payment[type_montage]' => 'Something New',
            'payment[_order]' => 'Something New',
        ]);

        self::assertResponseRedirects('/payment/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNumber_order());
        self::assertSame('Something New', $fixture[0]->getPaid());
        self::assertSame('Something New', $fixture[0]->getDocument());
        self::assertSame('Something New', $fixture[0]->getNumber_doc());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getCustomer());
        self::assertSame('Something New', $fixture[0]->getUser());
        self::assertSame('Something New', $fixture[0]->getType_montage());
        self::assertSame('Something New', $fixture[0]->get_order());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Payment();
        $fixture->setNumber_order('My Title');
        $fixture->setPaid('My Title');
        $fixture->setDocument('My Title');
        $fixture->setNumber_doc('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCustomer('My Title');
        $fixture->setUser('My Title');
        $fixture->setType_montage('My Title');
        $fixture->set_order('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/payment/');
    }
}
