<?php
/**
 * Class manages voucher assignment to user groups
 */
class rwu_blockgroups_voucherserie_groups_ajax extends ajaxListComponent
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,  visible, multilanguage, ident
        array( 'oxtitle',  'oxgroups', 1, 0, 0 ),
        array( 'oxid',     'oxgroups', 0, 0, 0 ),
        array( 'oxid',     'oxgroups', 0, 0, 1 ),
    ),
        'container2' => array(
            array( 'oxtitle',  'oxgroups', 1, 0, 0 ),
            array( 'oxid',     'oxgroups', 0, 0, 0 ),
            array( 'oxid',     'oxobject_block2group', 0, 0, 1 ),
        )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        // looking for table/view
        $sGroupTable = $this->_getViewName('oxgroups');
        $oDb = oxDb::getDb();
        $oConfig = oxRegistry::getConfig();
        $sVoucherId      = $oConfig->getRequestParameter( 'oxid' );
        $sSynchVoucherId = $oConfig->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sVoucherId) {
            $sQAdd  = " from $sGroupTable where 1 ";
        } else {
            $sQAdd  = " from $sGroupTable, oxobject_block2group where ";
            $sQAdd .= " oxobject_block2group.oxobjectid = ".$oDb->quote( $sVoucherId )." and $sGroupTable.oxid = oxobject_block2group.oxgroupsid ";
        }

        if ( $sSynchVoucherId && $sSynchVoucherId != $sVoucherId) {
            $sQAdd .= " and $sGroupTable.oxid not in ( select $sGroupTable.oxid from $sGroupTable, oxobject_block2group where ";
            $sQAdd .= " oxobject_block2group.oxobjectid = ".$oDb->quote( $sSynchVoucherId )." and $sGroupTable.oxid = oxobject_block2group.oxgroupsid ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes selected user group(s) from Voucher serie list.
     *
     * @return null
     */

    public function removeGroupFromVoucher()
    {
        $aRemoveGroups = $this->_getActionIds( 'oxobject_block2group.oxid' );
        if ( oxRegistry::getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject_block2group.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( $aRemoveGroups && is_array( $aRemoveGroups ) ) {
            $sQ = "delete from oxobject_block2group where oxobject_block2group.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aRemoveGroups ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds selected user group(s) to Voucher serie list.
     *
     * @return null
     */

    public function addGroupToVoucher()
    {
        $oConfig = oxRegistry::getConfig();
        $aChosenCat = $this->_getActionIds( 'oxgroups.oxid' );
        $soxId      = $oConfig->getRequestParameter( 'synchoxid');

        if ( $oConfig->getRequestParameter( 'all' ) ) {
            $sGroupTable = $this->_getViewName('oxgroups');
            $aChosenCat = $this->_getAll( $this->_addFilter( "select $sGroupTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenCat ) ) {
            foreach ( $aChosenCat as $sChosenCat) {
                $oNewGroup = oxNew("oxbase");
                $oNewGroup->init( "oxobject_block2group" );
                $oNewGroup->oxobject_block2group__oxobjectid = new oxField($soxId);
                $oNewGroup->oxobject_block2group__oxgroupsid = new oxField($sChosenCat);
                $oNewGroup->save();
            }
        }
    }
}