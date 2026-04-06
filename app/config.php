<?php

define('APP_NAME', 'Gestão Financeira');
define('APP_VERSION', '1.0.0');

// Database
define('DB_PATH', dirname(__DIR__) . '/storage/gestao.db');

// Authentication — change this password hash to set your own password
// Default password: admin123
// To generate a new hash: php -r "echo password_hash('your_password', PASSWORD_BCRYPT);"
define('APP_USERNAME', 'admin');
define('APP_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // password: password

// Session
define('SESSION_NAME', 'gestao_session');

// Timezone
date_default_timezone_set('Europe/Lisbon');
