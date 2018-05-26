<?php

/**
 * Class manages discount groups
 */
class rwu_blockgroups_discount_groups_ajax extends ajaxListComponent
{

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array('container1' => array( // field , table,  visible, multilanguage, ident
        array('oxtitle', 'oxgroups', 1, 0, 0),
        array('oxid', 'oxgroups', 0, 0, 0),
        array('oxid', 'oxgroups', 0, 0, 1),
    ),
        'container2' => array(
            array('oxtitle', 'oxgroups', 1, 0, 0),
            array('oxid', 'oxgroups', 0, 0, 0),
            array('oxid', 'oxobject_block2discount', 0, 0, 1),
        )
    );

    /**
     * Returns SQL query for data to fetch
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oConfig = $this->getConfig();
        // active AJAX component
        $sGroupTable = $this->_getViewName('oxgroups');
        $oDb = oxDb::getDb();
        $sId = $oConfig->getRequestParameter('oxid');
        $sSynchId = $oConfig->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sId) {
            $sQAdd = " from $sGroupTable where 1 ";
        } else {
            $sQAdd = " from oxobject_block2discount, $sGroupTable where $sGroupTable.oxid=oxobject_block2discount.oxobjectid ";
            $sQAdd .= " and oxobject_block2discount.oxdiscountid = " . $oDb->quote($sId) . " and oxobject_block2discount.oxtype = 'oxgroups' ";
        }

        if ($sSynchId && $sSynchId != $sId) {
            $sQAdd .= " and $sGroupTable.oxid not in ( select $sGroupTable.oxid from oxobject_block2discount, $sGroupTable where $sGroupTable.oxid=oxobject_block2discount.oxobjectid ";
            $sQAdd .= " and oxobject_block2discount.oxdiscountid = " . $oDb->quote($sSynchId)." and oxobject_block2discount.oxtype = 'oxgroups' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes user group from discount config
     */
    public function removeDiscGroup()
    {
        $oConfig = $this->getConfig();

        $aRemoveGroups = $this->_getActionIds('oxobject_block2discount.oxid');
        if ($oConfig->getRequestParameter('all')) {

            $sQ = $this->_addFilter("delete oxobject_block2discount.* " . $this->_getQuery());
            oxDb::getDb()->Execute($sQ);

        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sRemoveGroups = implode(", ", oxDb::getInstance()->quoteArray($aRemoveGroups));
            $sQ = "delete from oxobject_block2discount where oxobject_block2discount.oxid in (" . $sRemoveGroups . ") ";
            oxDb::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds user group to discount config
     */
    public function addDiscGroup()
    {
        $oConfig = $this->getConfig();
        $aChosenCat = $this->_getActionIds('oxgroups.oxid');
        $soxId = $oConfig->getRequestParameter('synchoxid');


        if ($oConfig->getRequestParameter('all')) {
            $sGroupTable = $this->_getViewName('oxgroups');
            $aChosenCat = $this->_getAll($this->_addFilter("select $sGroupTable.oxid " . $this->_getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aChosenCat)) {
            foreach ($aChosenCat as $sChosenCat) {
                $oObject2Discount = oxNew("oxbase");
                $oObject2Discount->init('oxobject_block2discount');
                $oObject2Discount->oxobject_block2discount__oxdiscountid = new oxField($soxId);
                $oObject2Discount->oxobject_block2discount__oxobjectid = new oxField($sChosenCat);
                $oObject2Discount->oxobject_block2discount__oxtype = new oxField("oxgroups");
                $oObject2Discount->save();
            }
        }
    }
}