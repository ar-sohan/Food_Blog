<?php
// ajax endpoint: live if the email available or not  check on the signup form.
// Returns json :available: bool, valid: bool.

header('Content-Type: application/json');
require_once('../model/userModel.php');

$email = trim($_REQUEST['email'] ?? '');

if ($email === '') {
    echo json_encode(['available' => false, 'valid' => false]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'valid' => false]);
    exit;
}

echo json_encode([
    'available' => !emailExists($email),
    'valid' => true,
]);
?>