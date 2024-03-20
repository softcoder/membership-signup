<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;

if(defined('__RIPRUNNER_ROOT__') === false) {
    define('__RIPRUNNER_ROOT__', dirname(__FILE__));
}

if(defined('INCLUSION_PERMITTED') === false) {
	define( 'INCLUSION_PERMITTED', true );
}

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __RIPRUNNER_ROOT__.'/config.php';
require __RIPRUNNER_ROOT__.'/vendor/autoload.php';
require_once __RIPRUNNER_ROOT__ . '/models/global-model.php';

require_once __RIPRUNNER_ROOT__ . '/template.php';
require_once __RIPRUNNER_ROOT__.'/functions.php';
require_once __RIPRUNNER_ROOT__.'/logging.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use \OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class ProcessRequest {
	
	private $request_variables;
	private $server_variables; 
	private $SITES;
	private $HEADERS_FUNC;
	private $PRINT_FUNC;
	private $GET_FILE_CONTENTS_FUNC;
	private $twigEnv;

	public function __construct($SITES,$request_variables=null,$server_variables=null,$hf=null,$pf=null,$gfcf=null) {
		$this->SITES = $SITES;
		$this->request_variables = $request_variables;
		$this->server_variables = $server_variables;
		$this->HEADERS_FUNC = $hf;
		$this->PRINT_FUNC = $pf;
		$this->GET_FILE_CONTENTS_FUNC = $gfcf;
    }

    public function setTwigEnv($twigEnv) {
        $this->twigEnv = $twigEnv;
    }

	private function header(string $header) {
		if($this->HEADERS_FUNC != null) {
			$cb = $this->HEADERS_FUNC;
			$cb($header);
		}
		else {
			header($header);
		}
	}

	private function print(string $text) {
		if($this->PRINT_FUNC != null) {
			$cb = $this->PRINT_FUNC;
			$cb($text);
		}
		else {
			print $text;
		}
	}

	private function file_get_contents(string $url) {
		if($this->GET_FILE_CONTENTS_FUNC != null) {
			$cb = $this->GET_FILE_CONTENTS_FUNC;
			return $cb($url);
		}
		else if(empty($this->request_variables)) {
			return file_get_contents($url);
		}
		return null;
	}

    private function stringEndsWith($string, $endsWith) {
        if(substr_compare($string, $endsWith, -strlen($endsWith)) === 0) {
            return true;
        } 
        else {
            return false;
        }
    }

    private function getFDFHeader() {
        $fdfHeader = "%FDF-1.2" . "\r\n";
        $fdfHeader .= "1 0 obj << /FDF << /Fields [" . "\r\n";
        return $fdfHeader;
    }

    private function getFDFFooter() {
        $fdfFooter = "] >> >>" . "\r\n";
        $fdfFooter .= "endobj" . "\r\n";
        $fdfFooter .= "trailer" . "\r\n";
        $fdfFooter .= "<</Root 1 0 R>>" . "\r\n";
        $fdfFooter .= "%%EOF";
        return $fdfFooter;
    }		

	public function execute() {
		global $log;
        global $global_vm;
        $gvm = $global_vm;
		
        $this->request_variables = $_POST;
        if(empty($this->request_variables)) {
            echo "<p>You've visited this page in error.</p>";
            exit();
        }

        $debugScript = false;
        if($debugScript) {
            // Debugging information, can be deleted
            echo "<h1>POST Data</h1>";
            echo "<div style='border-style: solid;'><pre>";
            print_r($this->request_variables);
            echo "</pre></div>";
        }
        
        // If two factor checking is enabled we will validate that we have a valid access code
        if($gvm->site->TWOFACTOR->ENABLED) {
            $this->processAcessCode($gvm);
        }

        // Configuration
        // Set location for FDF and PDF files
        $outputLocation = $gvm->site->PDFSettings->OUTPUTPATH;
        // Location of original PDF form
        $pdfLocation    = $gvm->site->PDFSettings->MEMBERSHIP_FILE;
        // Location of original PDF form email view template
        $pdfViewLocation    = $gvm->site->PDFSettings->MEMBERSHIP_FILE_EMAIL_VIEW_TEMPLATE;
        
        $iswaiver = getSafeRequestValue('waiver');
        if(isset($iswaiver)) {
          $pdfLocation = $gvm->site->PDFSettings->WAIVER_FILE;
          $pdfViewLocation = $gvm->site->PDFSettings->WAIVER_FILE_EMAIL_VIEW_TEMPLATE;
        }

        $member_email = $this->getMemberEmail($gvm);
        $fdf          = $this->getFDF();

        if($debugScript) {
            // Debugging information, can be deleted
            echo "<h1>FDF Data</h1>";
            echo "<div style='border-style: solid;'><pre>";
            print_r(htmlspecialchars($fdf));
            echo "</pre></div>";
        }

        // Dump FDF data to file
        //$timestamp = time();
        $timestamp = uniqid(mt_rand(), true);
        $outputFDF = $outputLocation . $timestamp . ".fdf";
        $outputPDF = $outputLocation . $timestamp . ".pdf";
        file_put_contents($outputFDF, $fdf);

        $pdftkPath = $gvm->site->PDFSettings->PDFTK_PATH;
        $pdftkCmd  = $pdftkPath . "pdftk " . $pdfLocation . " fill_form " . $outputFDF . " output " . $outputPDF;
        
        if ($log !== null) $log->trace("PDFTK cmd: ".$pdftkCmd);
        
        // Create the filled out PDF form
        $console_output = null;
        $retval         = null;
        exec($pdftkCmd, $console_output, $retval);
        
        if($retval !== 0) {
            $output_string = implode(PHP_EOL, $console_output);
            if ($log !== null) $log->error("PDFTK cmd: [$pdftkCmd] failed [$retval] with: $output_string");
        }

        if(isset($iswaiver)) {
            echo "<p>Done! Your waiver application will be reviewed shortly.</p>";
        }
        else {
            echo "<p>Done! Your membership application will be reviewed shortly.</p>";
        }
        echo "<a href='".$gvm->RR_DOC_ROOT."/'>Main Index</a>";
        
        echo "<iframe src='$outputPDF' width='100%' height='100%'></iframe>";
        
        if($gvm->site->PDFSettings->EMAILPDF_TO_MEMBER == true || 
           empty($gvm->site->PDFSettings->EMAILPDF_TO_DIRECTORS) == false) {

            $this->emailPDFToUsers($gvm, $member_email, $pdfLocation, $pdfViewLocation, $outputPDF);
        }
	}

    private function emailPDFToUsers($gvm, $member_email, $inputPDF, $inputPDFView, $outputPDF) {
       
       // Email the message to users
       $subject = $this->getFormEmailSubject($inputPDF, $inputPDFView);
       $msg = $this->getFormEmailMsg($gvm,$inputPDF, $inputPDFView);
       
       $users = array();
       if($gvm->site->PDFSettings->EMAILPDF_TO_MEMBER) {
           array_push($users,$member_email);
       }
       if(empty($gvm->site->PDFSettings->EMAILPDF_TO_DIRECTORS) == false) {
           $directors = preg_split ("/\,/", $gvm->site->PDFSettings->EMAILPDF_TO_DIRECTORS); 
           $users = array_merge($users,$directors);
       }
       $attachment   = $outputPDF;
       $notifyResult = $this->sendEmailMessage($attachment, $msg, $subject, $users, $gvm);

       if ($notifyResult == false) {
          echo "<p>Error attempting to send email to the email address [$member_email]</p>";

          exit();
       }
       else {
         echo "<p>Success sending email to the email address [$member_email]</p>";
       }        
    }
    
    private function getFDF() {
        // Loop through the $_POST data, creating a new row in the FDF file for each key/value pair
        $fdf = "";
        foreach($this->request_variables as $key => $value) {
            // If the user filled nothing in the field, like a text field, just skip it.
            // Note that if the PDF you provide already has text in it by default, doing this will leave the text as-is.
            // If you prefer to remove the text, you should remove the lines below so you overwrite the text with nothing.
            if($value == "") {
                continue;
            }

            // Figure out what kind of field it is by its name, which should be in the format name_fieldtype.

            // Textbox
            if($this->stringEndsWith($key, "_textbox")) {
                $key = str_replace("_textbox", "", $key);
                // Format:
                // << /V (Text) /T (Fieldname) >> 

                // Backslashes in the value are encoded as double backslashes
                $value = str_replace("\\", "\\\\", $value);
                // Parenthesis are encoded using \'s in front
                $value = str_replace("(", "\(", $value);
                $value = str_replace(")", "\)", $value);

                $fdf .= "<< /V (" . $value . ")" . " /T (" . $key . ") >>" . "\r\n";
            }

            // Checkbox
           else if($this->stringEndsWith($key, "_checkbox")) {
                $key = str_replace("_checkbox", "", $key);
                // Format:
                // << /V /On /T (Fieldname) >>

                // If the data was present in $_POST, that's because it was checked, so we can hardcode "/Yes" here
                $fdf .= "<< /V /Yes /T (" . $key . ") >>" . "\r\n";
            }

            // Radio Button
            else if($this->stringEndsWith($key, "_radio")) {
                $key = str_replace("_radio", "", $key);
                // Format:
                // << /V /Test#20Value /T (Fieldname) >>

                // Spaces are encoded as #20
                $value = str_replace(" ", "#20", $value);
                
                $fdf .= "<< /V /" . $value . " /T (" . $key . ") >>" . "\r\n";
            }

            // Dropdown
            else if($this->stringEndsWith($key, "_dropdown")) {
                $key = str_replace("_dropdown", "", $key);
                // Format:
                // << /V (Option 2) /T (Dropdown) >>

                $fdf .= "<< /V (" . $value . ") /T (" . $key . ") >>" . "\r\n";
            }
            
            // Unknown type
            else {
                echo "ERROR: We don't know what field type " . $key . " is, so we can't put it into the FDF file!";
            }
        }

        // Include the header and footer, then write the FDF data to a file
        $fdf = $this->getFDFHeader() . $fdf . $this->getFDFFooter();
        return $fdf;
    }
        
    private function getMemberEmail($gvm) {
        $member_email       = '';
        $isaccesscode_req   = getSafeRequestValue('accesscode');
        if(isset($isaccesscode_req)) {
            $member_email = $this->request_variables['emailaccess'];
        }
        else {
            $member_email = $this->request_variables[$gvm->site->PDFSettings->WEBFORM_EMAILFIELD.'_textbox'];
        }
        
        return $member_email;
    }
    	
	private function processAcessCode($gvm) {
        global $log;
        
        $isaccesscode_req   = getSafeRequestValue('accesscode');
        $accesscode_seconds = $gvm->site->TWOFACTOR->TOTP_TIMEOUT_SECONDS;
        $accesscode_key     = $gvm->site->TWOFACTOR->TOTP_KEY;
        $member_email       = $this->getMemberEmail($gvm);
        
        $accesscode_key .= Base32::encodeUpper($member_email);
        $accesscode_key  = trim(mb_strtoupper($accesscode_key), '=');
        
        if ($log !== null) $log->trace("Email access code to user: [$member_email] PHP version [".PHP_VERSION_ID."] TOTP timeout [$accesscode_seconds] TOTP secret [$accesscode_key]");
        
        $otp = TOTP::create($accesscode_key, $accesscode_seconds);
        
        if(isset($isaccesscode_req)) {
            
   		    $twofaKey = $otp->now();
   		    
   		    $accesscode_minutes = $accesscode_seconds/60;
            $subject = $this->getAccessCodeEmailSubject($twofaKey,$member_email,$accesscode_minutes);
            $msg = $this->getAccessCodeEmailMsg($twofaKey,$member_email,$accesscode_minutes);

            $users = array();
            array_push($users,$member_email);

            // Email the message to users
            $notifyResult = $this->sendEmailMessage(null, $msg, $subject, $users, $gvm);
            
           if ($notifyResult == false) {
              if ($log !== null) $log->error("ERROR trying to notify user with access code: $twofaKey");

              $errmsg = "Error attempting to send email to the email address [$member_email] check application logs.";
              header("HTTP/1.1 500 Internal Server Error");
              echo $errmsg;
           }
           else {
             if ($log !== null) $log->trace("Notified user with access code: $twofaKey");
             echo "Success sending email to the email address [$member_email]";
           }        

           exit();
        }

   	    $twofaKey = $this->request_variables['accesscode_textbox'];
        $valid2FA = $otp->verify($twofaKey,null,$accesscode_seconds-1);
        if($valid2FA == false) {
            echo "<p>You entered an invalid access code [$twofaKey] for email address [$member_email]</p>";
            if ($log !== null) $log->error("Notified user with INVALID access code: [$twofaKey] for email address [$member_email]");
            
            exit();
        }
	}
	
	private function getTwigEnv() {
	    global $twig;
	    if($this->twigEnv != null) {
	        return	$this->twigEnv;
	    }
	    return $twig;
	}
	
	public function getAccessCodeEmailSubject($twofaKey,$member_email,$accesscode_minutes) {
		global $log;

		$view_template_vars = array();
		$view_template_vars['twofaKey'] = $twofaKey;
		$view_template_vars['member_email'] = $member_email;
		$view_template_vars['accesscode_minutes'] = $accesscode_minutes;

		$view_templates = array();
		array_push($view_templates, '@custom/access-code-email-subject-msg-custom.twig.html');
		array_push($view_templates, 'access-code-email-subject-msg.twig.html');
		
		// Load our template
		$template = $this->getTwigEnv()->resolveTemplate($view_templates);
		// Output our template
		$msg = $template->render($view_template_vars);
		
		if($log != null) $log->trace("Access Code Email subject msg [$msg]");
		
		return $msg;
	}

	public function getAccessCodeEmailMsg($twofaKey,$member_email,$accesscode_minutes) {
		global $log;

		$view_template_vars = array();
		$view_template_vars['twofaKey'] = $twofaKey;
		$view_template_vars['member_email'] = $member_email;
		$view_template_vars['accesscode_minutes'] = $accesscode_minutes;

		$view_templates = array();
		array_push($view_templates, '@custom/access-code-email-msg-custom.twig.html');
		array_push($view_templates, 'access-code-email-msg.twig.html');
		
		// Load our template
		$template = $this->getTwigEnv()->resolveTemplate($view_templates);
		// Output our template
		$msg = $template->render($view_template_vars);
		
		if($log != null) $log->trace("Access Code Email msg [$msg]");
		
		return $msg;
	}

	public function getFormEmailSubject($inputPDF, $inputPDFView) {
		global $log;

		$view_template_vars = array();
		$view_template_vars['pdf'] = basename($inputPDF);

		$view_templates = array();
		array_push($view_templates, '@custom/pdf-form-email-subject-msg-'.basename($inputPDFView).'-custom.twig.html');
		array_push($view_templates, 'pdf-form-email-subject-msg-'.basename($inputPDFView).'.twig.html');
		
		// Load our template
		$template = $this->getTwigEnv()->resolveTemplate($view_templates);
		// Output our template
		$msg = $template->render($view_template_vars);
		
		if($log != null) $log->trace("PDF Form [$inputPDF] Email subject msg [$msg]");
		
		return $msg;
	}

	public function getFormEmailMsg($gvm, $inputPDF, $inputPDFView) {
		global $log;

		$view_template_vars = array();
		$view_template_vars['pdf'] = basename($inputPDF);
		$view_template_vars['membershipdirectors'] = $gvm->site->PDFSettings->EMAILPDF_TO_DIRECTORS;

		$view_templates = array();
		array_push($view_templates, '@custom/pdf-form-email-msg-'.basename($inputPDFView).'-custom.twig.html');
		array_push($view_templates, 'pdf-form-email-msg-'.basename($inputPDFView).'.twig.html');
		
		// Load our template
		$template = $this->getTwigEnv()->resolveTemplate($view_templates);
		// Output our template
		$msg = $template->render($view_template_vars);
		
		if($log != null) $log->trace("PDF Form [$inputPDF] Email msg [$msg]");
		
		return $msg;
	}

    private function sendEmailMessage($attachment, $msg, $subject, $users, $gvm) {
    	global $log;

        if($gvm->site->EMAIL->ENABLE_OUTBOUND_SMTP == true ||
           $gvm->site->EMAIL->ENABLE_OUTBOUND_SENDMAIL == true) {
            
            if($log !== null) $log->trace("email users: ".print_r($users,TRUE));
            $mail = new PHPMailer;
            
            foreach($users as $email) {
              $mail->addAddress($email, $email);
            }
            
            //$mail->SMTPDebug = 3; Enable verbose debug output
            //$mail->SMTPDebug = 2;
            //$mail->Debugoutput = 'html';
            
            if($gvm->site->EMAIL->ENABLE_OUTBOUND_SMTP == true) {
                $mail->isSMTP();
            }
            else if($gvm->site->EMAIL->ENABLE_OUTBOUND_SENDMAIL == true) {
                $mail->isSendmail();
            }
            
            //Set the hostname of the mail server
            $mail->Host = $gvm->site->EMAIL->OUTBOUND_HOST;
            // use
            // $mail->Host = gethostbyname('smtp.gmail.com');
            // if your network does not support SMTP over IPv6
            //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
            $mail->Port = $gvm->site->EMAIL->OUTBOUND_PORT;
            //Set the encryption system to use - ssl (deprecated) or tls
            $mail->SMTPSecure = $gvm->site->EMAIL->OUTBOUND_ENCRYPT;
            //Whether to use SMTP authentication
            $mail->SMTPAuth = $gvm->site->EMAIL->OUTBOUND_AUTH;
            //Username to use for SMTP authentication - use full email address for gmail
            $mail->Username = $gvm->site->EMAIL->OUTBOUND_USERNAME;
            //Password to use for SMTP authentication
            $mail->Password = $gvm->site->EMAIL->OUTBOUND_PASSWORD;
            //Set who the message is to be sent from
            $mail->setFrom($gvm->site->EMAIL->OUTBOUND_FROM_ADDRESS, $gvm->site->EMAIL->OUTBOUND_FROM_NAME);
            //Set an alternative reply-to address
            $mail->addReplyTo($gvm->site->EMAIL->OUTBOUND_FROM_ADDRESS, $gvm->site->EMAIL->OUTBOUND_FROM_NAME);
            //Set the subject line
            $mail->Subject = $subject;
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->msgHTML(nl2br($msg));
            //Replace the plain text body with one created manually
            //$mail->Body = $emailMsg;
            $mail->AltBody = $msg;
            
            if($attachment != null) {
              $mail->addAttachment($attachment);
            }

            //send the message, check for errors            
            if (!$mail->send()) {
                $sendMsgResultStatus = "Error sending Email Message: " . $mail->ErrorInfo;
                if ($log !== null) $log->error("Notified user of account status: ".print_r($sendMsgResultStatus, true));
                return false;
            }
            else {
                $sendMsgResultStatus = "Email Message sent to applicable recipients.";
                if ($log !== null) $log->trace("Notified user of account status: ".print_r($sendMsgResultStatus, true));
                return true;
            }
       }
       if ($log !== null) $log->error("Email is disabled in the configuration!");
       return false;
    }
	
}
