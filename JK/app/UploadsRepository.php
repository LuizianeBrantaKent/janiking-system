<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

final class UploadsRepository
{
    /* ================= Public API ================= */

    /**
     * Upload a training file.
     */
    public static function uploadTraining(
        int $userId,
        string $title,
        string $category,
        string $version,
        array $file
    ): void {
        $pdo = DB::pdo();
        $pdo->beginTransaction();

        try {
            // move file to uploads/
            $info = save_upload($file); // ['original_name','server_path','mime_type','file_size']
            if ($info === null) {
                throw new RuntimeException('No training file was selected.');
            }

            // If training tables exist, store as a training asset; otherwise fall back to documents
            if (self::tableExists($pdo, 'training_courses') && self::tableExists($pdo, 'training_assets')) {
                $courseId = self::ensureCourse($pdo, $title, $category, $version, self::extToKind($info['server_path']));
                self::insertTrainingAsset($pdo, $courseId, $userId, $info);
            } else {
                self::insertAsDocument($pdo, $title, $category, $version, $info);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Upload a normal document (policies/manuals/etc.).
     */
    public static function uploadDocument(
        int $userId,
        string $title,
        string $category,
        string $version,
        array $file
    ): void {
        $pdo = DB::pdo();
        $pdo->beginTransaction();

        try {
            $info = save_upload($file);
            if ($info === null) {
                throw new RuntimeException('No document file was selected.');
            }

            // Prefer documents table; if not present, use training tables as a generic asset
            if (self::tableExists($pdo, 'documents')) {
                self::insertAsDocument($pdo, $title, $category, $version, $info);
            } elseif (self::tableExists($pdo, 'training_courses') && self::tableExists($pdo, 'training_assets')) {
                $courseId = self::ensureCourse($pdo, $title, $category, $version, 'Document');
                self::insertTrainingAsset($pdo, $courseId, $userId, $info);
            } else {
                // If nothing exists, throw a clear error
                throw new RuntimeException('No suitable table found to save the upload. Create either `documents` or the pair `training_courses` + `training_assets`.');
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Recent uploads for the table on the Upload Files page.
     * Tries the explicit uploads table first; falls back to training/documents views.
     */
    public static function recentUploads(int $limit = 20): array
    {
        $pdo   = DB::pdo();
        $limit = max(1, min($limit, 200));
        $rows  = [];

        // 1) If you have a dedicated uploads table, prefer it (optional)
        if (self::tableExists($pdo, 'uploads')) {
            $sql = "
              SELECT
                u.title   AS name,
                u.kind    AS type,
                u.category,
                u.version,
                COALESCE(u.created_at, u.uploaded_at, NOW()) AS uploaded_at,
                COALESCE(u.status, 'Complete') AS status
              FROM uploads u
              ORDER BY uploaded_at DESC
              LIMIT :lim
            ";
            $st = $pdo->prepare($sql);
            $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $st->execute();
            $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            return self::mapUploadsRows($rows);
        }

        // 2) Else infer from training assets (if present)
        if (self::tableExists($pdo, 'training_assets') && self::tableExists($pdo, 'training_courses')) {
            $sql = "
              SELECT
                c.title                 AS name,
                c.kind                  AS type,
                c.category              AS category,
                c.version               AS version,
                ta.uploaded_at          AS uploaded_at,
                'Complete'              AS status
              FROM training_assets ta
              JOIN training_courses c ON c.course_id = ta.course_id
              ORDER BY ta.uploaded_at DESC
              LIMIT :lim
            ";
            $st = $pdo->prepare($sql);
            $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $st->execute();
            $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            return self::mapUploadsRows($rows);
        }

        // 3) Else use documents table (matches your phpMyAdmin screenshots)
        if (self::tableExists($pdo, 'documents')) {
            $sql = "
              SELECT
                d.name                  AS name,
                'Document'              AS type,
                d.category              AS category,
                d.version               AS version,
                d.updated_at            AS uploaded_at,
                CASE WHEN d.status = 'Published' THEN 'Complete' ELSE d.status END AS status
              FROM documents d
              ORDER BY d.updated_at DESC
              LIMIT :lim
            ";
            $st = $pdo->prepare($sql);
            $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $st->execute();
            $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            return self::mapUploadsRows($rows);
        }

        // 4) Nothing to show
        return [];
    }

    /* ================= Internals ================= */

    private static function mapUploadsRows(array $rows): array
    {
        return array_map(function (array $r): array {
            return [
                'name'     => (string)($r['name'] ?? ''),
                'type'     => (string)($r['type'] ?? 'Document'),
                'category' => (string)($r['category'] ?? ''),
                'version'  => (string)($r['version'] ?? ''),
                'date'     => self::fmtDate($r['uploaded_at'] ?? null),
                'status'   => (string)($r['status'] ?? 'Complete'),
            ];
        }, $rows);
    }

    private static function insertAsDocument(\PDO $pdo, string $title, string $category, string $version, array $info): void
    {
        // Make sure `documents` table exists
        if (!self::tableExists($pdo, 'documents')) {
            throw new RuntimeException('`documents` table not found. Create it or enable training tables.');
        }

        // Try to infer file type column from extension (PDF, DOCX, XLSX, PPTX, etc.)
        $fileType = strtoupper(self::extFromPath($info['server_path']) ?: 'PDF');

        $sql = "
          INSERT INTO documents (name, file_type, category, version, owner_user_id, status, updated_at, is_public)
          VALUES (:name, :file_type, :category, :version, :owner, 'Published', NOW(), 1)
        ";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':name'      => $title,
            ':file_type' => $fileType,
            ':category'  => $category,
            ':version'   => $version,
            ':owner'     =>  $GLOBALS['__jk_owner_id'] ?? 1, // fallback owner
        ]);

        // Optionally record file location in a companion table (document_files)
        if (self::tableExists($pdo, 'document_files')) {
            $docId = (int)$pdo->lastInsertId();
            $st2 = $pdo->prepare("
              INSERT INTO document_files (document_id, storage_path, mime_type, file_size, uploaded_at)
              VALUES (:doc, :path, :mime, :size, NOW())
            ");
            $st2->execute([
                ':doc'  => $docId,
                ':path' => $info['server_path'],
                ':mime' => $info['mime_type'],
                ':size' => $info['file_size'],
            ]);
        }
    }

    private static function ensureCourse(\PDO $pdo, string $title, string $category, string $version, string $kind): int
    {
        // Reuse course if already there
        $sel = $pdo->prepare("SELECT course_id FROM training_courses WHERE title = ? LIMIT 1");
        $sel->execute([$title]);
        $id = $sel->fetchColumn();
        if ($id) return (int)$id;

        $ins = $pdo->prepare("
          INSERT INTO training_courses (title, category, kind, version, created_at, updated_at)
          VALUES (:title, :category, :kind, :version, NOW(), NOW())
        ");
        $ins->execute([
            ':title'    => $title,
            ':category' => $category,
            ':kind'     => $kind,
            ':version'  => $version,
        ]);
        return (int)$pdo->lastInsertId();
    }

    private static function insertTrainingAsset(\PDO $pdo, int $courseId, int $userId, array $info): void
    {
        $st = $pdo->prepare("
          INSERT INTO training_assets (course_id, uploaded_by_user_id, storage_path, mime_type, file_size, uploaded_at)
          VALUES (:course, :user, :path, :mime, :size, NOW())
        ");
        $st->execute([
            ':course' => $courseId,
            ':user'   => $userId ?: 1,
            ':path'   => $info['server_path'],
            ':mime'   => $info['mime_type'],
            ':size'   => $info['file_size'],
        ]);
    }

    private static function tableExists(\PDO $pdo, string $table): bool
    {
        $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $q  = $pdo->prepare("
          SELECT COUNT(*)
          FROM information_schema.tables
          WHERE table_schema = ? AND table_name = ?
        ");
        $q->execute([$db, $table]);
        return (bool)$q->fetchColumn();
    }

    private static function fmtDate($ts): string
    {
        if (!$ts) return '—';
        $t = is_numeric($ts) ? (int)$ts : strtotime((string)$ts);
        if (!$t) return '—';
        return date('M d, Y', $t);
    }

    private static function extFromPath(string $p): string
    {
        return strtolower(pathinfo($p, PATHINFO_EXTENSION));
    }

    private static function extToKind(string $p): string
    {
        return match (self::extFromPath($p)) {
            'mp4','mov','avi','mkv' => 'Video',
            'pdf'                   => 'PDF',
            'ppt','pptx'            => 'PPT',
            'doc','docx'            => 'DOC',
            'xls','xlsx'            => 'Excel',
            default                 => 'File',
        };
    }
}
