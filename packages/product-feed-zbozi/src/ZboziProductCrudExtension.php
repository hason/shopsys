<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade;
use Symfony\Component\Translation\TranslatorInterface;

class ZboziProductCrudExtension implements PluginCrudExtensionInterface
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade
     */
    private $zboziProductDomainFacade;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade $zboziProductDomainFacade
     */
    public function __construct(
        TranslatorInterface $translator,
        ZboziProductDomainFacade $zboziProductDomainFacade
    ) {
        $this->translator = $translator;
        $this->zboziProductDomainFacade = $zboziProductDomainFacade;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return ZboziProductFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Zbozi.cz product feed');
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getData($productId)
    {
        $zboziProductDomains = $this->zboziProductDomainFacade->findByProductId($productId);

        return !empty($zboziProductDomains) ? $this->getZboziProductDomainsAsPluginDataArray($zboziProductDomains) : [];
    }

    /**
     * @param int $productId
     * @param array $data
     */
    public function saveData($productId, $data)
    {
        $zboziProductDomainsDataIndexedByDomainId = [];

        foreach ($data as $productAttributeName => $productAttributeValuesByDomainIds) {
            foreach ($productAttributeValuesByDomainIds as $domainId => $productAttributeValue) {
                if (!array_key_exists($domainId, $zboziProductDomainsDataIndexedByDomainId)) {
                    $zboziProductDomainsDataIndexedByDomainId[$domainId] = new ZboziProductDomainData();

                    $zboziProductDomainsDataIndexedByDomainId[$domainId]->domainId = $domainId;
                }

                $this->setZboziProductDomainDataProperty(
                    $zboziProductDomainsDataIndexedByDomainId[$domainId],
                    $productAttributeName,
                    $productAttributeValue
                );
            }
        }

        $this->zboziProductDomainFacade->saveZboziProductDomainsForProductId(
            $productId,
            $zboziProductDomainsDataIndexedByDomainId
        );
    }

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData $zboziProductDomainData
     * @param string $propertyName
     * @param string $propertyValue
     */
    private function setZboziProductDomainDataProperty(
        ZboziProductDomainData $zboziProductDomainData,
        $propertyName,
        $propertyValue
    ) {
        switch ($propertyName) {
            case 'show':
                $zboziProductDomainData->show = $propertyValue;
                break;
            case 'cpc':
                $zboziProductDomainData->cpc = $propertyValue;
                break;
            case 'cpc_search':
                $zboziProductDomainData->cpcSearch = $propertyValue;
                break;
        }
    }

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain[] $zboziProductDomains
     * @return array
     */
    private function getZboziProductDomainsAsPluginDataArray(array $zboziProductDomains)
    {
        $pluginData = [
            'show' => [],
            'cpc' => [],
            'cpc_search' => [],
        ];

        foreach ($zboziProductDomains as $zboziProductDomain) {
            $pluginData['show'][$zboziProductDomain->getDomainId()] = $zboziProductDomain->getShow();
            $pluginData['cpc'][$zboziProductDomain->getDomainId()] = $zboziProductDomain->getCpc();
            $pluginData['cpc_search'][$zboziProductDomain->getDomainId()] = $zboziProductDomain->getCpcSearch();
        }

        return $pluginData;
    }

    /**
     * @param int $productId
     */
    public function removeData($productId)
    {
        $this->zboziProductDomainFacade->delete($productId);
    }
}
