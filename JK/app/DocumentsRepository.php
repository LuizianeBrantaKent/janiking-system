<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

final class DocumentsRepository
{
    /**
     * Search documents for the UI with filters + pagination.
     * Returns: ['rows' => array<doc>, 'total' => int]
     */
    public static function search(
        int $viewerUserId,
        ?string $q,
        ?string $category,
        ?string $status,
        string $sort = 'updated_desc',
        int $page = 1,
        int $perPage = 10
    ): array {
        $pdo = DB::pdo();

        // ---- WHERE ----
        $where = [];
        $params = [];

        // If later you want row-level visibility use something like:
        // $where[] = '(d.is_public = 1 OR d.owner_user_id = ?)';
        // $params[] = $viewerUserId;

        if ($q !== null && $q !== '') {
            $where[] = '(d.name LIKE ? OR EXISTS (
                           SELECT 1
                           FROM document_tags dt
                           JOIN tags t ON t.tag_id = dt.tag_id
                           WHERE dt.document_id = d.document_id
                             AND t.tag_name LIKE ?
                        ))';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        if ($category) {
            $where[] = 'd.category = ?';
            $params[] = $category;
        }
        if ($status) {
            $where[] = 'd.status = ?';
            $params[] = $status;
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        // ---- ORDER BY (whitelist) ----
        $orderMap = [
            'updated_desc' => 'd.updated_at DESC, d.document_id DESC',
            'alpha'        => 'd.name ASC, d.document_id DESC',
            'owner'        => 'owner_name ASC, d.updated_at DESC',
        ];
        $orderBy = $orderMap[$sort] ?? $orderMap['updated_desc'];

        // ---- Pagination ----
        $page    = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset  = ($page - 1) * $perPage;

        // ---- Total count ----
        $countSql = "SELECT COUNT(*) FROM documents d $whereSql";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // ---- Rows ----
        $sql = "
            SELECT
              d.document_id,
              d.name,
              d.file_type,
              d.category,
              d.version,
              d.updated_at,
              u.name AS owner_name,
              d.status,
              GROUP_CONCAT(t.tag_name ORDER BY t.tag_name SEPARATOR ',') AS tags_concat
            FROM documents d
            LEFT JOIN users u ON u.user_id = d.owner_user_id
            LEFT JOIN document_tags dt ON dt.document_id = d.document_id
            LEFT JOIN tags t ON t.tag_id = dt.tag_id
            $whereSql
            GROUP BY d.document_id
            ORDER BY $orderBy
            LIMIT $perPage OFFSET $offset
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Map to UI
        $docs = array_map(function (array $r): array {
            $tags = [];
            if (!empty($r['tags_concat'])) {
                foreach (explode(',', (string)$r['tags_concat']) as $t) {
                    $t = trim($t);
                    if ($t !== '') $tags[] = $t;
                }
            }
            return [
                'document_id' => (int)$r['document_id'],
                'name'        => (string)$r['name'],
                'type'        => (string)$r['file_type'],           // PDF/DOCX/…
                'category'    => (string)$r['category'],
                'version'     => (string)$r['version'],
                'updated'     => self::fmtUpdated($r['updated_at']),
                'owner'       => $r['owner_name'] ?: '—',
                'tags'        => $tags,
                'status'      => (string)$r['status'],
            ];
        }, $rows);

        return ['rows' => $docs, 'total' => $total];
    }

    /** Distinct categories for the dropdown */
    public static function categories(): array {
        $pdo = DB::pdo();
        $rows = $pdo->query("
            SELECT DISTINCT category
            FROM documents
            WHERE category IS NOT NULL AND category <> ''
            ORDER BY category ASC
        ")->fetchAll(PDO::FETCH_COLUMN);
        return $rows ?: [];
    }

    /** For download action – latest file attached to the doc */
    public static function fileInfo(int $documentId): ?array {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT d.document_id, d.name,
                   f.disk_path, f.original_name, f.mime_type
            FROM documents d
            JOIN document_files f ON f.document_id = d.document_id
            WHERE d.document_id = ?
            ORDER BY f.uploaded_at DESC
            LIMIT 1
        ");
        $stmt->execute([$documentId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$r) return null;
        return [
            'document_id' => (int)$r['document_id'],
            'display'     => (string)$r['original_name'],
            'path'        => (string)$r['disk_path'],
            'mime'        => (string)$r['mime_type'],
        ];
    }

    private static function fmtUpdated(?string $ts): string {
        if (!$ts) return '';
        $t = strtotime($ts);
        if (!$t) return '';
        return date('M d, Y', $t);
    }
}
