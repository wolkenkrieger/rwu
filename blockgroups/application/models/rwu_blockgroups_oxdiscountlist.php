<?php

class rwu_blockgroups_oxdiscountlist extends rwu_blockgroups_oxdiscountlist_parent
{

    /**
     * If any shops category has "skip discounts" status this parameter value will be true
     *
     * @var bool
     */
    protected $_hasSkipDiscountCategories = null;

    /**
     * Creates discount list filter SQL to load current state discount list
     *
     * @param object $oUser user object
     *
     * @return string
     */
    protected function _getFilterSelect($oUser)
    {
        $oBaseObject = $this->getBaseObject();

        $sTable = $oBaseObject->getViewName();
        $sQ = "select " . $oBaseObject->getSelectFields() . " from $sTable ";
        $sQ .= "where " . $oBaseObject->getSqlActiveSnippet() . ' ';


        // defining initial filter parameters
        $sUserId = null;
        $sGroupIds = null;
        $sCountryId = $this->getCountryId($oUser);
        $oDb = oxDb::getDb();

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser) {

            // user ID
            $sUserId = $oUser->getId();

            // user group ids
            foreach ($oUser->getUserGroups() as $oGroup) {
                if ($sGroupIds) {
                    $sGroupIds .= ', ';
                }
                $sGroupIds .= $oDb->quote($oGroup->getId());
            }
        }

        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $sCountrySql = $sCountryId ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' and oxobject2discount.OXOBJECTID=" . $oDb->quote($sCountryId) . ")" : '0';
        $sUserSql = $sUserId ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' and oxobject2discount.OXOBJECTID=" . $oDb->quote($sUserId) . ")" : '0';
        $sGroupSql = $sGroupIds ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' and oxobject2discount.OXOBJECTID in ($sGroupIds) )" : '0';
        $sBlockedGroupSql = $sGroupIds ? "NOT EXISTS(select oxid from oxobject_block2discount where oxobject_block2discount.OXDISCOUNTID=$sTable.OXID and oxobject_block2discount.oxtype='oxgroups' and oxobject_block2discount.OXOBJECTID in ($sGroupIds) LIMIT 1)" : '1';

        $sQ .= "and ( 
            select 
                if(EXISTS(select 1 from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1), 
                        $sCountrySql, 
                        1) && 
                if(EXISTS(select 1 from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1), 
                        $sUserSql, 
                        1) && 
                if(EXISTS(select 1 from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1), 
                        $sGroupSql, 
                        1) && 
                if(EXISTS(select 1 from oxobject_block2discount, $sGroupTable where $sGroupTable.oxid=oxobject_block2discount.oxobjectid and oxobject_block2discount.OXDISCOUNTID=$sTable.OXID and oxobject_block2discount.oxtype='oxgroups' LIMIT 1), 
                        $sBlockedGroupSql, 
                        1) 
            )";
        return $sQ;
    }
}