<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Tests;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class ProductLabelRepositoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $productLabelRepository;

    private Context $context;

    protected function setUp(): void
    {
        $this->productLabelRepository = $this->getContainer()
            ->get('product_label.repository');

        $this->context = Context::createDefaultContext();
    }

    public function testProductLabelCanBeWrittenAndRead(): void
    {
        $id = Uuid::randomHex();

        $this->productLabelRepository->create([
            [
                'id' => $id,
                'color' => '#ff0000',
                'priority' => 10,
                'active' => true,
                'translations' => [
                    Defaults::LANGUAGE_SYSTEM => [
                        'name' => 'Sale',
                    ],
                ],
            ],
        ], $this->context);

        $criteria = new Criteria([$id]);

        $label = $this->productLabelRepository
            ->search($criteria, $this->context)
            ->get($id);

        self::assertNotNull($label);

        self::assertSame($id, $label->getId());
        self::assertSame('Sale', $label->getName());
        self::assertSame('#ff0000', $label->getColor());
        self::assertSame(10, $label->getPriority());
        self::assertTrue($label->isActive());
    }

    public function testProductLabelCanBeUpdatedAndRead(): void
    {
        $id = Uuid::randomHex();

        $this->productLabelRepository->create([
            [
                'id' => $id,
                'color' => '#ff0000',
                'priority' => 10,
                'active' => true,
                'translations' => [
                    Defaults::LANGUAGE_SYSTEM => [
                        'name' => 'Sale',
                    ],
                ],
            ],
        ], $this->context);

        $this->productLabelRepository->update([
            [
                'id' => $id,
                'color' => '#00ff00',
                'priority' => 20,
                'active' => false,
                'translations' => [
                    Defaults::LANGUAGE_SYSTEM => [
                        'name' => 'Updated Sale',
                    ],
                ],
            ],
        ], $this->context);

        $label = $this->productLabelRepository
            ->search(new Criteria([$id]), $this->context)
            ->get($id);

        self::assertNotNull($label);

        self::assertSame($id, $label->getId());
        self::assertSame('Updated Sale', $label->getName());
        self::assertSame('#00ff00', $label->getColor());
        self::assertSame(20, $label->getPriority());
        self::assertFalse($label->isActive());
    }
}

