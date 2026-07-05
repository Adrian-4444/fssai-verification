<?php
/**
 * PostgreSQL Database Initialization Script
 * Runs the schema and loads sample data
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    // Read and execute the PostgreSQL schema
    $schema = file_get_contents(__DIR__ . '/schema-postgresql.sql');
    
    // Split by semicolons and execute each statement
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        function($stmt) { return !empty($stmt); }
    );
    
    foreach ($statements as $statement) {
        $db->exec($statement);
        echo "✓ Executed statement\n";
    }
    
    // Verify tables were created
    $result = $db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
    $tableCount = $result->fetch()['count'];
    
    echo "\n✓ Database initialization successful!\n";
    echo "✓ Tables created: $tableCount\n";
    
    // Verify sample data
    $licenses = $db->query("SELECT COUNT(*) as count FROM valid_licenses")->fetch()['count'];
    echo "✓ Sample licenses loaded: $licenses\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
