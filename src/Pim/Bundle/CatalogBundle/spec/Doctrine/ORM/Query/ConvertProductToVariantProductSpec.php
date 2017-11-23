<?php

namespace spec\Pim\Bundle\CatalogBundle\Doctrine\ORM\Query;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Query\ConvertProductToVariantProduct;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Model\ValueCollectionInterface;
use Pim\Component\Catalog\Model\VariantProductInterface;
use Prophecy\Argument;

class ConvertProductToVariantProductSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $entityManager)
    {
        $this->beConstructedWith($entityManager);
    }

    function it is initializable()
    {
        $this->shouldHaveType(ConvertProductToVariantProduct::class);
    }

    function it transforms a product into variant product in database(
        $entityManager,
        VariantProductInterface $variantProduct,
        Connection $connection,
        ValueCollectionInterface $valueCollection,
        ProductModelInterface $productModel,
        UnitOfWork $unitOfWork,
        Collection $groups,
        Collection $association,
        Collection $completenesses,
        FamilyInterface $family,
        Collection $categories,
        \DateTime $updated,
        \DateTime $created,
        Collection $uniqueData
    ) {

        $variantProduct->getId()->willReturn(64);
        $variantProduct->getValuesForVariation()->willReturn($valueCollection);
        $variantProduct->getParent()->willReturn($productModel);
        $variantProduct->getIdentifier()->willReturn('identifier');
        $variantProduct->getGroups()->willReturn($groups);
        $variantProduct->getAssociations()->willReturn($association);
        $variantProduct->isEnabled()->willReturn(true);
        $variantProduct->getCompletenesses()->willReturn($completenesses);
        $variantProduct->getFamily()->willReturn($family);
        $variantProduct->getCategories()->willReturn($categories);
        $variantProduct->getCreated()->willReturn($created);
        $variantProduct->getUpdated()->willReturn($updated);
        $variantProduct->getUniqueData()->willReturn($uniqueData);
        $productModel->getId()->willReturn(40);

        $entityManager->getConnection()->willReturn($connection);
        $connection->executeQuery(Argument::type('string'), [
            'product_type' => 'variant_product',
            'id' => 64
        ])->shouldBeCalled();

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->registerManaged(
            $variantProduct,
            ['id' => 64],
            [
                'id' => 64,
                'parent' => null,
                'familyVariant' => null,
                'identifier' => 'identifier',
                'groups' => $groups,
                'associations' => $association,
                'enabled' => true,
                'completenesses' => $completenesses,
                'family' => $family,
                'categories' => $categories,
                'created' => $created,
                'updated' => $updated,
                'rawValues' => [],
                'uniqueData' => $uniqueData,
            ]
        )->shouldBeCalled();

        $this->__invoke($variantProduct)->shouldReturn(null);
    }
}
