<?php
require_once __DIR__ . '/../src/Database.php';

class Seo {
    public static function get(PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM app_seo ORDER BY updated_at DESC, seo_id DESC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row && $row['meta_keywords']) {
            // meta_keywords stored as JSON
            $row['meta_keywords'] = json_decode($row['meta_keywords'], true);
        }
        return $row ?: null;
    }
}
