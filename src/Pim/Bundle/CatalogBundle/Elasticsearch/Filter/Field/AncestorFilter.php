<?php

declare(strict_types=1);

namespace Pim\Bundle\CatalogBundle\Elasticsearch\Filter\Field;

use Akeneo\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Pim\Component\Catalog\Exception\InvalidOperatorException;
use Pim\Component\Catalog\Exception\ObjectNotFoundException;
use Pim\Component\Catalog\Query\Filter\FieldFilterHelper;
use Pim\Component\Catalog\Repository\ProductModelRepositoryInterface;

/**
 * An ancestor is a product model that is either a parent or a grand parent.
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AncestorFilter extends AbstractFieldFilter
{
    private const ANCESTOR_ID_ES_FIELD = 'ancestors.ids';
    private const PQB_FIELD = 'ancestor.id';

    /** @var IdentifiableObjectRepositoryInterface */
    private $productModelRepository;

    /**
     * @param ProductModelRepositoryInterface $productModelRepository
     * @param array                           $supportedOperators
     */
    public function __construct(
        ProductModelRepositoryInterface $productModelRepository,
        array $supportedOperators
    ) {
        $this->productModelRepository = $productModelRepository;
        $this->supportedOperators = $supportedOperators;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsOperator($operator): bool
    {
        return in_array($operator, $this->supportedOperators);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsField($field): bool
    {
        return $field === self::PQB_FIELD;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldFilter($field, $operator, $values, $locale = null, $channel = null, $options = []): void
    {
        if (null === $this->searchQueryBuilder) {
            throw new \LogicException('The search query builder is not initialized in the filter.');
        }

        if (!$this->supportsOperator($operator)) {
            throw InvalidOperatorException::notSupported($operator, AncestorFilter::class);
        }

        $this->checkValues($values);

        $this->searchQueryBuilder->addShould(
            [
                [
                    'terms' => [
                        self::ANCESTOR_ID_ES_FIELD => $values,
                    ],
                ],
                [
                    'terms' => [
                        'id' => $values,
                    ],
                ],
            ]
        );
    }

    /**
     * Checks the value we want to filter on is valid
     *
     * @param $values
     *
     * @throws ObjectNotFoundException
     */
    private function checkValues($values): void
    {
        FieldFilterHelper::checkArray(self::ANCESTOR_ID_ES_FIELD, $values, static::class);
        foreach ($values as $value) {
            FieldFilterHelper::checkString(self::ANCESTOR_ID_ES_FIELD, $value, static::class);
            if (!$this->isValidId($value)) {
                throw new ObjectNotFoundException(
                    sprintf('Object "product model" with ID "%s" does not exist', $value)
                );
            }
        }
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function isValidId(string $value): bool
    {
        $id = str_replace('product_model_', '', $value);

        return null !== $this->productModelRepository->findOneBy(['id' => $id]);
    }
}
