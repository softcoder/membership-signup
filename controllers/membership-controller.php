<?php 
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;
 
define( 'INCLUSION_PERMITTED', true );

if(defined('__RIPRUNNER_ROOT__') === false) {
    define('__RIPRUNNER_ROOT__', dirname(dirname(__FILE__)));
}

require_once __RIPRUNNER_ROOT__ . '/template.php';
require_once __RIPRUNNER_ROOT__ . '/models/global-model.php';
require_once __RIPRUNNER_ROOT__ . '/models/membership-model.php';

$_SESSION['LOGIN_REFERRER'] = basename(__FILE__);
new MembershipViewModel($global_vm, $view_template_vars);

$view_form = 'membership-index';
// Check for routing action
$route_action = get_query_param('route_action');
if(isset($route_action) === true) {
    if($route_action === 'membership') {
        $view_form = 'membership-start';
    }
    else if($route_action === 'waiver') {
        $view_form = 'membership-waiver';
    }
}

// Load out template
$template = $twig->resolveTemplate(
		array("@custom/$view_form-custom.twig.html",
			  "$view_form.twig.html"));

// Output our template
echo $template->render($view_template_vars);
