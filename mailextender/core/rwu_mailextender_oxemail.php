<?php

class rwu_mailextender_oxemail extends rwu_mailextender_oxemail_parent
{
	private function _getPayUntil ($days = 10) {		
	   $now = time();
	   $day = getdate($now);
	   
	   if($day['weekday'] == "Saturday") {
		   $now = $now + 86400*2;
	   }
	   if($day['weekday'] == "Sunday") {
		   $now  = $now + 86400;
	   }
	   
	   for ($i = 0; $i < $days; $i++)
	   {
		   $now = $now + 86400;
		   $day = getdate($now);
		   
		   if($day['weekday'] == "Saturday") {
			   $now = $now + 86400 * 2;
		   } 
		} 
		$now = date("d.m.Y",$now);
		
		return $now;
	}
	
	private function _getOrderFolder ($oOrder) {
		$folder = $oOrder->oxorder__oxfolder->value;
	}
    /**
     * Sets mailer additional settings and sends "SendedNowMail" mail to user.
     * Returns true on success.
     *
     * @param oxOrder $oOrder   order object
     * @param string  $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendSendedNowMail( $oOrder, $sSubject = null )
    {
        $myConfig = $this->getConfig();

        $iOrderLang = (int) ( isset( $oOrder->oxorder__oxlang->value ) ? $oOrder->oxorder__oxlang->value : 0 );

        // shop info
        $oShop = $this->_getShop( $iOrderLang );

        //set mail params (from, fromName, smtp)
        $this->_setMailParams( $oShop );

        //create messages
        $oLang = oxRegistry::getLang();
        $oSmarty = $this->_getSmarty();
        $this->setViewData( "order", $oOrder );
        $this->setViewData( "shopTemplateDir", $myConfig->getTemplateDir(false) );

        // BOC Rico Wunglück
        // adding user object to viewdata
        $oUser = $oOrder->getOrderUser();
        $this->setUser( $oUser );
		// adding Date to pay until to viewdata
		$this->setViewData("payUntil", $this->_getPayUntil(10));
        // EOC Rico Wunglück

        if ( $myConfig->getConfigParam( "bl_perfLoadReviews" ) ) {
            $oUser = oxNew( 'oxuser' );
            $this->setViewData( "blShowReviewLink", true );
            $this->setViewData( "reviewuserhash", $oUser->getReviewUserHash($oOrder->oxorder__oxuserid->value) );
        }

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        // dodger #1469 - we need to patch security here as we do not use standard template dir, so smarty stops working
        $aStore['INCLUDE_ANY'] = $oSmarty->security_settings['INCLUDE_ANY'];
        //V send email in order language
        $iOldTplLang = $oLang->getTplLanguage();
        $iOldBaseLang = $oLang->getTplLanguage();
        $oLang->setTplLanguage( $iOrderLang );
        $oLang->setBaseLanguage( $iOrderLang );

        $oSmarty->security_settings['INCLUDE_ANY'] = true;
        // force non admin to get correct paths (tpl, img)
        $myConfig->setAdminMode( false );
        $this->setBody( $oSmarty->fetch( $this->_sSenedNowTemplate ) );
        $this->setAltBody( $oSmarty->fetch( $this->_sSenedNowTemplatePlain ) );
        $myConfig->setAdminMode( true );
        $oLang->setTplLanguage( $iOldTplLang );
        $oLang->setBaseLanguage( $iOldBaseLang );
        // set it back
        $oSmarty->security_settings['INCLUDE_ANY'] = $aStore['INCLUDE_ANY'] ;

        //Sets subject to email
		$orderFolder = $oOrder->oxorder__oxfolder->value;
		$orderStatus = strrchr($orderFolder, '_');
		$sSubject = $oLang->translateString('MAIL_SUBJECT' . $orderStatus);
		$this->setSubject( ( $sSubject !== null ) ? $sSubject : $oShop->oxshops__oxsendednowsubject->getRawValue() );
		

        $sFullName = $oOrder->oxorder__oxbillfname->getRawValue() . " " . $oOrder->oxorder__oxbilllname->getRawValue();

        $this->setRecipient( $oOrder->oxorder__oxbillemail->value, $sFullName );
        $this->setReplyTo( $oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue() );
		
		$this->set('ConfirmReadingTo', $oShop->oxshops__oxorderemail->value);

        return $this->send();
    }
} 