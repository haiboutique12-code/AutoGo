<?php
if (PHP_SAPI!=='cli') {
    http_response_code(404);
    exit("Not found.\n");
}

require_once __DIR__.'/../app/core/Database.php';

$email=strtolower(trim($argv[1]??''));
if (!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>160) {
    fwrite(STDERR,"Usage: php scripts/promote_admin.php existing-user@example.com\n");
    exit(2);
}

$db=Database::connect();
$find=$db->prepare('SELECT id,name,role FROM users WHERE email=? LIMIT 1');
$find->execute([$email]);
$user=$find->fetch();
if (!$user) {
    fwrite(STDERR,"No registered user found for that email. Register the account first.\n");
    exit(1);
}
if ($user['role']==='admin') {
    echo "This account already has admin access.\n";
    exit(0);
}

echo "Grant administrator access to {$user['name']} ({$email})? Type YES to confirm: ";
$confirmation=trim((string)fgets(STDIN));
if ($confirmation!=='YES') {
    echo "Cancelled; no account was changed.\n";
    exit(0);
}

$update=$db->prepare("UPDATE users SET role='admin' WHERE id=? AND role='customer'");
$update->execute([$user['id']]);
if ($update->rowCount()!==1) {
    fwrite(STDERR,"The account could not be updated. Please check its role and try again.\n");
    exit(1);
}

echo "Administrator access granted. Sign in again to activate the admin menu.\n";
