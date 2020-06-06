<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Model\Service;

/**
 * EmailSender Class
 */
class EmailSender
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Vault
     */
    protected $vaultHelper;

    /**
     * @var \ParadoxLabs\TokenBase\Helper\Address
     */
    protected $addressHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Directory\Model\Currency[]
     */
    protected $currencies = [];

    /**
     * EmailSender constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \ParadoxLabs\Subscriptions\Helper\Vault $vaultHelper
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @internal param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \ParadoxLabs\Subscriptions\Helper\Vault $vaultHelper,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->currencyFactory = $currencyFactory;
        $this->localeDate = $localeDate;
        $this->vaultHelper = $vaultHelper;
        $this->addressHelper = $addressHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Send billing failure email to admin
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $message
     * @return $this
     */
    public function sendBillingFailedEmail(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $message
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */

        $storeId = $subscription->getStoreId();

        if ($this->config->billingFailedEmailsEnabled($storeId) !== true) {
            return $this;
        }

        $paymentFailed = false;
        if ($subscription->getStatus() === \ParadoxLabs\Subscriptions\Model\Source\Status::STATUS_PAYMENT_FAILED) {
            $paymentFailed = true;
        }

        $paymentFailedActive = $this->config->paymentFailedEmailsEnabled($storeId);

        $this->inlineTranslation->suspend();

        $copyTo = $this->config->getBillingFailedCopyEmails($storeId);
        $copyMethod = $this->config->getBillingFailedCopyMethod($storeId);
        $bcc = [];
        if (!empty($copyTo) && $copyMethod === 'bcc') {
            $bcc = $copyTo;
        }

        $sendTo = [
            $this->config->getBillingFailedRecipient($storeId)
        ];

        if (!empty($copyTo) && $copyMethod === 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->config->getBillingFailedTemplate($storeId)
            )->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $subscription->getStoreId(),
                ]
            )->setTemplateVars(
                [
                    'subscription'    => $subscription,
                    'subtotal'        => $this->getFormattedSubtotal($subscription),
                    'reason'          => $message,
                    'paymentFailure'  => $paymentFailed === true && $paymentFailedActive === 1,
                ]
            )->setFrom(
                $this->config->getBillingFailedSender($storeId)
            )->addTo(
                $recipient['email'],
                $recipient['name']
            )->addBcc(
                $bcc
            )->getTransport();

            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Send payment failure email to customer
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $message
     * @return $this
     */
    public function sendPaymentFailedEmail(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $message
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */

        $storeId = $subscription->getStoreId();

        if ($this->config->paymentFailedEmailsEnabled($storeId) !== true) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->config->getPaymentFailedCopyEmails($storeId);
        $copyMethod = $this->config->getPaymentFailedCopyMethod($storeId);
        $bcc = [];
        if (!empty($copyTo) && $copyMethod === 'bcc') {
            $bcc = $copyTo;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();

        $sendTo = [
            [
                'email' => $quote->getCustomerEmail(),
                'name'  => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            ],
        ];

        if (!empty($copyTo) && $copyMethod === 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->config->getPaymentFailedTemplate($storeId)
            )->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $subscription->getStoreId(),
                ]
            )->setTemplateVars(
                [
                    'subscription'    => $subscription,
                    'subtotal'        => $this->getFormattedSubtotal($subscription),
                    'reason'          => $message,
                    'viewUrl'         => $this->urlBuilder->getUrl(
                        'customer/subscriptions/view',
                        [
                            'id' => $subscription->getId(),
                        ]
                    ),
                ]
            )->setFrom(
                $this->config->getPaymentFailedSender($storeId)
            )->addTo(
                $recipient['email'],
                $recipient['name']
            )->addBcc(
                $bcc
            )->getTransport();

            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        return $this;
    }

    public function sendSecondBillingNoticeEmail(\ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription){
        $storeId = $subscription->getStoreId();
        if ($this->config->billingSecondNoticesAreEnabled($storeId) !== true) {
            return $this;
        }
        $this->inlineTranslation->suspend();
        $copyTo = $this->config->getSecondBillingNoticeCopyEmails($storeId);
        $copyMethod = $this->config->getSecondBillingNoticeCopyMethod($storeId);
        $bcc = [];
        if (!empty($copyTo) && $copyMethod === 'bcc') {
            $bcc = $copyTo;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();

        $sendTo = [
            [
                'email' => $quote->getCustomerEmail(),
                'name'  => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            ],
        ];

        if (!empty($copyTo) && $copyMethod === 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        $shippingAddress = false;
        if ((int)$quote->getIsVirtual() === 0) {
            $shippingAddress = $this->addressHelper->getFormattedAddress(
                $quote->getShippingAddress()->exportCustomerAddress()
            );
        }

        $templateVars = [
            'subscription'      => $subscription,
            'subtotal'          => $this->getFormattedSubtotal($subscription),
            'nextRunDate'       => $this->localeDate->formatDate($subscription->getNextRun()),
            'installmentNumber' => $subscription->getRunCount() + 1,
            'shippingAddress'   => $shippingAddress,
            'billingAddress'    => $this->addressHelper->getFormattedAddress(
                $quote->getBillingAddress()->exportCustomerAddress()
            ),
            'cardLabel'         => $this->vaultHelper->getCardLabel(
                $this->vaultHelper->getQuoteCard($quote)
            ),
            'viewUrl'           => $this->urlBuilder->getUrl(
                'customer/subscriptions/view',
                [
                    'id' => $subscription->getId(),
                ]
            ),
        ];

        foreach ($sendTo as $recipient) {
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->config->getSecondBillingNoticeTemplate($storeId)
            )->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )->setTemplateVars(
                $templateVars
            )->setFrom(
                $this->config->getSecondBillingNoticeSender($storeId)
            )->addTo(
                $recipient['email'],
                $recipient['name']
            )->addBcc(
                $bcc
            )->getTransport();

            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Send billing notice email to customer
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @return $this
     */
    public function sendBillingNoticeEmail(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */

        $storeId = $subscription->getStoreId();

        if ($this->config->billingNoticesAreEnabled($storeId) !== true) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->config->getBillingNoticeCopyEmails($storeId);
        $copyMethod = $this->config->getBillingNoticeCopyMethod($storeId);
        $bcc = [];
        if (!empty($copyTo) && $copyMethod === 'bcc') {
            $bcc = $copyTo;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();

        $sendTo = [
            [
                'email' => $quote->getCustomerEmail(),
                'name'  => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            ],
        ];

        if (!empty($copyTo) && $copyMethod === 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        $shippingAddress = false;
        if ((int)$quote->getIsVirtual() === 0) {
            $shippingAddress = $this->addressHelper->getFormattedAddress(
                $quote->getShippingAddress()->exportCustomerAddress()
            );
        }

        $templateVars = [
            'subscription'      => $subscription,
            'subtotal'          => $this->getFormattedSubtotal($subscription),
            'nextRunDate'       => $this->localeDate->formatDate($subscription->getNextRun()),
            'installmentNumber' => $subscription->getRunCount() + 1,
            'shippingAddress'   => $shippingAddress,
            'billingAddress'    => $this->addressHelper->getFormattedAddress(
                $quote->getBillingAddress()->exportCustomerAddress()
            ),
            'cardLabel'         => $this->vaultHelper->getCardLabel(
                $this->vaultHelper->getQuoteCard($quote)
            ),
            'viewUrl'           => $this->urlBuilder->getUrl(
                'customer/subscriptions/view',
                [
                    'id' => $subscription->getId(),
                ]
            ),
        ];

        foreach ($sendTo as $recipient) {
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->config->getBillingNoticeTemplate($storeId)
            )->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )->setTemplateVars(
                $templateVars
            )->setFrom(
                $this->config->getBillingNoticeSender($storeId)
            )->addTo(
                $recipient['email'],
                $recipient['name']
            )->addBcc(
                $bcc
            )->getTransport();

            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Get the formatted subscription subtotal.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @return string
     */
    public function getFormattedSubtotal(\ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription)
    {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $currency = $subscription->getData('quote_currency_code');

        if (!isset($this->currencies[$currency])) {
            $this->currencies[$currency] = $this->currencyFactory->create();
            $this->currencies[$currency]->load($currency);
        }

        return $this->currencies[$currency]->formatTxt($subscription->getSubtotal());
    }
}
