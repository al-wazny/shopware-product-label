<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Core\Content\ProductLabel;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;

/**
 * Pure mapping table for the product_label <-> product ManyToMany.
 * MappingEntityDefinition is Shopware's base class for tables that only
 * exist to join two entities — no own EntityEntity/Collection needed.
 */
class ProductLabelProductDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'product_label_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('product_label_id', 'productLabelId', ProductLabelDefinition::class))
                ->addFlags(new Required(), new PrimaryKey()),
 
            (new FkField('product_id', 'productId', ProductDefinition::class))
                ->addFlags(new Required(), new PrimaryKey()),
 
            (new ReferenceVersionField(ProductDefinition::class))
                ->addFlags(new Required(), new PrimaryKey()),
 
            new ManyToOneAssociationField('productLabel', 'product_label_id', ProductLabelDefinition::class, 'id', false),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),
            new CreatedAtField(),
        ]);
    }
}
