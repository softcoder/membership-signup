<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================

// ----------------------------------------------------------------------
class EmailAccount
{
	// Indicates whether the email host should be checked for email triggers
	public $EMAIL_HOST_ENABLED;
	// The From email address that is allowed to trigger a callout.
	// Two formats are allowed:
	// 1. Full email address
	//    donotreply@mysite.ca
	// 2. Domain name (all emails from domain)
	//    focc.mycity.ca
	public $EMAIL_FROM_TRIGGER;
	// Email provider connection string to check for email triggers
	public $EMAIL_HOST_CONNECTION_STRING;
	// Email address that will receive callout information
	public $EMAIL_HOST_USERNAME;
	// Email address password that will receive callout information
	public $EMAIL_HOST_PASSWORD;
	// Email should be deleted after it is received and processed.
	public $EMAIL_DELETE_PROCESSED;
	// Only examine unread emails
	public $PROCESS_UNREAD_ONLY;

	// Outbound Email Settings
	public $ENABLE_OUTBOUND_SMTP ;
	public $ENABLE_OUTBOUND_SENDMAIL; 
	public $OUTBOUND_HOST;
	public $OUTBOUND_PORT;
	public $OUTBOUND_ENCRYPT;
	public $OUTBOUND_AUTH;
	public $OUTBOUND_USERNAME;
	public $OUTBOUND_PASSWORD;
	public $OUTBOUND_FROM_ADDRESS;
	public $OUTBOUND_FROM_NAME;
	
	public function __construct($host_enabled=false, $from_trigger=null, 
			$host_conn_str=null, $host_username=null, $host_password=null, 
			$host_delete_processed=true,$unread_only=true) {
		$this->EMAIL_HOST_ENABLED = $host_enabled;
		$this->EMAIL_FROM_TRIGGER = $from_trigger;
		$this->EMAIL_HOST_CONNECTION_STRING = $host_conn_str;
		$this->EMAIL_HOST_USERNAME = $host_username;
		$this->EMAIL_HOST_PASSWORD = $host_password;
		$this->EMAIL_DELETE_PROCESSED = $host_delete_processed;
		$this->PROCESS_UNREAD_ONLY = $unread_only;
	}

	public function __toString() {
	    return $this->toString();
	}
	public function toString() {
		$result = "Email Settings:" .
				  "\nhost enabled: " . var_export($this->EMAIL_HOST_ENABLED, true) .
				  "\nfrom trigger: " . $this->EMAIL_FROM_TRIGGER .
				  "\nconnection string: " . $this->EMAIL_HOST_CONNECTION_STRING .
				  "\nusername: " . $this->EMAIL_HOST_USERNAME .
				  "\ndelete processed emails: " . var_export($this->EMAIL_DELETE_PROCESSED, true) .
				  "\nonly examine unread emails: " . var_export($this->PROCESS_UNREAD_ONLY, true) .
				  "\nenable outbound mail SMTP: " . var_export($this->ENABLE_OUTBOUND_SMTP, true) .
				  "\nenable outbound mail SENDMAIL: " . var_export($this->ENABLE_OUTBOUND_SENDMAIL, true) .
				  "\noutbound mail host: " . $this->OUTBOUND_HOST .
				  "\noutbound mail port: " . $this->OUTBOUND_PORT .
				  "\noutbound mail encrypt: " . $this->OUTBOUND_ENCRYPT .
				  "\noutbound mail auth: " . var_export($this->OUTBOUND_AUTH,true) .
				  "\noutbound mail user: " . $this->OUTBOUND_USERNAME .
				  "\noutbound mail password: " . $this->OUTBOUND_PASSWORD .
				  "\noutbound mail from address: " . $this->OUTBOUND_FROM_ADDRESS .
				  "\noutbound mail from name: " . $this->OUTBOUND_FROM_NAME;
		return $result;
	}
	public function setHostEnabled($host_enabled) {
		$this->EMAIL_HOST_ENABLED = $host_enabled;
	}
	public function setFromTrigger($from_trigger) {
		$this->EMAIL_FROM_TRIGGER = $from_trigger;
	}
	public function setConnectionString($host_conn_str) {
		$this->EMAIL_HOST_CONNECTION_STRING = $host_conn_str;
	}
	public function setUserName($host_username) {
		$this->EMAIL_HOST_USERNAME = $host_username;
	}
	public function setPassword($host_password) {
		$this->EMAIL_HOST_PASSWORD = $host_password;
	}
	public function setDeleteOnProcessed($host_delete_processed) {
		$this->EMAIL_DELETE_PROCESSED = $host_delete_processed;
	}
	public function setProcessUnreadOnly($unread_only) {
	    $this->PROCESS_UNREAD_ONLY = $unread_only;
	}
	
	public function setEnableOutboundSMTP($value) {
	    $this->ENABLE_OUTBOUND_SMTP= $value;
	}
	
	public function setEnableOutboundSendmail($value) {
	    $this->ENABLE_OUTBOUND_SENDMAIL= $value;
	}
	
	public function setOutboundHost($value) {
	    $this->OUTBOUND_HOST= $value;
	}
	
	public function setOutboundPort($value) {
	    $this->OUTBOUND_PORT= $value;
	}
	
	public function setOutboundEncrypt($value) {
	    $this->OUTBOUND_ENCRYPT= $value;
	}
	
	public function setOutboundAuth($value) {
	    $this->OUTBOUND_AUTH= $value;
	}
	
	public function setOutboundUsername($value) {
	    $this->OUTBOUND_USERNAME= $value;
	}
	
	public function setOutboundPassword($value) {
	    $this->OUTBOUND_PASSWORD= $value;
	}
	
	public function setOutboundFromAddress($value) {
	    $this->OUTBOUND_FROM_ADDRESS= $value;
	}
	
	public function setOutboundFromName($value) {
	    $this->OUTBOUND_FROM_NAME= $value;
	}
	
}
		

// ----------------------------------------------------------------------
class Website
{
	// The display name 
	public $NAME;
	// The timezone where the site is located
	public $TIMEZONE;
	// The Base URL where you installed rip runner example: http://mywebsite.com/riprunner/
	public $WEBSITE_ROOT_URL;
	// Maximum number of invalid login attempts before user is locked out
	public $MAX_INVALID_LOGIN_ATTEMPTS;
	
	public function __construct($name=null, $root_url=null, $tz=null,$max_logins=3) {
		
		$this->NAME = $name;
		$this->WEBSITE_ROOT_URL = $root_url;
		$this->TIMEZONE = $tz;
		$this->MAX_INVALID_LOGIN_ATTEMPTS = $max_logins;
	}

	public function __toString() {
	    return $this->toString();
	}
	public function toString() {
		$result = "Website Settings:" .
				"\nSite name: " . $this->NAME .
				"\nSite timezone: " . $this->TIMEZONE .
				"\nBase URL: " . $this->WEBSITE_ROOT_URL .
				"\nMaximum login attempts: " . $this->MAX_INVALID_LOGIN_ATTEMPTS;
						;
		return $result;
	}
	
	public function setName($name) {
		$this->NAME = $name;
	}
	public function setTimezone($tz) {
		$this->TIMEZONE = $tz;
	}
	public function setRootURL($root_url) {
		$this->WEBSITE_ROOT_URL = $root_url;
	}
	public function setMaxLoginAttempts($max_logins) {
	    $this->MAX_INVALID_LOGIN_ATTEMPTS = $max_logins;
	}
}

// ----------------------------------------------------------------------
class PDFSettings
{
	// Indicates whether PDF should be used
	public $ENABLED;
	// Indicates the path for output files
	public $OUTPUTPATH;
	// The PDF membership input file
	public $MEMBERSHIP_FILE;
	// The PDF waiver input file
	public $WAIVER_FILE;
	// The name of the members email field on the webform
	public $WEBFORM_EMAILFIELD;
	// The path on the server to PDFTK
	public $PDFTK_PATH;
	// The setting (true/false) to enable emailing the pdf to the member
	public $EMAILPDF_TO_MEMBER;
	// The list of directors to email the filled out forms
	public $EMAILPDF_TO_DIRECTORS;
	
	public function __construct($enabled=false, $outputpath=null, $membership_file=null, $waiver_file=null,
			$webform_emailfield=null, $pdftk_path=null, $emailpdf_to_member=null, $emailpdf_to_directors=null) {
		
		$this->ENABLED = $enabled;
		$this->OUTPUTPATH = $outputpath;
		$this->MEMBERSHIP_FILE = $membership_file;
		$this->WAIVER_FILE = $waiver_file;
		$this->WEBFORM_EMAILFIELD = $webform_emailfield;
		$this->PDFTK_PATH = $pdftk_path;
		$this->EMAILPDF_TO_MEMBER = $emailpdf_to_member;
		$this->EMAILPDF_TO_DIRECTORS = $emailpdf_to_directors;
	}

	public function __toString() {
	    return $this->toString();
	}
	public function toString() {
		$result = "PDF Settings:" .
				"\nenabled: " . var_export($this->ENABLED, true) .
				"\noutput path: " . $this->OUTPUTPATH .
				"\nmembership file: " . $this->MEMBERSHIP_FILE .
				"\nwaiver file: " . $this->WAIVER_FILE .
				"\nwebform email field: " . $this->WEBFORM_EMAILFIELD .
				"\nPDFTK path: " . $this->PDFTK_PATH .
				"\nemail pdf to member: " . var_export($this->EMAILPDF_TO_MEMBER, true) .
				"\nemail pdf to directors: " . $this->EMAILPDF_TO_DIRECTORS;
		return $result;
	}
	
	public function setEnabled($enabled) {
		$this->ENABLED = $enabled;
	}
	public function setOutputPath($value) {
		$this->OUTPUTPATH = $value;
	}
	public function setMembershipfile($value) {
		$this->MEMBERSHIP_FILE = $value;
	}
	public function setWaiverfile($value) {
		$this->WAIVER_FILE = $value;
	}
	public function setWebformEmailField($value) {
		$this->WEBFORM_EMAILFIELD = $value;
	}
	public function setPDFTKPath($value) {
		$this->PDFTK_PATH = $value;
	}
	public function setEmailPDFToMember($value) {
		$this->EMAILPDF_TO_MEMBER = $value;
	}
	public function setEmailPDFToDirectors($value) {
		$this->EMAILPDF_TO_DIRECTORS = $value;
	}
	
}

// ----------------------------------------------------------------------
class TwoFactor
{
	// Indicates whether PDF should be used
	public $ENABLED;
	// Indicates the TOPT secret key
	public $TOTP_KEY;
	// The TOTP timeout
	public $TOTP_TIMEOUT_SECONDS;
	
	public function __construct($enabled=false, $totpkey=null, $totptimeout=null) {
		
		$this->ENABLED = $enabled;
		$this->TOTP_KEY = $totpkey;
		$this->TOTP_TIMEOUT_SECONDS = $totptimeout;
	}

	public function __toString() {
	    return $this->toString();
	}
	public function toString() {
		$result = "TwoFactor:" .
				"\nenabled: " . var_export($this->ENABLED, true) .
				"\ntotp key: " . $this->TOTP_KEY .
				"\ntotp timeout seconds: " . $this->TOTP_TIMEOUT_SECONDS;
		return $result;
	}
	
	public function setEnabled($enabled) {
		$this->ENABLED = $enabled;
	}
	public function setTotpKey($value) {
		$this->TOTP_KEY = $value;
	}
	public function setTotpTimeoutSeconds($value) {
		$this->TOTP_TIMEOUT_SECONDS = $value;
	}
}

// ----------------------------------------------------------------------
class SiteConfig
{
	// Indicates whether the site is enabled or not
	public $ENABLED;
	// A unique ID to differentiate multiple sites
	public $ID;
	// The Email configuration
	public $EMAIL;
	// The Website configuration
	public $WEBSITE;
	// The PDF configuration
	public $PDFSettings;
	// The two factor settings
	public $TWOFACTOR;
		
	public function __construct($enabled=false, $id=null, $db=null, 
			$email=null, $website=null, $pdfcfg=null, $twofa=null) {
		
		$this->ENABLED = $enabled;
		$this->ID = $id;
		$this->EMAIL = $email;
		$this->WEBSITE = $website;
		$this->PDFSettings = $pdfcfg;
		$this->TWOFACTOR = $twofa;
	}

	public function __toString() {
	    return $this->toString();
	}
	public function toString() {
		$result = "Site Settings:" .
				"\nenabled: " . var_export($this->ENABLED, true) .
				"\nSite ID: " . $this->ID .
				"\n" . $this->EMAIL->toString() .
				"\n" . $this->WEBSITE->toString() .
				"\n" . $this->PDFSettings->toString() .
				"\n" . $this->TWOFACTOR->toString();
		return $result;
	}
	
	public function __get($name) {
	    if (property_exists($this, $name) === true) {
	        return $this->$name;
	    }
	}
	public function __isset($name) {
	    return isset($this->$name);
	}
	public function __set($name, $value) {
        if (property_exists($this, $name) === true) {
            $this->$name = $value;
        }
	}
	
	public function setEnabled($enabled) {
		$this->ENABLED = $enabled;
	}
	public function setId($id) {
		$this->ID = $id;
	}
	public function setEmailSettings($email) {
		$this->EMAIL = $email;
	}
	public function setWebsiteSettings($website) {
		$this->WEBSITE = $website;
	}
	public function setPDF_Settings($cfg) {
		$this->PDFSettings = $cfg;
	}
	public function setTwoFactor($cfg) {
		$this->TWOFACTOR = $cfg;
	}
	
}
