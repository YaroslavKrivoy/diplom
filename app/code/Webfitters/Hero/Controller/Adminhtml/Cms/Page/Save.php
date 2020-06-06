<?php
namespace Webfitters\Hero\Controller\Adminhtml\Cms\Page;

use Magento\Backend\App\Action;
use Magento\Cms\Model\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Cms\Controller\Adminhtml\Page\Save {
    
    const ADMIN_RESOURCE = 'Magento_Cms::save';
    protected $dataProcessor;
    protected $dataPersistor;
    protected $filesystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor $dataProcessor,
        \Magento\Framework\App\Request\DataPersistor $dataPersistor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Cms\Model\PageFactory $pageFactory = null,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository = null
    ){
        $this->filesystem=$filesystem;
        parent::__construct($context, $dataProcessor, $dataPersistor, $pageFactory, $pageRepository);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();


        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Page::STATUS_ENABLED;
            }
            if (empty($data['page_id'])) {
                $data['page_id'] = null;
            }

            /** @var \Magento\Cms\Model\Page $model */
            $model = $this->_objectManager->create('Magento\Cms\Model\Page');

            $id = $this->getRequest()->getParam('page_id');
            if ($id) {
                $model->load($id);
            }

            // Add custom image field to data
            if(isset($data['hero_image']) && is_array($data['hero_image'])){
                $media = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
                //var_dump($media);
                //die();
                if(!file_exists($media.'cms')){
                    mkdir($media.'cms');
                }
                if(!file_exists($media.'cms/pages')){
                    mkdir($media.'cms/pages');
                }
                if(file_exists($media.'catalog/tmp/category/'.$data['hero_image'][0]['name'])){
                    $final = 'cms/pages/'.bin2hex(openssl_random_pseudo_bytes(3)).'-'.$data['hero_image'][0]['name'];
                    $image = fopen($media.$final, 'w+');
                    fwrite($image, file_get_contents($media.'catalog/tmp/category/'.$data['hero_image'][0]['name']));
                    fclose($image);
                    $data['hero_image']=$final;
                } else {
                    $data['hero_image'] = 'cms/pages/'.$data['hero_image'][0]['name'];
                }
            }


            $model->setData($data);

            $this->_eventManager->dispatch(
                'cms_page_prepare_save',
                ['page' => $model, 'request' => $this->getRequest()]
            );

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $model->getId(), '_current' => true]);
            }

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the page.'));
                $this->dataPersistor->clear('cms_page');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['page_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die();
                $this->messageManager->addException($e, __('Something went wrong while saving the page.'));
            }

            $this->dataPersistor->set('cms_page', $data);
            return $resultRedirect->setPath('*/*/edit', ['page_id' => $this->getRequest()->getParam('page_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
?>