<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

final class TrainingRepository
{
    /** KPI tiles for the training page */
    public static function kpis(int $userId): array {
        $pdo = DB::pdo();

        // Courses assigned
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM training_assignments WHERE user_id = ?");
        $stmt->execute([$userId]);
        $coursesAssigned = (int)$stmt->fetchColumn();

        // Due this week (next 7 days), not completed
        $stmt = $pdo->prepare("
          SELECT COUNT(*)
          FROM training_assignments
          WHERE user_id = ?
            AND status <> 'Completed'
            AND due_at IS NOT NULL
            AND due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$userId]);
        $dueThisWeek = (int)$stmt->fetchColumn();

        // Completed
        $stmt = $pdo->prepare("
          SELECT COUNT(*) FROM training_assignments
          WHERE user_id = ? AND status = 'Completed'
        ");
        $stmt->execute([$userId]);
        $completed = (int)$stmt->fetchColumn();

        // Avg score across completed with non-null scores
        $stmt = $pdo->prepare("
          SELECT AVG(tp.score_pct)
          FROM training_progress tp
          JOIN training_assignments ta
            ON ta.course_id = tp.course_id AND ta.user_id = tp.user_id
          WHERE tp.user_id = ? AND ta.status = 'Completed' AND tp.score_pct IS NOT NULL
        ");
        $stmt->execute([$userId]);
        $avg = $stmt->fetchColumn();
        $avgScore = $avg !== null ? (string)round((float)$avg) . '%' : '—';

        return [
            ['label' => 'Courses Assigned', 'value' => $coursesAssigned],
            ['label' => 'Due This Week',    'value' => $dueThisWeek],
            ['label' => 'Completed',        'value' => $completed],
            ['label' => 'Avg. Score',       'value' => $avgScore],
        ];
    }

    /**
     * List assigned courses for a user (optionally with simple filters).
     * Returns rows in the view’s expected shape.
     */
    public static function coursesForUser(
        int $userId,
        ?string $q = null,
        ?string $category = null,
        ?string $status = null,
        string $sort = 'due_soon' // due_soon | recent | alpha
    ): array {
        $pdo = DB::pdo();

        $w = ['ta.user_id = ?'];
        $p = [$userId];

        if ($q)        { $w[] = '(c.title LIKE ? OR c.category LIKE ?)'; $p[]='%'.$q.'%'; $p[]='%'.$q.'%'; }
        if ($category) { $w[] = 'c.category = ?';   $p[]=$category; }
        if ($status)   { $w[] = 'ta.status = ?';    $p[]=$status; }

        $where = 'WHERE ' . implode(' AND ', $w);

        $order = match ($sort) {
            'alpha'  => 'c.title ASC, ta.assignment_id DESC',
            'recent' => 'ta.assigned_at DESC',
            default  => 'COALESCE(ta.due_at, "2999-12-31") ASC, ta.assignment_id DESC', // due soon first
        };

        $sql = "
          SELECT
            c.title,
            c.category,
            c.kind,
            ta.due_at,
            ta.status,
            COALESCE(tp.progress_pct,0)  AS progress_pct,
            tp.score_pct
          FROM training_assignments ta
          JOIN training_courses c ON c.course_id = ta.course_id
          LEFT JOIN training_progress tp
                 ON tp.course_id = ta.course_id AND tp.user_id = ta.user_id
          $where
          ORDER BY $order
          LIMIT 200
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($p);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(function(array $r): array {
            $dueStr = self::fmtDate($r['due_at'] ?? null);
            $status = (string)$r['status'];

            // derive Overdue if past due and not completed
            if ($status !== 'Completed' && !empty($r['due_at'])) {
                $t = strtotime((string)$r['due_at']);
                if ($t && $t < time()) {
                    $status = 'Overdue';
                }
            }

            return [
                'title'    => (string)$r['title'],
                'category' => (string)$r['category'],
                'due'      => $dueStr,
                'status'   => $status,
                'progress' => (int)$r['progress_pct'],
                'score'    => $r['score_pct'] !== null ? (int)$r['score_pct'] : null,
                'type'     => (string)$r['kind'],
            ];
        }, $rows);
    }

    /** Current/in-progress course (pick most recently opened or assigned) */
    public static function currentCourse(int $userId): ?array {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
          SELECT c.title, c.category, c.kind,
                 ta.due_at,
                 ta.status,
                 COALESCE(tp.progress_pct,0) AS progress_pct,
                 tp.last_opened_at
          FROM training_assignments ta
          JOIN training_courses c ON c.course_id = ta.course_id
          LEFT JOIN training_progress tp
                 ON tp.course_id = ta.course_id AND tp.user_id = ta.user_id
          WHERE ta.user_id = ? AND ta.status IN ('In Progress','Not Started')
          ORDER BY COALESCE(tp.last_opened_at, ta.assigned_at) DESC
          LIMIT 1
        ");
        $stmt->execute([$userId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$r) return null;

        $status = (string)$r['status'];
        if ($status !== 'Completed' && !empty($r['due_at'])) {
            $t = strtotime((string)$r['due_at']);
            if ($t && $t < time()) $status = 'Overdue';
        }

        return [
            'title'    => (string)$r['title'],
            'category' => (string)$r['category'],
            'type'     => (string)$r['kind'],
            'due'      => self::fmtDate($r['due_at'] ?? null),
            'status'   => $status,
            'progress' => (int)$r['progress_pct'],
        ];
    }

    /** Certificates grid */
    public static function certificatesForUser(int $userId): array {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
          SELECT c.title, tc.issued_at, tc.expires_at, tc.cert_code
          FROM training_certificates tc
          JOIN training_courses c ON c.course_id = tc.course_id
          WHERE tc.user_id = ?
          ORDER BY tc.issued_at DESC
          LIMIT 200
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(fn($r) => [
            'course'  => (string)$r['title'],
            'issued'  => self::fmtDate($r['issued_at'] ?? null),
            'expires' => self::fmtDate($r['expires_at'] ?? null),
            'id'      => (string)$r['cert_code'],
        ], $rows);
    }

    private static function fmtDate(?string $ts): string {
        if (!$ts) return '—';
        $t = strtotime((string)$ts);
        if (!$t) return '—';
        return date('M d, Y', $t);
    }
}
