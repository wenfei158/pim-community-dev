<?php

declare(strict_types=1);

namespace Pim\Bundle\CatalogBundle\EventSubscriber;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Query;
use Pim\Component\Catalog\EntityWithFamily\Event\ParentWasAddedToProduct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * When a product is converted to a variant product we need to update the database and change the object in the
 * doctrine unit of work.
 *
 * @author    Arnaud Langlade <arnaud.langlade@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AddParentAProductSubscriber implements EventSubscriberInterface
{
    /** @var Query\ConvertProductToVariantProduct */
    private $convertProductToVariantProduct;

    /**
     * AddParentAProductSubscriber constructor.
     *
     * @param Query\ConvertProductToVariantProduct $convertProductToVariantProduct
     */
    public function __construct(Query\ConvertProductToVariantProduct $convertProductToVariantProduct)
    {
        $this->convertProductToVariantProduct = $convertProductToVariantProduct;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ParentWasAddedToProduct::EVENT_NAME => 'convertProductToVariantProduct'
        ];
    }

    /**
     * @param ParentWasAddedToProduct $event
     */
    public function convertProductToVariantProduct(ParentWasAddedToProduct $event): void
    {
        ($this->convertProductToVariantProduct)($event->convertedProduct());
    }
}
