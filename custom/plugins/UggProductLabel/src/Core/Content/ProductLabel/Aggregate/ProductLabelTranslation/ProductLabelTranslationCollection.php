<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ProductLabelTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductLabelTranslationEntity::class;
    }
}
