<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class rwu_formatinvoicepdf_oxpdf extends rwu_formatinvoicepdf_oxpdf_parent {
	/**
     * Sets font for current text line
     *
     * NOTICE: In case you have problems with fonts, you must override this function and set different font
     *
     * @param string $family   font family
     * @param string $style    font style [optional]
     * @param string $size     font size [optional]
     * @param string $fontfile font file[optional]
     */
    public function SetFont($family, $style = '', $size = 0, $fontfile = '')
    {
        if ($family == 'Arial') {
            // overriding standard ..
            $family = oxRegistry::getConfig()->isUtf() ? 'helvetica' : '';
        }

        parent::SetFont($family, $style, $size, $fontfile);
    }
}
