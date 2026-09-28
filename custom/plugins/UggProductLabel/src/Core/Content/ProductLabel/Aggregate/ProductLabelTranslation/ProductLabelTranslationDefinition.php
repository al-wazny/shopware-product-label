<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;
use Ugg\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;

class ProductLabelTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'product_label_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getParentDefinitionClass(): string
    {
        return ProductLabelDefinition::class;
    }

    public function getEntityClass(): string
    {
        return ProductLabelTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductLabelTranslationCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))
                ->addFlags(new Required(), new ApiAware()),

            (new FkField('product_label_id', 'productLabelId', ProductLabelDefinition::class))
                ->addFlags(new Required(), new PrimaryKey()),

            (new FkField('language_id', 'languageId', LanguageDefinition::class))
                ->addFlags(new Required(), new PrimaryKey()),

            new ManyToOneAssociationField(
                'productLabel',
                'product_label_id',
                ProductLabelDefinition::class,
                'id',
                false
            ),

            new ManyToOneAssociationField(
                'language',
                'language_id',
                LanguageDefinition::class,
                'id',
                false
            ),
        ]);
    }
}

