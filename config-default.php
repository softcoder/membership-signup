<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
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
	$EMAIL->setOutboundHost('smtp.googlemail.com');
	$EMAIL->setOutboundPort(587);
	$EMAIL->setOutboundEncrypt('tls');
	$EMAIL->setOutboundAuth(true);
	$EMAIL->setOutboundUsername('myemail@gmail.com');
	$EMAIL->setOutboundPassword('xyz');
	$EMAIL->setOutboundFromAddress('myemail@gmail.com');
	$EMAIL->setOutboundFromName('Caledonia Ramblers Membership');

	// ----------------------------------------------------------------------
	// Website and Location Settings

	$WEBSITE = new Website();
	$WEBSITE->setName('Caledonia Ramblers');
	$WEBSITE->setRootURL('https://somewebsite.com/ramblers/');
	$WEBSITE->setTimezone('America/Vancouver');

	// ----------------------------------------------------------------------
	// Main PDF Configuration Container Settings

    $PDF = new PDFSettings();
    $PDF->setEnabled(true);
	$PDF->setOutputPath('output/');
	$PDF->setMembershipfile('forms/MembershipForm2023-2024.pdf');
	$PDF->setWaiverfile('forms/E-waiver-FMCBC-Universal-Waiver-Basic-2022.pdf');
	$PDF->setWebformEmailField('emailaddress');
	$PDF->setPDFTKPath('');
	$PDF->setEmailPDFToMember(true);
	//$PDF->setEmailPDFToDirectors('somedirector@gmail.com');

	// ----------------------------------------------------------------------
	// Main TwoFactor Configuration Container Settings

    $TWOFA = new TwoFactor();
    $TWOFA->setEnabled(true);
	$TWOFA->setTotpKey('KFKF4334FFSFD');
	$TWOFA->setTotpTimeoutSeconds(60*45); // 45 minutes
	
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

