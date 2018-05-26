<?php

/*
 * Modul: DPD Versand
 * Oxid-Version: 4.10.x
 * Theme: vt's Glow https://github.com/vanilla-thunder/glow
 * Autor: Rico Wunglück kontakt@hundemineral.de
 */

class rwu_dpdshipping_helper {
	
	const PAPER_SIZE              = 'PDF_A4';
    const RETURN_SERVICE          = 'Shop_Return';
    const MIN_WEIGHT              = 5;
    const MAX_WEIGHT              = 31.5;
    const MODE_SANDBOX              = 0;
    const MODE_LIVE               = 1;
	const RETOURE_DIRECTPRINT     = 1;
    const RETOURE_PORTALSERVICE   = 2;

    const ERR_SERVICE_NOTAVAILABLE    = "Der Service ist momentan nicht erreichbar. Das tut uns leid. Bitte kontaktieren Sie DPD.";
	
	private $restApiStage         = "https://cloud-stage.dpd.com/api/v1/";
    private $partnerNameStage     = "DPD Sandbox";
    private $partnerTokenStage    = "06445364853584D75564";

    private $restApiLive          = "https://cloud.dpd.com/api/v1/";
    private $partnerNameLive      = "Oxid";
    private $partnerTokenLive     = "358714E356A474344575";
    private $mode;
	public $apiresponse			  = "";
	public $dpdaddconf			  = 0;

    public function __construct(){
        $this->setMode();
    }

    public function setMode($mode = self::MODE_SANDBOX){    	
        $this->mode = $this->_getConfig()->getConfigParam('dx_dpdPluginMode');
    }

    /**
     * getPluginMode
     *
     * @since: Version 1.0.0
     *
     * @return string
     */
    public function getMode(){
        return $this->mode;
    }

    /**
     * gerPartnerToken
     * returns the Partner Token set for choosen mode
     *
     * @since: Version 1.0.0
     *
     * @return string
     */
    public function getPartnerToken(){
        if($this->getMode() == self::MODE_LIVE){
            return $this->partnerTokenLive;
        }elseif($this->getMode() == self::MODE_DEBUG){
            return $this->partnerTokenStage;
        }
    }

    /**
     * getPartnerName
     * returns the Partnername set for choosen mode
     *
     * @since: Version 1.0.0
     *
     * @return string
     */
    public function getPartnerName(){
        if($this->getMode() == self::MODE_LIVE){
            return $this->partnerNameLive;
        }elseif($this->getMode() == self::MODE_DEBUG){
            return $this->partnerNameStage;
        }
    }

    /**
     * getUrl
     * returns the Url set for choosen mode
     *
     * @since: Version 1.0.0
     *
     * @return string
     */
    public function getUrl(){    	    	
        if($this->getMode() == self::MODE_LIVE){        	
            return $this->restApiLive;
        }elseif($this->getMode() == self::MODE_DEBUG){        	
            return $this->restApiStage;
        }
    }

    /**
     * restCall
     * runs a rest call with url by mode given and returns the response as object
     * runs get methods if $data is null and post call if $data is filled
     *
     * @since: Version 1.0.0
     *
     * @param string $restFunction the name of the restfunction called like "setOrder" or "zipCodeRules"
     * @param string $locale Language like "de_DE" or "en_EN" standard is "de_DE"
     * @param array $data postfield data array
     * @param array $printParcelShopFinder postfield data integer
     * @return mixed response to the restcall
     */
    public function restCall($restFunction, $locale = "de_DE", $data = null,$checkDataDecode = 0){
        $url            = $this->getUrl().$restFunction;		
        $partnerName    = $this->getPartnerName();
        $partnerToken   = $this->getPartnerToken();		
		
        $httpHeaderArray = array(
            'Content-Type: application/json',
            'Version : ' . '100',
            'Language : ' . $locale,
            'PartnerCredentials-Name : ' . $partnerName,
            'PartnerCredentials-Token : ' . $partnerToken,
            'UserCredentials-cloudUserID : ' . $this->_getConfig()->getConfigParam('dx_dpduserid'),
            'UserCredentials-Token : ' . $this->_getConfig()->getConfigParam('dx_dpdusertoken'),
        );
		
        if($data == null){
            $response = $this->httpGet($url, $httpHeaderArray,$checkDataDecode);			
        }else{
            $response = $this->httpPost($url, $httpHeaderArray, $data);
        }		
		
        return $response;
    }

    /**
     * httpGet
     * inits a curl object like dpd example for http get
     *
     * @since: Version 1.0.0
     *
     * @param $url  rest url
     * @param array $httpHeaderArray header data
     * @return mixed response (object)
     */
    private function httpGet($url, array $httpHeaderArray, $checkDataDecode = 0){

        $myRestApiGetCall = curl_init();

        curl_setopt($myRestApiGetCall, CURLOPT_URL, $url);
        curl_setopt($myRestApiGetCall, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($myRestApiGetCall, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($myRestApiGetCall, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($myRestApiGetCall, CURLOPT_HTTPGET, TRUE);
        curl_setopt($myRestApiGetCall, CURLOPT_HTTPHEADER, $httpHeaderArray);

        $apiResponse = curl_exec($myRestApiGetCall);				
		if($checkDataDecode == 1) {			
			return json_decode($apiResponse, true);
		} else {
			return json_decode($apiResponse);	
		}
		
        
    }

    /**
     * httpPost
     * inits a curl object like dpd example for http post
     *
     * @since: Version 1.0.0
     *
     * @param $url rest url
     * @param array $httpHeaderArray header data
     * @param array $data postfield data
     * @return mixed response (object)
     */
    private function httpPost($url, array $httpHeaderArray, $data){

        $curlInit = curl_init($url);

        $jsonData = json_encode($data);
		
        curl_setopt_array($curlInit, array(

                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => $httpHeaderArray,
                CURLOPT_POSTFIELDS => $jsonData,
                CURLOPT_SSL_VERIFYHOST=> false,
                CURLOPT_SSL_VERIFYPEER=>false)

        );

        $apiResponse = curl_exec($curlInit);
        return json_decode($apiResponse);
    }
	
	/**
     * Returns oxConfig instance
     *
     * @return oxConfig
     */
    protected function _getConfig()
    {
        return oxRegistry::getConfig();
    }
	
	
	 /**
	 *
     * setHolidaysDetails
     * set holiday list into genrate new holiday table.
     *
     * @since: Version 1.0.0
     *
     * @param $holidayList holidaylist data
     *
     */
	public function setHolidaysDetails($holidayList) {
	 
	 $oDb = oxDb::getDb();
	 $delteHolidayList = "TRUNCATE TABLE `dx_dpd_holidays`";
	 $oDb->execute($delteHolidayList);
	 $holidayCount = count($holidayList);
	 	
	 	for($i=0;$i<$holidayCount;$i++) {	 	
	 
			$newDate = date("Y-m-d", strtotime($holidayList[$i]));
			$oxid = oxUtilsObject::getInstance()->generateUID();	
		 	$sQ = "INSERT INTO `dx_dpd_holidays` (`oxid`,`holiday_date`) VALUES('$oxid','$newDate')";
			$oDb->execute($sQ);
			
		 }   
	
	}
	
	/**
	 * getSystemPrintFormat
	 * returns the printformat for labels set in plugin configuration
	 *
	 * @return string
	 * @since: Version 1.0.0
	 */
	public function getSystemPrintFormat()
	{
		$printFormat = 'PDF_A4';		
		switch ($this->_getConfig()->getConfigParam('dx_packetprintformat')) {
			case 1:
				$printFormat = 'PDF_A4';
				break;
			case 2:
				$printFormat = 'PDF_A6';
				break;
		}
		return $printFormat;
	}	
	
	/**
	 * getRetourenmanagementValue
	 * get value through configuration for retoure field management
	 *
	 * @return string
	 * @since: Version 1.0.0
	 */
	public function getRetourenmanagementValue() {
		
	 	$retoureData = 1;
		$retoureData = $this->_getConfig()->getConfigParam('dx_dpdretouremanage');
		return $retoureData;
	}
	
	/**
	 * getCountryName
	 * get country title using country id
	 *
	 * @return string
	 * @since: Version 1.0.0
	 */
	public function getCountryName($countryId) {
		
 		$oDb = oxDb::getDb('true');				
		$countryName = "";
		$sQ = "SELECT oxtitle FROM oxcountry WHERE oxid='".$countryId."'";		
		$countryName = $oDb->getOne($sQ);
		// Änderung hundemineral -> Inselsplitting!
		
		$iTreffer = 0;
		$iTreffer = stripos($countryName, '(');
		if ($iTreffer !== false) {
			$countryName = substr($countryName, 0, $iTreffer-1);
		}
		return $countryName;
		
	}
	
	/**
     * @param $orderid
     * @return array
     */
    public function getOrderLocale($orderid){
    	
		$oDb = oxDb::getDb('true');
		$localeData = 0;
		$sQ = "SELECT oxlang FROM oxorder WHERE oxid='".$orderid."'";
		$localeData = $oDb->getOne($sQ);
		return $localeData;
		
	}
	
	/**
     * getProductWeight
     * get the weight of a product by orderid
     *
     * @param $orderid
     * @return string
	 * @since: Version 1.0.0 rev 0
     */
	public function getProductWeight($orderid){
        $oDb = oxDb::getDb('true');
	
		$sQ = "SELECT 	SUM(a.OXWEIGHT)
               FROM 	oxarticles a
               JOIN 	oxorderarticles o ON a.OXID = o.OXARTID
               WHERE 	o.OXORDERID = '$orderid'";

		$result = $oDb->getOne($sQ);
		$weight = ($result > 0)? $result : 5;
		
		return number_format($weight,2,".","");
	}
	
	/**
     * logError
     *
     * @since: Version 1.0.0
     *
     * @param $api
     * @param $labelID
     * @param $orderID
     * @return string
     */     
    public function logError($api,$labelID,$orderID)
	{
		
		$oOrder = oxNew( 'oxorder' );				
		$oOrder->load( $orderID ); 			
		$reference2       = $oOrder->oxorder__oxordernr->value;		
    	$weight           = $this->getProductWeight($orderID);
		$aParams		  = (array) oxRegistry::getConfig()->getRequestParameter("editval");
		$genNoofPDF 	  = $aParams['dx_dpd_shipping_label_no'];
		
		if($genNoofPDF == "") {
			$genNoofPDF = 1;
		} else {
			$genNoofPDF = $genNoofPDF;
		}
		
		$oDb = oxDb::getDb('true');
		$error = '';				
		$i=0;
		$aError = array();
		$iBaseLanguage = oxRegistry::getLang()->getBaseLanguage();				
		
		foreach($api->ErrorDataList as $key=>$value){			
				$oxid 			= oxUtilsObject::getInstance()->generateUID();	
				$dxerrorID  	= $value->ErrorID;
	            $dxerrorCODE  	= $value->ErrorCode;
	            $dxerrorMsgShort= addslashes($value->ErrorMsgShort);
	            $dxerrorMsgLong = addslashes($value->ErrorMsgLong);
	            if(!in_array($value->ErrorID,$aError)){
	            	$aError[$i] = $value->ErrorID;
					
					if($value->ErrorCode == "CLOUD_API_ORDER_WEIGHT") {
							
							$errro1 =  oxRegistry::getLang()->translateString('DX_ERRORMSG_WEIGHT', $iBaseLanguage, false);
							$errro2 =  oxRegistry::getLang()->translateString('DX_ERRORMSG_WEIGHT1', $iBaseLanguage, false);
							$errro3 =  oxRegistry::getLang()->translateString('DX_ERRORMSG_WEIGHT2', $iBaseLanguage, false);
							$errro4 =  oxRegistry::getLang()->translateString('DX_ERRORMSG_WEIGHT3', $iBaseLanguage, false);
							
			 				$error .= $errro1.$reference2.$errro2.$weight.$errro3.$genNoofPDF.$errro4;				
						
					} else {			
            			$error .= $value->ErrorMsgLong."<br>";
					}
	            	//$error .= $value->ErrorMsgShort."<br>";
				
		            $sQuery = "INSERT INTO `dx_dpd_errorlog`(`OXID`, `ORDERID`, `LABELID`, `DXERRORID`, `DXERRORCODE`, `DXERRORMSGSHORT`, `DXERRORMSGLONG`) VALUES ('$oxid','$orderID','$labelID','$dxerrorID','$dxerrorCODE','$dxerrorMsgShort','$dxerrorMsgLong')";			
		            $oDb->execute($sQuery);
				}
				$i++;
		}		
		return $error;
	}
	
	/**
     * getProductName
     *
     * @since: Version 1.0.0
     *
     * @param $orderid
     * @return string
     */
    public function getProductName($orderid){
    	$oDb = oxDb::getDb('true');				
		$sQ = "SELECT GROUP_CONCAT(art.oxtitle SEPARATOR ' & ') FROM oxorderarticles as art join oxorder on oxorder.oxid = '".$orderid."' where art.oxorderid='".$orderid."'";		
		$content = $oDb->getOne($sQ);
		if(strlen($content) > 35){
			$content = substr($content,0,32);
			$content .= '...';
		}
		return $content;
	}
	
	/**
	* checkHolidayDate
	* compare holiday date with ship date and return correct ship date.
	*			  
	* @since: Version 1.0.0 rev 0
	**/	
	public function checkHolidayDate($time = null) {
		
		$oHelper = oxNew("dx_dpdversand_core_helper");
	
		if(date('w',$time) == 0){//If day is Sunday go to Monday to check
			$time = $time + 86400;
			$shipDate = date('Y-m-d',$time);
			$holidayCheck = date('d.m.Y',$time);
		}elseif(date('w',$time) == 6){
			$time = $time + (86400*2);
			$shipDate = date('Y-m-d',$time);
			$holidayCheck = date('d.m.Y',$time);
		}else{
			$shipDate =	date('Y-m-d',$time);
			$holidayCheck = date('d.m.Y',$time);
		}
		
  		$response = $oHelper->restCall("ZipCodeRules");
		$nopickupdate = $response->ZipCodeRules->NoPickupDays;
		$nopickupdate = rtrim($nopickupdate, ',');	
        $aHolidays = explode(',',$nopickupdate);
		
		if(in_array($holidayCheck,$aHolidays)){
			$nextDay = strtotime($holidayCheck) + 86400;
			return $this->checkHolidayDate($nextDay);
		}
		else{
			return $shipDate;
		}
    }
	
}