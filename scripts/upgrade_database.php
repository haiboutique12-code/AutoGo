<?php
if (PHP_SAPI!=='cli') {
    http_response_code(404);
    exit("Not found.\n");
}

require_once __DIR__.'/../app/core/Database.php';

$db=Database::connect();
$db->exec(
    'CREATE TABLE IF NOT EXISTS app_settings ('.
    'setting_key VARCHAR(100) NOT NULL PRIMARY KEY,'.
    'setting_value TEXT NOT NULL,'.
    'updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'.
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$columns=[
    'insurance_amount'=>'DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER total_price',
    'deposit_amount'=>'DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER insurance_amount',
    "payment_method"=>"ENUM('qr','cash') NOT NULL DEFAULT 'cash' AFTER deposit_amount",
    "payment_status"=>"ENUM('unpaid','awaiting_confirmation','paid','refunded') NOT NULL DEFAULT 'unpaid' AFTER payment_method",
    "payment_reference"=>'VARCHAR(100) NULL AFTER payment_status',
    "paid_at"=>'DATETIME NULL AFTER payment_reference',
    "deposit_status"=>"ENUM('not_collected','held','refunded') NOT NULL DEFAULT 'not_collected' AFTER paid_at"
];
$existing=$db->prepare(
    'SELECT COUNT(*) FROM information_schema.COLUMNS '.
    'WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?'
);

foreach ($columns as $name=>$definition) {
    $existing->execute(['bookings',$name]);
    if ((int)$existing->fetchColumn()===0) {
        $db->exec('ALTER TABLE bookings ADD COLUMN '.$name.' '.$definition);
        echo "Added bookings.$name\n";
    } else {
        echo "Already exists: bookings.$name\n";
    }
}

$index=$db->prepare(
    'SELECT COUNT(*) FROM information_schema.STATISTICS '.
    'WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND INDEX_NAME=?'
);
$index->execute(['bookings','idx_booking_payment_status']);
if ((int)$index->fetchColumn()===0) {
    $db->exec('ALTER TABLE bookings ADD INDEX idx_booking_payment_status (payment_status,paid_at)');
    echo "Added payment reporting index\n";
}

echo "AutoGo payment, insurance, and deposit database upgrade is complete.\n";
