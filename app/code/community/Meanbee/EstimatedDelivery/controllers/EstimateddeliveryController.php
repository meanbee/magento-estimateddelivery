<?php

class Meanbee_EstimatedDelivery_EstimateddeliveryController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title('Estimated Delivery');

        $this->_loadLayout();
        $this->renderLayout();
    }

    public function editAction() {
        $this->_title('Edit Estimated Delivery');

        $this->_loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($postData = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('meanbee_estimateddelivery/estimateddelivery');
            if ($id) {
                $model->load($id);
            }
            $model->addData($postData);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('checkout')->__('Successfully saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('checkout')->__('An error occurred while saving.'));
            }

            $this->_redirectReferer();
        }
    }

    protected function _loadLayout() {
        return $this->loadLayout()
            ->_setActiveMenu('sales/meanbee_estimateddelivery');
    }
}
