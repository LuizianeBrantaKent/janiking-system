<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

final class AnnouncementRepository {

  public static function create(
    int $authorUserId,
    string $subject,
    string $body,
    array $targets,           // e.g. ['ALL'] or ['GROUP' => [1,3]]  (ignored unless you add the targets table)
    array $attachments = []   // each: ['original_name','file_size','server_path', ('mime_type' optional)]
  ): int {
    $pdo = DB::pdo();
    $pdo->beginTransaction();
    try {
      // match schema: announcements(author_id, subject, body)
      $stmt = $pdo->prepare(
        'INSERT INTO announcements (author_id, subject, body) VALUES (?, ?, ?)'
      );
      $stmt->execute([$authorUserId, $subject, $body]);
      $announcementId = (int)$pdo->lastInsertId();

      // (Optional) Targets: skip unless you add the table in your schema
      // If you want it, create:
      // CREATE TABLE announcement_targets (
      //   id INT AUTO_INCREMENT PRIMARY KEY,
      //   announcement_id INT NOT NULL,
      //   target_type ENUM("ALL","GROUP") NOT NULL,
      //   group_id INT NULL,
      //   FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id) ON DELETE CASCADE
      // );
      // Then uncomment below.
      /*
      if (in_array('ALL', $targets, true)) {
        $pdo->prepare('INSERT INTO announcement_targets (announcement_id, target_type) VALUES (?, "ALL")')
            ->execute([$announcementId]);
      }
      if (!empty($targets['GROUP']) && is_array($targets['GROUP'])) {
        $stmtG = $pdo->prepare('INSERT INTO announcement_targets (announcement_id, target_type, group_id) VALUES (?, "GROUP", ?)');
        foreach ($targets['GROUP'] as $gid) {
          $stmtG->execute([$announcementId, (int)$gid]);
        }
      }
      */

      // Attachments: match schema columns (no mime_type column, PK is `id`)
      if ($attachments) {
        $stmtA = $pdo->prepare(
          'INSERT INTO announcement_attachments (announcement_id, original_name, file_size, server_path) VALUES (?, ?, ?, ?)'
        );
        foreach ($attachments as $f) {
          $stmtA->execute([
            $announcementId,
            $f['original_name'] ?? '',
            (int)($f['file_size'] ?? 0),
            $f['server_path'] ?? '',
          ]);
        }
      }

      $pdo->commit();
      return $announcementId;
    } catch (Throwable $e) {
      $pdo->rollBack();
      throw $e;
    }
  }

  public static function listRecent(?string $search = null, int $limit = 20): array {
    $pdo = DB::pdo();
    $limit = max(1, (int)$limit);

    if ($search !== null && $search !== '') {
      $sql = '
        SELECT announcement_id, subject, LEFT(body, 160) AS snippet, created_at
        FROM announcements
        WHERE subject LIKE :q OR body LIKE :q
        ORDER BY created_at DESC
        LIMIT ' . $limit;
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':q' => '%'.$search.'%']);
    } else {
      $sql = '
        SELECT announcement_id, subject, LEFT(body,160) AS snippet, created_at
        FROM announcements
        ORDER BY created_at DESC
        LIMIT ' . $limit;
      $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
  }

  public static function find(int $id): ?array {
    $pdo = DB::pdo();
    // match schema: a.author_id; users key is user_id
    $stmt = $pdo->prepare('
      SELECT a.*, u.name AS author_name, u.role AS author_role
      FROM announcements a
      JOIN users u ON u.user_id = a.author_id
      WHERE a.announcement_id = ?
    ');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) return null;

    // attachments: PK is `id`
    $att = $pdo->prepare('
      SELECT id, announcement_id, original_name, server_path, file_size, created_at
      FROM announcement_attachments
      WHERE announcement_id = ?
      ORDER BY id ASC
    ');
    $att->execute([$id]);
    $row['attachments'] = $att->fetchAll();

    // replies: author_id (not author_user_id)
    $re = $pdo->prepare('
      SELECT r.*, u.name AS author_name, u.role AS author_role
      FROM announcement_replies r
      JOIN users u ON u.user_id = r.author_id
      WHERE r.announcement_id = ?
      ORDER BY r.created_at ASC
    ');
    $re->execute([$id]);
    $row['replies'] = $re->fetchAll();

    return $row;
  }

  public static function addReply(int $announcementId, int $authorUserId, string $body): int {
    $pdo = DB::pdo();
    // match schema: announcement_replies(author_id)
    $stmt = $pdo->prepare(
      'INSERT INTO announcement_replies (announcement_id, author_id, body) VALUES (?, ?, ?)'
    );
    $stmt->execute([$announcementId, $authorUserId, $body]);
    return (int)$pdo->lastInsertId();
  }
}
