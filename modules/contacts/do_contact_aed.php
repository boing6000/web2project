<?php
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$delete = (int) w2PgetParam($_POST, 'del', 0);

$contact = new CContact();
// TODO: I don't like this particular hack but it's better than using the raw POST within the class
$contact->_contact_methods = empty($_POST['contact_methods']) ? array() : $_POST['contact_methods'];

$controller = new w2p_Controllers_Base(
                    $contact, $delete, 'Contact', 'm=contacts', 'm=companies&a=addedit'
                  );

$AppUI = $controller->process($AppUI, $_POST);

if ($controller->success && !$delete) {
    $controller = __extract_from_contact_controller($controller);
}

$AppUI->redirect($controller->resultPath);
