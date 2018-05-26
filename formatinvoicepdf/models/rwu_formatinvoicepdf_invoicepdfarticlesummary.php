<?php
/**
 * Created by PhpStorm.
 * User: Rico
 * Date: 26.02.2017
 * Time: 23:44
 */
if (class_exists('InvoicepdfArticleSummary') && class_exists('paypPayPalPlusPdfArticleSummaryPaymentInstructions')) {
    class rwu_formatinvoicepdf_invoicepdfarticlesummary extends InvoicepdfArticleSummary
    {
        /**
         * Sets grand total order price to pdf.
         *
         * @param int &$iStartPos text start position
         */
        protected function _setGrandTotalPriceInfo(&$iStartPos)
        {
            $this->font($this->getFont(), 'B', 8);

            // total order sum
            $sTotalOrderSum = $this->_oData->getFormattedTotalOrderSum() . ' ' . $this->_oData->getCurrency()->name;
            $this->text(45, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLSUM'));
            $this->text(195 - $this->_oPdf->getStringWidth($sTotalOrderSum), $iStartPos, $sTotalOrderSum);
            $iStartPos += 2;

            if ($this->_oData->oxorder__oxdelvat->value || $this->_oData->oxorder__oxwrapvat->value || $this->_oData->oxorder__oxpayvat->value) {
                $iStartPos += 2;
            }
        }

        /**
         * Sets payment method info to pdf.
         *
         * @param int &$iStartPos text start position
         */
        protected function _setPaymentMethodInfo(&$iStartPos)
        {
            $oPayment = oxNew('oxpayment');
            $oPayment->loadInLang($this->_oData->getSelectedLang(), $this->_oData->oxorder__oxpaymenttype->value);

            $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_SELPAYMENT') . $oPayment->oxpayments__oxdesc->value;
            $this->font($this->getFont(), '', 8);
            $this->text(15, $iStartPos + 4, $text);
            $iStartPos += 4;
        }

        /**
         * @inheritdoc
         *
         * Add the possibility to add payment instructions to the due date or 'PayUntilInfo', as it is called in the parent
         * function.
         */
        protected function _setPayUntilInfo(&$iStartPos)
        {
            $oPaymentInstructions = $this->_getPaymentInstructions();

            if ($oPaymentInstructions) {
                $iLang = $this->_oData->getSelectedLang();
				$oPdfArticleSummaryPaymentInstructions = new rwu_formatinvoicepdf_paymentinstructions();
                //$oPdfArticleSummaryPaymentInstructions = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
                $oPdfArticleSummaryPaymentInstructions->setPdfArticleSummary($this);
                $oPdfArticleSummaryPaymentInstructions->setPaymentInstructions($oPaymentInstructions);
                $oPdfArticleSummaryPaymentInstructions->setOrder($this->_getOrder());
                $oPdfArticleSummaryPaymentInstructions->addPaymentInstructions($iStartPos, $iLang);
            }
        }

        /**
         * Return an instance of the related order.
         *
         * @return oxOrder
         */
        protected function _getOrder()
        {
            $sOrderId = $this->_getOrderId();
            $oOrder = oxNew('oxOrder');
            $oOrder->load($sOrderId);

            return $oOrder;
        }

        /**
         * Get the payment instructions from the order.
         *
         * @return null|paypPayPalPlusPuiData|void
         */
        protected function _getPaymentInstructions()
        {
            $oPaymentInstructions = null;

            $sOrderId = $this->_getOrderId();

            $oOrder = oxNew('oxOrder');
            if ($oOrder->load($sOrderId)) {
                $oPaymentInstructions = $oOrder->getPaymentInstructions();
            }

            return $oPaymentInstructions;
        }

        /**
         * Return the ID or the current order.
         * Needed for testing.
         *
         * @codeCoverageIgnore
         *
         * @return mixed
         */
        protected function _getOrderId()
        {
            $sOrderId = $this->_oData->getId();

            return $sOrderId;
        }

        /**
         * Generates order info block (prices, VATs, etc ).
         *
         * @param int $iStartPos text start position
         *
         * @return int
         */
        public function generate($iStartPos)
        {

            $this->font($this->getFont(), '', 8);
            $siteH = $iStartPos;

            // #1147 discount for vat must be displayed
            if (!$this->_oData->oxorder__oxdiscount->value) {
                $this->_setTotalCostsWithoutDiscount($siteH);
            } else {
                $this->_setTotalCostsWithDiscount($siteH);
            }

            $siteH += 12;

            // voucher info
            $this->_setVoucherInfo($siteH);

            // additional line separator
            if ($this->_oData->oxorder__oxdiscount->value || $this->_oData->oxorder__oxvoucherdiscount->value) {
                $this->line(45, $siteH - 3, 195, $siteH - 3);
            }

            // delivery info
            $this->_setDeliveryInfo($siteH);

            // payment info
            $this->_setPaymentInfo($siteH);

            // wrapping info
            $this->_setWrappingInfo($siteH);

            // TS protection info
            $this->_setTsProtection($siteH);

            // separating line
            $siteH += 4;
            $this->line(15, $siteH, 195, $siteH);
            $siteH += 4;

            // total order sum
            $this->_setGrandTotalPriceInfo($siteH);

            // separating line
            $this->line(15, $siteH, 195, $siteH);
            $siteH += 4;

            // payment method
            $this->_setPaymentMethodInfo($siteH);

            // pay until ...
            $this->_setPayUntilInfo($siteH);

            return $siteH - $iStartPos;
        }
    }
}