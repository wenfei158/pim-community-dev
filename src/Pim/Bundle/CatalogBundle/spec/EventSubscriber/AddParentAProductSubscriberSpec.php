<?php

namespace spec\Pim\Bundle\CatalogBundle\EventSubscriber;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Query;
use Pim\Bundle\CatalogBundle\EventSubscriber\AddParentAProductSubscriber;
use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\EntityWithFamily\Event\ParentWasAddedToProduct;
use Pim\Component\Catalog\Model\VariantProductInterface;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddParentAProductSubscriberSpec extends ObjectBehavior
{
    function let(Query\ConvertProductToVariantProduct $convertProductToVariantProduct)
    {
        $this->beConstructedWith($convertProductToVariantProduct);
    }

    function it is initializable()
    {
        $this->shouldHaveType(AddParentAProductSubscriber::class);
    }

    function it is a subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it subscribes to event()
    {
        $this->getSubscribedEvents()->shouldReturn([
            ParentWasAddedToProduct::EVENT_NAME => 'convertProductToVariantProduct'
        ]);
    }

    function it converts a product to a variant product(
        $convertProductToVariantProduct,
        ParentWasAddedToProduct $event,
        VariantProductInterface $variantProduct
    ) {
        $event->convertedProduct()->willReturn($variantProduct);
        $convertProductToVariantProduct->__invoke($variantProduct)->shouldBeCalled();

        $this->convertProductToVariantProduct($event)->shouldReturn(null);
    }
}
