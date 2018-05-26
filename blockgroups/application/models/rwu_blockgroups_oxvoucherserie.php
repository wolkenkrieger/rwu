<?php
class rwu_blockgroups_oxvoucherserie extends rwu_blockgroups_oxvoucherserie_parent
{
    public function getEnabledUserGroups() {
        $this->_oGroups = oxNew( 'oxlist' );
        $this->_oGroups->init( 'oxgroups' );
        $sViewName = getViewName( "oxgroups" );
        $sSelect  = "select gr.* from {$sViewName} as gr, oxobject2group as o2g where o2g.oxobjectid = ". oxDb::getDb()->quote( $this->getId() ) ." and gr.oxid = o2g.oxgroupsid";
        $this->_oGroups->selectString( $sSelect );
        return $this->_oGroups;
    }

    public function getBlockedUserGroups() {
        $this->_oGroups = oxNew( 'oxlist' );
        $this->_oGroups->init( 'oxgroups' );
        $sViewName = getViewName( "oxgroups" );
        $sSelect = "select gr.* from {$sViewName} as gr, oxobject_block2group as ob2g where ob2g.oxobjectid =".oxDb::getDb()->quote($this->getId())." and gr.oxid = ob2g.oxgroupsid";
        $this->_oGroups->selectString( $sSelect );
        return $this->_oGroups;
    }
}