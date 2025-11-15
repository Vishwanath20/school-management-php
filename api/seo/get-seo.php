<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('SEO ID is required');
    }

    $stmt = $pdo->prepare("SELECT * FROM seo_settings WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $seo = $stmt->fetch();

    if ($seo) {
        echo json_encode([
            'success' => true,
            'data' => $seo
        ]);
    } else {
        throw new Exception('SEO settings not found');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}