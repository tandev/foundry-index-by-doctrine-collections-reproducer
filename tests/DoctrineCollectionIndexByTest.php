<?php

namespace App\Tests;

use App\Factory\CategoryFactory;
use App\Factory\PostFactory;
use App\Factory\SpaceFactory;
use App\Factory\TranslationFactory;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class DoctrineCollectionIndexByTest extends KernelTestCase
{
    use Factories;

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadata);
    }

    public function testRelationship(): void
    {
        $category = CategoryFactory::new()->create(['translations' => TranslationFactory::new()->many(1)]);
        self::assertCount(1, $category->getTranslations());
    }

    public function testIndexedByWithAOneToOneAndAOneToManyLayer(): void
    {
        $post = PostFactory::new()->with(['category' => CategoryFactory::new()->with(['translations' => TranslationFactory::new(['culture' => 'en'])->many(1)])])->create();
        self::assertNotNull($post->getCategory()->getTranslation('en'));
    }

    public function testIndexedByWithTwoOneToManyLayers(): void
    {
        $space = SpaceFactory::new()->with(
            [
                'categories' => CategoryFactory::new()->with(
                    [
                        'translations' => TranslationFactory::new(['culture' => 'en'])->many(1),
                    ])->many(1),
            ]
        )->create()
        ;
        self::assertNotNull($space->getCategories()[0]->getTranslation('en'));
    }
}
