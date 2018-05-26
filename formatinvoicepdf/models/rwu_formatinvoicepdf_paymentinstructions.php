<?php
class rwu_formatinvoicepdf_paymentinstructions extends paypPayPalPlusPdfArticleSummaryPaymentInstructions {
	/**
     * Do the formatting of the payment instructions text lines as a total.
     *
     * @param $sLegalNotice
     * @param $sTerm
     * @param $sBankName
     * @param $sAccountHolder
     * @param $sIban
     * @param $sBic
     * @param $sAmount
     * @param $sReferenceNumber
     *
     * @return array
     */
    protected function _getPaymentInstructionsTextLines($sLegalNotice, $sTerm, $sBankName, $sAccountHolder, $sIban, $sBic, $sAmount, $sReferenceNumber)
    {
        /** @var integer $iLineWidth Line width in characters, adapt this if you change the font size */
        $iLineWidth = 110;
        /** @var string $sWrapBreak Delimiter string for wordwrap */
        $sWrapBreak = '###';
        /** @var array $aTextLines Initial lines array. It starts with a blank line */
        $aTextLines = array('');

        /** Merge rest of params into text lines */
        $aTextLines = array_merge(
            $aTextLines,
            array(
                $sTerm,
                '',
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME', $sBankName),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER', $sAccountHolder),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN', $sIban),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_BIC', $sBic),
                '',
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT', $sAmount),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER', $sReferenceNumber),
            )
        );

        /** Add 2 blank lines */
        $aTextLines = array_merge(
            $aTextLines,
            array('', '')
        );

        /** Merge legal notice into text lines */
        $aLegalNotice = $this->_explodeLongString($sLegalNotice, $sWrapBreak, $iLineWidth);
        $aTextLines = array_merge($aTextLines, $aLegalNotice);

        /** Add 2 blank lines */
        $aTextLines = array_merge(
            $aTextLines,
            array('')
        );

        return $aTextLines;
    }
}
?>
