<?php
require_once __DIR__ . '/../src/Database.php';

class Seo {
    // If instance_id provided, get specific, else get latest
    public static function get(PDO $pdo, ?int $instance_id = null) {
        if ($instance_id) {
            $stmt = $pdo->prepare('SELECT * FROM app_seo WHERE instance_id = ? ORDER BY updated_at DESC, seo_id DESC LIMIT 1');
            $stmt->execute([$instance_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query('SELECT * FROM app_seo ORDER BY updated_at DESC, seo_id DESC LIMIT 1');
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if ($row && $row['meta_keywords']) {
            $decoded = json_decode($row['meta_keywords'], true);
            if (json_last_error() === JSON_ERROR_NONE) $row['meta_keywords'] = $decoded;
        }
        return $row ?: null;
    }
}
