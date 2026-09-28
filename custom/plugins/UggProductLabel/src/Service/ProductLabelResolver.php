<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Service;

use Ugg\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use Ugg\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;

/**
 * ASSUMPTION: this is a suggested extraction, not something pulled from your
 * existing code. If your PRODUCT_LOADED_EVENT subscriber already contains this
 * filter/sort logic inline, either:
 *   a) move it into a class like this one so it's unit-testable without
 *      booting the kernel (recommended), or
 *   b) adjust the namespace/class name below and in
 *      tests/Unit/Service/ProductLabelResolverTest.php to match wherever
 *      the logic actually lives.
 *
 * Contract the tests are written against:
 *   resolve(ProductLabelCollection $assignedLabels, \DateTimeInterface $now): ProductLabelCollection
 *
 * Takes the labels already assigned to a product (via the `labels` ManyToMany
 * association) and returns only those that are active and within their
 * validFrom/validTo window, sorted by priority descending.
 */
class ProductLabelResolver
{
    public function resolve(ProductLabelCollection $assignedLabels, \DateTimeInterface $now): ProductLabelCollection
    {
        $valid = $assignedLabels->filter(function (ProductLabelEntity $label) use ($now): bool {
            if ($label->isActive() !== true) {
                return false;
            }

            if ($label->getValidFrom() !== null && $label->getValidFrom() > $now) {
                return false;
            }

            if ($label->getValidTo() !== null && $label->getValidTo() < $now) {
                return false;
            }

            return true;
        });

        $valid->sort(static function (ProductLabelEntity $a, ProductLabelEntity $b): int {
            return $b->getPriority() <=> $a->getPriority();
        });

        return $valid;
    }
}
