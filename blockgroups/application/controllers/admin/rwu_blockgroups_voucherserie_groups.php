<?php
class rwu_blockgroups_voucherserie_groups extends rwu_blockgroups_voucherserie_groups_parent
{
    /**
     * Executes parent method parent::render(), creates oxlist and oxvoucherserie
     * objects, passes it's data to Smarty engine and returns name of template
     * file "voucherserie_groups.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oVoucherSerie = oxNew( "oxvoucherserie" );
            $oVoucherSerie->load( $soxId);
            $oVoucherSerie->setUserGroups();
            $this->_aViewData["edit"] =  $oVoucherSerie;

            //Disable editing for derived items
            if ($oVoucherSerie->isDerived())
                $this->_aViewData['readonly'] = true;
        }
        $iAoc = oxRegistry::getConfig()->getRequestParameter("aoc");
        if ( $iAoc == 1 ) {
            $oVoucherSerieGroupsAjax = oxNew( 'voucherserie_groups_ajax' );
            $this->_aViewData['oxajax'] = $oVoucherSerieGroupsAjax->getColumns();

            return "popups/voucherserie_groups.tpl";
        } elseif ($iAoc == 2) {
            $oVoucherSerieGroupsAjax = oxNew( 'rwu_blockgroups_voucherserie_groups_ajax' );
            $this->_aViewData['oxajax'] = $oVoucherSerieGroupsAjax->getColumns();

            return "rwu_voucherserie_groups_block.tpl";
        }

        return "rwu_voucherserie_groups.tpl";
    }
}