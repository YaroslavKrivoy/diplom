<?php
namespace Webfitters\Custom\Ui\Component;

class Tracking extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $escaper;
    protected $order;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $order,
        \Magento\Framework\Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->order = $order;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['tracking_code'] = '';
                $order = $this->order->get($item['entity_id']);
                $tracking = $order->getTracksCollection();
                foreach($tracking->getItems() as $track){
                    $item['tracking_code'].=$track->getTrackNumber().'<br/>';
                }
            }
        }
        return $dataSource;
    }
}
