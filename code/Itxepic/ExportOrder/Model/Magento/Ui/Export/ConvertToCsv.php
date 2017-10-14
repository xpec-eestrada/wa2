<?php

namespace Itxepic\ExportOrder\Model\Magento\Ui\Export;


class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{ 
	public function getMoreData($item)
	{
		$data = $item->getData();
		if($item->getIncrementId())
		{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$_orderRepository = $objectManager->create('Magento\Sales\Api\OrderRepositoryInterface');
			$order1  = $_orderRepository->get($item["entity_id"]);
			$phone = $order1->getBillingAddress()->getTelephone();
			$items123 = $order1->getAllItems();
			$skuArray = [];
			$qtyArray = [];
			$nameArray = [];
			$idArray = [];
			foreach ($items123 as $item1) {
				
				$idArray[] = $item1->getProductId();
				$nameArray[] = $item1->getName();
				$qtyArray[] = $item1->getQtyOrdered();
				$skuArray[] = $item1->getSku();
			}
			$ids = implode (", ", $idArray);
			$sku = implode (", ", $skuArray);
			$name = implode (", ", $nameArray);
			
			$data['product_id'] = $ids;
			$data['sku'] = $sku;
			$data['quantity'] = array_sum($qtyArray);
			$data['name'] = $name;
			$data['dni'] = 'DNI';
			$data['phone'] = $phone;
		}
		return $data;
	}
	
	public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int) $dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $item) {
				$data = $this->getMoreData($item); 
				$item->setData($data);
                $this->metadataProvider->convertDate($item, $component->getName());
                $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
	
	