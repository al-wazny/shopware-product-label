<?php declare(strict_types=1);

namespace Ugg\ProductLabel\Tests;

use PHPUnit\Framework\TestCase;
use Ugg\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use Ugg\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use Ugg\ProductLabel\Service\ProductLabelResolver;

/**
 * Covers the "Storefront Subscriber (unit tests)" checklist. These tests target
 * ProductLabelResolver directly (pure PHP, no kernel/DB) rather than the event
 * subscriber itself, since the subscriber's own job is just wiring
 * (read product -> call resolver -> addExtension) and isn't worth testing in
 * isolation from a DB-backed integration test. If you'd rather test the
 * subscriber's dispatch handling directly, mock EntityLoadedEvent /
 * ProductEntity and assert on $product->getExtension(...) after calling
 * onProductLoaded() — the resolver contract below stays the same either way.
 */
class ProductLabelResolverTest extends TestCase
{
    private ProductLabelResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ProductLabelResolver();
    }

    private function makeLabel(
        string $id,
        string $name,
        int $priority = 0,
        bool $active = true,
        ?\DateTimeInterface $validFrom = null,
        ?\DateTimeInterface $validTo = null
    ): ProductLabelEntity {
        $label = new ProductLabelEntity();
        $label->setId($id);
        $label->setName($name);
        $label->setPriority($priority);
        $label->setActive($active);
        $label->setValidFrom($validFrom);
        $label->setValidTo($validTo);

        return $label;
    }

    // 1. load/display valid labels only -> within time window and isActive
    public function testOnlyActiveAndDateValidLabelsAreReturned(): void
    {
        $now = new \DateTimeImmutable('2026-06-15 12:00:00');

        $collection = new ProductLabelCollection([
            $this->makeLabel('1', 'Active and valid'),
            $this->makeLabel('2', 'Inactive', 0, false),
            $this->makeLabel('3', 'Expired', 0, true, null, new \DateTimeImmutable('2026-01-01')),
            $this->makeLabel('4', 'Not started yet', 0, true, new \DateTimeImmutable('2027-01-01')),
        ]);

        $result = $this->resolver->resolve($collection, $now);

        self::assertCount(1, $result);
        self::assertTrue($result->has('1'));
    }

    // 2. removing assigned labels removes them from storefront
    public function testRemovingAssignedLabelExcludesItFromResult(): void
    {
        $now = new \DateTimeImmutable();

        $withLabel = new ProductLabelCollection([$this->makeLabel('1', 'Sale')]);
        $withoutLabel = new ProductLabelCollection([]);

        self::assertCount(1, $this->resolver->resolve($withLabel, $now));
        self::assertCount(0, $this->resolver->resolve($withoutLabel, $now));
    }

    // 3. validFrom and validTo both null -> always valid regardless of current date
    public function testNullValidFromAndValidToIsAlwaysValid(): void
    {
        $collection = new ProductLabelCollection([
            $this->makeLabel('1', 'Evergreen', 0, true, null, null),
        ]);

        self::assertCount(1, $this->resolver->resolve($collection, new \DateTimeImmutable('2000-01-01')));
        self::assertCount(1, $this->resolver->resolve($collection, new \DateTimeImmutable('2100-01-01')));
    }

    // 4. only validFrom set -> valid with no expiration after starting date
    public function testOnlyValidFromSetIsValidAfterStartWithNoExpiry(): void
    {
        $validFrom = new \DateTimeImmutable('2026-01-01 00:00:00');
        $collection = new ProductLabelCollection([
            $this->makeLabel('1', 'Starts Jan', 0, true, $validFrom, null),
        ]);

        self::assertCount(0, $this->resolver->resolve($collection, new \DateTimeImmutable('2025-12-31 23:59:59')));
        self::assertCount(1, $this->resolver->resolve($collection, $validFrom));
        self::assertCount(1, $this->resolver->resolve($collection, new \DateTimeImmutable('2099-01-01')));
    }

    // 5. only validTo set -> valid from beginning of time until expiration date
    public function testOnlyValidToSetIsValidFromBeginningUntilExpiry(): void
    {
        $validTo = new \DateTimeImmutable('2026-01-01 00:00:00');
        $collection = new ProductLabelCollection([
            $this->makeLabel('1', 'Ends Jan', 0, true, null, $validTo),
        ]);

        self::assertCount(1, $this->resolver->resolve($collection, new \DateTimeImmutable('1990-01-01')));
        self::assertCount(1, $this->resolver->resolve($collection, $validTo));
        self::assertCount(0, $this->resolver->resolve($collection, new \DateTimeImmutable('2026-01-01 00:00:01')));
    }

    // 6. product with no assigned label -> return empty array/result
    public function testProductWithNoAssignedLabelsReturnsEmptyResult(): void
    {
        $result = $this->resolver->resolve(new ProductLabelCollection([]), new \DateTimeImmutable());

        self::assertCount(0, $result);
    }

    // Bonus coverage: explicit priority ordering (mentioned in the requirements
    // as "labels ordered by priority" — worth locking down since a regression
    // here wouldn't otherwise fail any listed checklist item)
    public function testLabelsAreSortedByPriorityDescending(): void
    {
        $now = new \DateTimeImmutable();
        $collection = new ProductLabelCollection([
            $this->makeLabel('low', 'Low', 1),
            $this->makeLabel('high', 'High', 10),
            $this->makeLabel('mid', 'Mid', 5),
        ]);

        $result = $this->resolver->resolve($collection, $now);

        self::assertSame(['high', 'mid', 'low'], array_values($result->getIds()));
    }

    // 7. product_label(name) with missing translation -> fallback to default-language
    public function testResolverPassesThroughAlreadyResolvedFallbackNameUnchanged(): void
    {
        // IMPORTANT: actual translation fallback happens at the DAL level via
        // Context::getLanguageIdChain() when product_label.repository loads
        // the entities — NOT inside this resolver. By the time a
        // ProductLabelEntity reaches resolve(), its `name` is already whatever
        // the DAL resolved (specific translation, or the default-language
        // fallback if none existed for the current language).
        //
        // This test only protects the resolver's contract: it must not
        // re-derive, blank out, or otherwise mutate an already-resolved name.
        // The authoritative test for the fallback behavior itself lives in
        // the integration suite:
        // ProductLabelRepositoryTest::testNameFallsBackToDefaultLanguageWhenTranslationMissing()
        $label = $this->makeLabel('1', 'Sale'); // as it would arrive after DAL fallback resolution
        $collection = new ProductLabelCollection([$label]);

        $result = $this->resolver->resolve($collection, new \DateTimeImmutable());

        self::assertSame('Sale', $result->first()->getName());
    }
}
