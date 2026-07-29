<?php
require_once __DIR__ . '/../src/Database.php';

class Theme {
    public static function getCurrent(PDO $pdo) {
        $stmt = $pdo->prepare('SELECT * FROM app_theme ORDER BY updated_at DESC, theme_id DESC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
