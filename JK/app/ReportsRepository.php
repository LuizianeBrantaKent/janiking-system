<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

final class ReportsRepository
{
    /**
     * Search reports with optional free-text and date-range filters.
     * Returns: ['rows' => array<report>, 'total' => int]
     *
     * NOTE: Schema provides `reports(title, owner_user_id, status, created_at)`.
     * We derive "format" from the report title if it contains a known extension.
     */
    public static function search(
        ?string $q,
        ?string $dateFrom,   // 'YYYY-MM-DD' or null
        ?string $dateTo,     // 'YYYY-MM-DD' or null
        int $page = 1,
        int $perPage = 10
    ): array {
        $pdo = DB::pdo();

        $w = [];
        $p = [];

        if ($q !== null && $q !== '') {
            $w[] = 'r.title LIKE ?';
            $p[] = '%' . $q . '%';
        }
        if ($dateFrom) {
            $w[] = 'r.created_at >= ?';
            $p[] = $dateFrom . ' 00:00:00';
        }
        if ($dateTo) {
            $w[] = 'r.created_at <= ?';
            $p[] = $dateTo . ' 23:59:59';
        }

        $where = $w ? ('WHERE ' . implode(' AND ', $w)) : '';

        $page    = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset  = ($page - 1) * $perPage;

        // total
        $sqlCount = "SELECT COUNT(*) FROM reports r $where";
        $stmt = $pdo->prepare($sqlCount);
        $stmt->execute($p);
        $total = (int)$stmt->fetchColumn();

        // rows
        $sql = "
          SELECT
            r.report_id,
            r.title,
            r.created_at,
            r.status,
            u.name AS owner_name
          FROM reports r
          JOIN users u ON u.user_id = r.owner_user_id
          $where
          ORDER BY r.created_at DESC, r.report_id DESC
          LIMIT $perPage OFFSET $offset
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($p);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $mapped = array_map(function(array $r): array {
            return [
                'report_id'  => (int)$r['report_id'],
                'name'       => (string)$r['title'],
                'created_on' => self::fmtDate($r['created_at'] ?? null),
                'created_by' => (string)$r['owner_name'],
                'format'     => self::guessFormat((string)$r['title']), // PDF/Excel/PPT/— 
            ];
        }, $rows);

        return ['rows' => $mapped, 'total' => $total];
    }

    /** Very simple "format" guess from title */
    private static function guessFormat(string $title): string {
        $t = strtolower($title);
        if (str_contains($t, '.pdf'))  return 'PDF';
        if (str_contains($t, '.xlsx') || str_contains($t, '.xls')) return 'Excel';
        if (str_contains($t, '.ppt')  || str_contains($t, '.pptx')) return 'PPT';
        return 'PDF'; // default badge (you can change to '—' if you prefer)
    }

    /** Pull recent report-related notifications from user_activity */
    public static function notifications(int $limit = 10): array {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
          SELECT detail, status, occurred_at
          FROM user_activity
          WHERE type = 'Report'
          ORDER BY occurred_at DESC
          LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(function(array $r): array {
            $status = (string)($r['status'] ?? '');
            $color  = match (strtolower($status)) {
                'approved', 'complete', 'completed' => '#10b981',
                'rejected'                           => '#ef4444',
                'pending'                            => '#f97316',
                default                              => '#0ea5e9', // info
            };
            return [
                'title' => (string)$r['detail'],
                'by'    => 'System',
                'when'  => self::fmtWhen($r['occurred_at'] ?? null),
                'cta'   => 'View Report',
                'color' => $color,
            ];
        }, $rows);
    }

    /** Format: 'Aug 15, 2023' */
    private static function fmtDate(?string $ts): string {
        if (!$ts) return '';
        $t = strtotime($ts);
        if (!$t) return '';
        return date('M d, Y', $t);
    }

    /** Coarse relative time (e.g., '2 hours ago') */
    private static function fmtWhen(?string $ts): string {
        if (!$ts) return '';
        $t = strtotime($ts);
        if (!$t) return '';
        $diff = time() - $t;
        if ($diff < 60)   return $diff . ' sec ago';
        if ($diff < 3600) return floor($diff/60) . ' min ago';
        if ($diff < 86400) return floor($diff/3600) . ' hours ago';
        return floor($diff/86400) . ' days ago';
    }
}
