<?php
declare(strict_types=1);

final class Audit
{
    public static function record(PDO $db, ?int $userId, string $action, string $entity, ?int $entityId = null): void
    {
        $stmt = $db->prepare(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, created_at)
             VALUES (:user_id, :action, :entity_type, :entity_id, :ip_address, :created_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entity,
            'entity_id' => $entityId,
            'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }
}
