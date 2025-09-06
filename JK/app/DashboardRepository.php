<?php
declare(strict_types=1);
require_once __DIR__.'/db.php';

final class DashboardRepository {

  public static function kpis(int $userId): array {
    $pdo = DB::pdo();

    // Open Tasks --------------- FIX: don't cast the statement to int
    $stmt = $pdo->prepare("
      SELECT COUNT(*)
      FROM tasks t
      JOIN task_assignments a ON a.task_id = t.task_id
      WHERE a.user_id = ? AND t.status IN ('Open','In Progress','Blocked')
    ");
    $stmt->execute([$userId]);
    $openTasksCount = (int)$stmt->fetchColumn();

    // New Announcements (last 7 days) not read
    $stmt = $pdo->prepare("
      SELECT COUNT(*)
      FROM announcements an
      LEFT JOIN announcement_reads r
        ON r.announcement_id = an.announcement_id AND r.user_id = ?
      WHERE an.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        AND r.id IS NULL
    ");
    $stmt->execute([$userId]);
    $newAnnouncements = (int)$stmt->fetchColumn();

    // Upcoming Shifts (next 14 days)
    $stmt = $pdo->prepare("
      SELECT COUNT(*)
      FROM shifts s
      JOIN shift_assignments sa ON sa.shift_id = s.shift_id
      WHERE sa.user_id = ?
        AND s.starts_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 14 DAY)
    ");
    $stmt->execute([$userId]);
    $upcomingShifts = (int)$stmt->fetchColumn();

    // Pending Reports (owned by user)
    $stmt = $pdo->prepare("
      SELECT COUNT(*)
      FROM reports
      WHERE owner_user_id = ? AND status = 'Pending'
    ");
    $stmt->execute([$userId]);
    $pendingReports = (int)$stmt->fetchColumn();

    return [
      ['label' => 'Open Tasks',        'value' => $openTasksCount,  'icon' => '🧹'],
      ['label' => 'New Announcements', 'value' => $newAnnouncements,'icon' => '📣'],
      ['label' => 'Upcoming Shifts',   'value' => $upcomingShifts,  'icon' => '🗓️'],
      ['label' => 'Pending Reports',   'value' => $pendingReports,  'icon' => '📄'],
    ];
  }

  public static function tasksForUser(int $userId, int $limit = 10): array {
    $pdo = DB::pdo();
    $stmt = $pdo->prepare("
      SELECT t.task_id, t.title, t.due_at,
             COALESCE(tp.progress_pct, 0) AS progress
      FROM tasks t
      JOIN task_assignments a ON a.task_id = t.task_id
      LEFT JOIN task_progress tp ON tp.task_id = t.task_id AND tp.user_id = a.user_id
      WHERE a.user_id = ?
      ORDER BY COALESCE(t.due_at, '2999-12-31') ASC, t.task_id DESC
      LIMIT ?
    ");
    // TIP: bind LIMIT as integer
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public static function recentActivity(int $userId, int $limit = 10): array {
    $pdo = DB::pdo();
    $stmt = $pdo->prepare("
      SELECT DATE_FORMAT(occurred_at, '%b %e, %Y') AS date_fmt,
             type, detail, COALESCE(status,'') AS status
      FROM user_activity
      WHERE user_id = ?
      ORDER BY occurred_at DESC
      LIMIT ?
    ");
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  // Mix of messages & announcements preview, newest first
  public static function messagePreview(int $userId, int $limit = 5): array {
    $pdo = DB::pdo();

    // Announcements
    $ann = $pdo->query("
      SELECT a.announcement_id AS id,
             a.subject AS title,
             LEFT(a.body, 120) AS snippet,
             a.created_at AS created_at,
             'announcement' AS kind
      FROM announcements a
      ORDER BY a.created_at DESC
      LIMIT 10
    ")->fetchAll();

    // Messages (optional): ignore if table doesn't exist
    $msg = [];
    try {
      $msg = $pdo->query("
        SELECT m.message_id AS id,
               m.subject AS title,
               LEFT(m.body, 120) AS snippet,
               m.created_at AS created_at,
               'message' AS kind
        FROM messages m
        ORDER BY m.created_at DESC
        LIMIT 10
      ")->fetchAll();
    } catch (Throwable $e) {
      // table may be absent – that's okay for now
    }

    $mix = array_merge($ann, $msg);
    usort($mix, fn($x, $y) => strcmp($y['created_at'], $x['created_at']));
    $mix = array_slice($mix, 0, $limit);

    return array_map(function($r){
      return [
        'title'   => (string)$r['title'],
        'snippet' => (string)$r['snippet'],
        'time'    => self::timeAgo((string)$r['created_at']),
      ];
    }, $mix);
  }

  public static function upcomingShifts(int $userId, int $limit = 5): array {
    $pdo = DB::pdo();
    $stmt = $pdo->prepare("
      SELECT s.starts_at, s.ends_at, s.location, s.role_label
      FROM shifts s
      JOIN shift_assignments sa ON sa.shift_id = s.shift_id
      WHERE sa.user_id = ?
        AND s.starts_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
      ORDER BY s.starts_at ASC
      LIMIT ?
    ");
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    return array_map(function($r){
      $start = strtotime((string)$r['starts_at']);
      $end   = strtotime((string)$r['ends_at']);
      $when = date('D, g:i A', $start) . ' – ' . date('g:i A', $end);
      return [
        'when' => $when,
        'loc'  => (string)$r['location'],
        'role' => (string)$r['role_label'],
      ];
    }, $rows);
  }

  private static function timeAgo(string $ts): string {
    $t = strtotime($ts);
    $d = time() - $t;
    if ($d < 3600)        return floor($d / 60).'m ago';
    if ($d < 86400)       return floor($d / 3600).'h ago';
    if ($d < 7 * 86400)   return floor($d / 86400).'d ago';
    return date('M j', $t);
  }
}
