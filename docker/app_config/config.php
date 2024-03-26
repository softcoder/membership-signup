<?php
// ==============================================================
//	Copyright (C) 2024 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
ini_set('display_errors', 'On');
error_reporting(E_ALL);

if ( defined('INCLUSION_PERMITTED') === false ||
     ( defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false) ) { 
	die( 'This file must not be invoked directly.' ); 
}

require_once 'config_interfaces.php';
require_once 'config_constants.php';
require_once 'config/config_manager.php';

// ==============================================================

// =============================================================================================
// ===--------------EDIT BLOCKS BELOW TO COMPLETE THE SETUP FOR YOUR SITE--------------------===
// =============================================================================================
    $config = new \riprunner\ConfigManager();
    
	// ----------------------------------------------------------------------
	// Email Settings
		
	$EMAIL = new EmailAccount();
	$EMAIL->setHostEnabled(true);
	$EMAIL->setDeleteOnProcessed(false);
	$EMAIL->setEnableOutboundSMTP(true);
	$EMAIL->setOutboundHost(getenv('APP_SMTP_OutboundHost') ? getenv('APP_SMTP_OutboundHost') : 'smtp.googlemail.com');
	$EMAIL->setOutboundPort(getenv('APP_SMTP_OutboundPort') ? getenv('APP_SMTP_OutboundPort') : 587);
	$EMAIL->setOutboundEncrypt(getenv('APP_SMTP_OutboundEncrypt') ? getenv('APP_SMTP_OutboundEncrypt') : 'tls');
	$EMAIL->setOutboundAuth(getenv('APP_SMTP_OutboundAuth') ? getenv('APP_SMTP_OutboundAuth') : true);
	$EMAIL->setOutboundUsername(getenv('APP_SMTP_OutboundUsername') ? getenv('APP_SMTP_OutboundUsername') : 'X@gmail.com');
	$EMAIL->setOutboundPassword(getenv('APP_SMTP_OutboundPassword') ? getenv('APP_SMTP_OutboundPassword') : 'XX');
	$EMAIL->setOutboundFromAddress(getenv('APP_SMTP_OutboundFromAddress') ? getenv('APP_SMTP_OutboundFromAddress') : 'X@gmail.com');
	$EMAIL->setOutboundFromName(getenv('APP_SMTP_OutboundFromName') ? getenv('APP_SMTP_OutboundFromName') : 'Membership Signup');

	// ----------------------------------------------------------------------
	// Website and Location Settings

	$WEBSITE = new Website();
	$WEBSITE->setName(getenv('APP_WEBSITE_Name') ? getenv('APP_WEBSITE_Name') : 'Local Test');
	$WEBSITE->setRootURL(getenv('APP_WEBSITE_RootURL') ? getenv('APP_WEBSITE_RootURL') : '/');
	$WEBSITE->setTimezone(getenv('APP_WEBSITE_Timezone') ? getenv('APP_WEBSITE_Timezone') : 'America/Vancouver');

	// ----------------------------------------------------------------------
	// Main PDF Configuration Container Settings

    $PDF = new PDFSettings();
    $PDF->setEnabled(true);
	$PDF->setOutputPath(getenv('APP_PDF_OutputPath') ? getenv('APP_PDF_OutputPath') : 'output/');
	$PDF->setMembershipfile(getenv('APP_PDF_Membershipfile') ? getenv('APP_PDF_Membershipfile') : 'forms/MembershipForm2024-2025.pdf');
	$PDF->setMembershipfileEmailViewTemplate(getenv('APP_PDF_MembershipfileEmailViewTemplate') ? getenv('APP_PDF_MembershipfileEmailViewTemplate') : 'MembershipForm.pdf');
	$PDF->setWaiverfile(getenv('APP_PDF_Waiverfile') ? getenv('APP_PDF_Waiverfile') : 'forms/E-waiver-FMCBC-Universal-Waiver-Basic-2022.pdf');
	$PDF->setWaiverfileEmailViewTemplate(getenv('APP_PDF_WaiverfileEmailViewTemplate') ? getenv('APP_PDF_WaiverfileEmailViewTemplate') : 'E-waiver-FMCBC-Universal-Waiver-Basic.pdf');
	$PDF->setWebformEmailField(getenv('APP_PDF_WebformEmailField') ? getenv('APP_PDF_WebformEmailField') : 'emailaddress');
	$PDF->setPDFTKPath(getenv('APP_PDF_PDFTKPath') ? getenv('APP_PDF_PDFTKPath') : '');
	$PDF->setEmailPDFToMember(getenv('APP_PDF_EmailPDFToMember') ? getenv('APP_PDF_EmailPDFToMember') : true);
	// comma separated list of people to email a copy of the form
	$PDF->setEmailPDFToDirectors(getenv('APP_PDF_EmailPDFToDirectors') ? getenv('APP_PDF_EmailPDFToDirectors') : '');
	$PDF->setFormsDateRange(getenv('APP_PDF_FormsDateRange') ? getenv('APP_PDF_FormsDateRange') : 'May 1, 2024 - April 30, 2025');

	// ----------------------------------------------------------------------
	// Main TwoFactor Configuration Container Settings

    $TWOFA = new TwoFactor();
    $TWOFA->setEnabled(getenv('APP_TWOFA_Enabled') ? getenv('APP_TWOFA_Enabled') : true);
	$TWOFA->setTotpKey(getenv('APP_TWOFA_TotpKey') ? getenv('APP_TWOFA_TotpKey') : 'DOCKER23MRL5AUQNK3G');
	$TWOFA->setTotpTimeoutSeconds(getenv('APP_TWOFA_TotpTimeoutSeconds') ? getenv('APP_TWOFA_TotpTimeoutSeconds') : 60*45);			
	
	// ----------------------------------------------------------------------
	// Main Configuration Container Settings

	$CFG = new SiteConfig();
	$CFG->setEnabled(true);
	$CFG->setId(0);
	$CFG->setEmailSettings($EMAIL);
	$CFG->setWebsiteSettings($WEBSITE);
	$CFG->setPDF_Settings($PDF);
	$CFG->setTwoFactor($TWOFA);
	
	$SITECONFIGS = array( $CFG);

