<?php

class rwu_blockgroups_oxvoucher extends rwu_blockgroups_oxvoucher_parent
{

    /**
     * Checks if user belongs to the same group as the voucher. Returns true on sucess.
     *
     * @param object $oUser user object
     *
     * @throws oxVoucherException exception
     *
     * @return bool
     */
    protected function _isValidUserGroup( $oUser )
    {
        $oVoucherSeries = $this->getSerie();
        $oEnabledUserGroups = $oVoucherSeries->getEnabledUserGroups();
        $oBlockedUserGroups = $oVoucherSeries->getBlockedUserGroups();

        if ( $oUser && $oBlockedUserGroups->count()) {
            foreach ( $oBlockedUserGroups as $oGroup ) {
                if ( $oUser->inGroup( $oGroup->getId() ) ) {
                    $oEx = oxNew( 'oxVoucherException' );
                    $oEx->setMessage( 'ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP' );
                    $oEx->setVoucherNr( $this->oxvouchers__oxvouchernr->value );
                    throw $oEx;
                    return;
                }
            }
        }

        if ( !$oEnabledUserGroups->count() ) {
            return true;
        }

        if ( $oUser ) {
            foreach ( $oEnabledUserGroups as $oGroup ) {
                if ( $oUser->inGroup( $oGroup->getId() ) ) {
                    return true;
                }
            }
        }

        $oEx = oxNew( 'oxVoucherException' );
        $oEx->setMessage( 'ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP' );
        $oEx->setVoucherNr( $this->oxvouchers__oxvouchernr->value );
        throw $oEx;
    }
}
