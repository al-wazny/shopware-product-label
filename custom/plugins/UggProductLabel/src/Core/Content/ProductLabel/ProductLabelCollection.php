<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Core\Content\ProductLabel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ProductLabelEntity $entity)
 * @method void set(string $key, ProductLabelEntity $entity)
 * @method ProductLabelEntity[] getIterator()
 * @method ProductLabelEntity[] getElements()
 * @method ProductLabelEntity|null get(string $key)
 * @method ProductLabelEntity|null first()
 * @method ProductLabelEntity|null last()
 */
class ProductLabelCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductLabelEntity::class;
    }
}
