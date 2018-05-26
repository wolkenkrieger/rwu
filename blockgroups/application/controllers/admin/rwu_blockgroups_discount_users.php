<?php
class rwu_blockgroups_discount_users extends rwu_blockgroups_discount_users_parent
{
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        $sSelGroup = oxRegistry::getConfig()->getRequestParameter("selgroup");

        // all usergroups
        $oGroups = oxNew('oxlist');
        $oGroups->init('oxgroups');
        $oGroups->selectString("select * from " . getViewName("oxgroups", $this->_iEditLang));

        $oRoot = new stdClass();
        $oRoot->oxgroups__oxid = new oxField("");
        $oRoot->oxgroups__oxtitle = new oxField("-- ");
        // rebuild list as we need the "no value" entry at the first position
        $aNewList = array();
        $aNewList[] = $oRoot;

        foreach ($oGroups as $val) {
            $aNewList[$val->oxgroups__oxid->value] = new stdClass();
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxid = new oxField($val->oxgroups__oxid->value);
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxtitle = new oxField($val->oxgroups__oxtitle->value);
        }

        $this->_aViewData["allgroups2"] = $aNewList;

        if (isset($soxId) && $soxId != "-") {
            $oDiscount = oxNew("oxdiscount");
            $oDiscount->load($soxId);

            if ($oDiscount->isDerived()) {
                $this->_aViewData["readonly"] = true;
            }
        }

        $iAoc = oxRegistry::getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oDiscountGroupsAjax = oxNew('discount_groups_ajax');
            $this->_aViewData['oxajax'] = $oDiscountGroupsAjax->getColumns();

            return "popups/discount_groups.tpl";
        } elseif ($iAoc == 2) {
            $oDiscountUsersAjax = oxNew('discount_users_ajax');
            $this->_aViewData['oxajax'] = $oDiscountUsersAjax->getColumns();

            return "popups/discount_users.tpl";
        } elseif ($iAoc == 3) {
            $oDiscountBlockGroupsAjax = oxNew('rwu_blockgroups_discount_groups_ajax');
            $this->_aViewData['oxajax'] = $oDiscountBlockGroupsAjax->getColumns();
            return "rwu_discount_groups_block.tpl";
        }

        return "rwu_discount_users.tpl";
    }
}