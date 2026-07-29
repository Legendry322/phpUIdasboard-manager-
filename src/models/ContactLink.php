<?php
require_once __DIR__ . '/../src/Database.php';

class ContactLink {
    public static function getActive(PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM app_contact_link WHERE is_active = 1 ORDER BY display_order ASC, created_at ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
