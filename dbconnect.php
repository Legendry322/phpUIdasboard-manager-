define('DB_HOST', 'localhost');
define('DB_NAME', 'uimanger');
define('DB_USER', 'root');
define('DB_PASS', ''); // Replace with your actual password
define('DB_CHARSET', 'utf8mb4');

// Connection Options
$options = [
    // Throw exceptions on errors
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Return records as associative arrays by default
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Disable emulated prepared statements for true SQL injection protection
    PDO::ATTR_EMULATE_PREPARES   => false,
    
    // Persistent connection option (optional, set to true if high traffic requires it)
    PDO::ATTR_PERSISTENT         => false,
];

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log detailed error for developers to inspect safely
    error_log("Database Connection Error: " . $e->getMessage());

    // Display a clean, generic message to end users (prevents information disclosure)
    http_response_code(500);
    die("Database connection error. Please try again later.");
}
