<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

final class UserRepository
{
    /** Fetch merged profile/settings for one user */
    public static function profile(int $userId): array {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT
              u.user_id, u.name, u.role, u.email, u.avatar, u.password_hash,
              us.phone, us.position_title, us.location_text, us.timezone_id,
              us.language_label, us.two_factor,
              us.notif_email, us.notif_tasks, us.notif_schedule,
              us.notif_training, us.notif_documents
            FROM users u
            LEFT JOIN user_settings us ON us.user_id = u.user_id
            WHERE u.user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // sensible defaults when settings row doesn't exist yet
        return [
            'user_id'   => (int)($r['user_id'] ?? $userId),
            'name'      => (string)($r['name'] ?? ''),
            'role'      => (string)($r['role'] ?? 'Staff'),
            'email'     => (string)($r['email'] ?? ''),
            'avatar'    => (string)($r['avatar'] ?? 'default-avatar.png'),
            'phone'     => (string)($r['phone'] ?? ''),
            'position'  => (string)($r['position_title'] ?? ''),
            'location'  => (string)($r['location_text'] ?? ''),
            'timezone'  => (string)($r['timezone_id'] ?? 'UTC'),
            'language'  => (string)($r['language_label'] ?? 'English'),
            'two_factor'=> (bool)($r['two_factor'] ?? 0),
            'notifications' => [
                'email'     => (bool)($r['notif_email'] ?? 1),
                'tasks'     => (bool)($r['notif_tasks'] ?? 1),
                'schedule'  => (bool)($r['notif_schedule'] ?? 1),
                'training'  => (bool)($r['notif_training'] ?? 1),
                'documents' => (bool)($r['notif_documents'] ?? 1),
            ],
        ];
    }

    /** Create/update settings and basic profile fields */
    public static function saveProfile(int $userId, array $data): void {
        $pdo = DB::pdo();
        $pdo->beginTransaction();
        try {
            // Update simple fields on users table
            $stmt = $pdo->prepare("UPDATE users SET name = ?, avatar = COALESCE(?, avatar) WHERE user_id = ?");
            $stmt->execute([$data['name'], $data['avatar'] ?? null, $userId]);

            // Upsert settings row
            $stmt = $pdo->prepare("
                INSERT INTO user_settings
                  (user_id, phone, position_title, location_text, timezone_id,
                   language_label, two_factor, notif_email, notif_tasks,
                   notif_schedule, notif_training, notif_documents)
                VALUES
                  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                  phone = VALUES(phone),
                  position_title = VALUES(position_title),
                  location_text  = VALUES(location_text),
                  timezone_id    = VALUES(timezone_id),
                  language_label = VALUES(language_label),
                  two_factor     = VALUES(two_factor),
                  notif_email    = VALUES(notif_email),
                  notif_tasks    = VALUES(notif_tasks),
                  notif_schedule = VALUES(notif_schedule),
                  notif_training = VALUES(notif_training),
                  notif_documents= VALUES(notif_documents)
            ");
            $stmt->execute([
                $userId,
                $data['phone'] ?? null,
                $data['position'] ?? null,
                $data['location'] ?? null,
                $data['timezone'] ?? null,
                $data['language'] ?? 'English',
                !empty($data['two_factor']) ? 1 : 0,
                !empty($data['notif_email']) ? 1 : 0,
                !empty($data['notif_tasks']) ? 1 : 0,
                !empty($data['notif_schedule']) ? 1 : 0,
                !empty($data['notif_training']) ? 1 : 0,
                !empty($data['notif_documents']) ? 1 : 0,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** Verify current password (if set) and update to a new value */
    public static function changePassword(int $userId, string $current, string $new, string $confirm): array {
        if ($new === '' || $confirm === '') {
            return [false, 'New password and confirmation are required.'];
        }
        if ($new !== $confirm) {
            return [false, 'New password and confirmation do not match.'];
        }
        if (strlen($new) < 8) {
            return [false, 'Password must be at least 8 characters.'];
        }

        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $hash = (string)$stmt->fetchColumn();

        // If a hash exists, verify; if none exists yet, allow setting.
        if ($hash && !password_verify($current, $hash)) {
            return [false, 'Current password is incorrect.'];
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $up = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $up->execute([$newHash, $userId]);

        return [true, 'Password updated successfully.'];
    }
}
