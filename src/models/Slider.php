<?php
require_once __DIR__ . '/../src/Database.php';

class Slider {
    public static function getAll(PDO $pdo, int $instance_id) {
        $stmt = $pdo->prepare('SELECT * FROM app_slider WHERE instance_id = ? ORDER BY display_order ASC, created_at ASC');
        $stmt->execute([$instance_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getActive(PDO $pdo, int $instance_id) {
        $stmt = $pdo->prepare('SELECT * FROM app_slider WHERE instance_id = ? AND is_active = 1 ORDER BY display_order ASC, created_at ASC');
        $stmt->execute([$instance_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function get(PDO $pdo, int $instance_id, int $id) {
        $stmt = $pdo->prepare('SELECT * FROM app_slider WHERE instance_id = ? AND slider_id = ? LIMIT 1');
        $stmt->execute([$instance_id, $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
