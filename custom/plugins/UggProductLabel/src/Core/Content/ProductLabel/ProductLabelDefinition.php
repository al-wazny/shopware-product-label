<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Core\Content\ProductLabel;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Ugg\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation\ProductLabelTranslationDefinition;
 
class ProductLabelDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'product_label';
 
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }
 
    public function getEntityClass(): string
    {
        return ProductLabelEntity::class;
    }
 
    public function getCollectionClass(): string
    {
        return ProductLabelCollection::class;
    }
 
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))
                ->addFlags(new Required(), new PrimaryKey()),
 
            // Required, translated field — no physical column on this table,
            // resolved via product_label_translation at read time.
            (new TranslatedField('name'))
                ->addFlags(new Required(), new ApiAware()),

            (new StringField('color', 'color'))
                ->addFlags(new Required(), new ApiAware()),

            (new IntField('priority', 'priority'))
                ->addFlags(new ApiAware()),

            (new BoolField('active', 'active'))
                ->addFlags(new ApiAware()),

            (new DateTimeField('valid_from', 'validFrom'))
                ->addFlags(new ApiAware()),

            (new DateTimeField('valid_to', 'validTo'))
                ->addFlags(new ApiAware()),

            (new TranslationsAssociationField(
                ProductLabelTranslationDefinition::class,
                'product_label_id'
            ))->addFlags(
                new Required(),
                new ApiAware()
            ),

            (new ManyToManyAssociationField(
                'products',
                ProductDefinition::class,
                ProductLabelProductDefinition::class,
                'product_label_id',
                'product_id'
            ))->addFlags(new ApiAware()),
        ]);
    }
}

