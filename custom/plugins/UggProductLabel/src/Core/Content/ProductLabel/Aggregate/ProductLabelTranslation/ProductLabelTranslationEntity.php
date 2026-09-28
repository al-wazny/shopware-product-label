<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\System\Language\LanguageEntity;
use Ugg\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;

class ProductLabelTranslationEntity extends Entity
{
    protected string $productLabelId;

    protected string $name;

    protected string $languageId;

    protected ?LanguageEntity $language = null;
    
    protected ?ProductLabelEntity $productLabel = null;

    public function getProductLabelId(): string
    {
        return $this->productLabelId;
    }

    public function setProductLabelId(string $productLabelId): void
    {
        $this->productLabelId = $productLabelId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }

    public function getProductLabel(): ?ProductLabelEntity
    {
        return $this->productLabel;
    }

    public function setProductLabel(?ProductLabelEntity $productLabel): void
    {
        $this->productLabel = $productLabel;
    }
}
