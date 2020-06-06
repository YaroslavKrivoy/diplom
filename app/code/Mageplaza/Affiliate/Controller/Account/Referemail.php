<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

use Magento\Framework\DataObject;

class Referemail extends \Mageplaza\Affiliate\Controller\Account
{
	const XML_PATH_REFER_EMAIL_TEMPLATE = 'affiliate/refer/account_sharing';

	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$data           = $this->getRequest()->getPostValue();
		$contacts       = $data['contacts'];
		$subject        = $data['subject'];
		$message        = $data['content'];

		$contacts = explode(',', $contacts);
		if (!sizeof($contacts)) {
			$this->messageManager->addErrorMessage(__('Please input your friends email address.'));
			$this->_redirect('*/*/refer');

			return;
		}

		foreach ($contacts as $key => $email) {
			if (strpos($email, '<') !== false) {
				$name  = substr($email, 0, strpos($email, '<'));
				$email = substr($email, strpos($email, '<') + 1);
			} else {
				$emailIdentify = explode('@', $email);
				$name          = $emailIdentify[0];
			}

			$name  = trim($name, '\'"');
			$email = trim(rtrim(trim($email), '>'));
			if (!\Zend_Validate::is($email, 'EmailAddress')) {
				continue;
			}
			$tmpMessage = str_replace('{{friend_name}}', $name, $message);

			$escaper           = $this->_objectManager->create('Magento\Framework\Escaper');
			$params['message'] = /*$escaper->escapeHtml(*/nl2br($tmpMessage)/*)*/;
			$params['subject'] = $escaper->escapeHtml(nl2br($subject));
			try {
				$this->dataHelper->sendEmailTemplate(
					new DataObject(['name' => $name, 'email' => $email]),
					self::XML_PATH_REFER_EMAIL_TEMPLATE,
					$params
				);
				$this->messageManager->addSuccess(__('Your message was successfully sent.'));
			} catch (\Exception $e) {
				$this->messageManager->addErrorMessage(__('Something went wrong while trying to send your message.'));
			}
		}

		$this->_redirect('*/*/refer');

		return;
	}
}
