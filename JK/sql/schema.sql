-- ===========================================================
-- JaniKing DB (consolidated schema)
-- MySQL 8.x compatible
-- ===========================================================

-- Database
CREATE DATABASE IF NOT EXISTS janiking
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE janiking;

-- =========================
-- USERS & SETTINGS
-- =========================
CREATE TABLE IF NOT EXISTS users (
  user_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(120) NOT NULL,
  role           ENUM('Admin','Staff') NOT NULL DEFAULT 'Staff',
  email          VARCHAR(190) NOT NULL,
  avatar         VARCHAR(255) NULL,
  password_hash  VARCHAR(255) NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_settings (
  user_id         INT UNSIGNED NOT NULL,
  phone           VARCHAR(40)  NULL,
  position_title  VARCHAR(120) NULL,
  location_text   VARCHAR(190) NULL,
  timezone_id     VARCHAR(60)  NULL,
  language_label  VARCHAR(60)  NULL,
  two_factor      TINYINT(1)   NOT NULL DEFAULT 0,

  notif_email     TINYINT(1)   NOT NULL DEFAULT 1,
  notif_tasks     TINYINT(1)   NOT NULL DEFAULT 1,
  notif_schedule  TINYINT(1)   NOT NULL DEFAULT 1,
  notif_training  TINYINT(1)   NOT NULL DEFAULT 1,
  notif_documents TINYINT(1)   NOT NULL DEFAULT 1,

  updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_user_settings_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- ANNOUNCEMENTS
-- =========================
CREATE TABLE IF NOT EXISTS announcements (
  announcement_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  author_id       INT UNSIGNED NOT NULL,
  subject         VARCHAR(200) NOT NULL,
  body            MEDIUMTEXT NOT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ann_created_at (created_at),
  CONSTRAINT fk_ann_author
    FOREIGN KEY (author_id) REFERENCES users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS announcement_attachments (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  announcement_id  INT UNSIGNED NOT NULL,
  original_name    VARCHAR(255) NOT NULL,
  server_path      VARCHAR(255) NOT NULL,
  file_size        INT UNSIGNED NOT NULL,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_att_announcement
    FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS announcement_replies (
  reply_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  announcement_id INT UNSIGNED NOT NULL,
  author_id       INT UNSIGNED NOT NULL,
  body            MEDIUMTEXT NOT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rep_announcement
    FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id)
    ON DELETE CASCADE,
  CONSTRAINT fk_rep_author
    FOREIGN KEY (author_id) REFERENCES users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS announcement_reads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  announcement_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_ann_read (announcement_id, user_id),
  FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TASKS
-- =========================
CREATE TABLE IF NOT EXISTS tasks (
  task_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description MEDIUMTEXT NULL,
  due_at DATETIME NULL,
  status ENUM('Open','In Progress','Blocked','Completed') NOT NULL DEFAULT 'Open',
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS task_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  task_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS task_progress (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  task_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  progress_pct TINYINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                         ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_task_user (task_id, user_id),
  FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- SHIFTS
-- =========================
CREATE TABLE IF NOT EXISTS shifts (
  shift_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NOT NULL,
  location VARCHAR(200) NOT NULL,
  role_label VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shift_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shift_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (shift_id) REFERENCES shifts(shift_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- REPORTS (lightweight)
-- =========================
CREATE TABLE IF NOT EXISTS reports (
  report_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  owner_user_id INT UNSIGNED NOT NULL,
  status ENUM('Pending','Submitted','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- USER ACTIVITY
-- =========================
CREATE TABLE IF NOT EXISTS user_activity (
  activity_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  type ENUM('Report','Training','Message','Schedule','Task','System') NOT NULL,
  detail VARCHAR(400) NOT NULL,
  status VARCHAR(40) NULL,
  occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  KEY idx_activity_user_time (user_id, occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================================
-- DOCUMENTS MODULE
-- ===========================================================
CREATE TABLE IF NOT EXISTS documents (
  document_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(255) NOT NULL,
  file_type      VARCHAR(10) NOT NULL,                -- PDF/DOCX/XLSX/PPTX
  category       VARCHAR(120) NOT NULL,
  version        VARCHAR(20) NOT NULL DEFAULT '1.0',
  owner_user_id  INT UNSIGNED NULL,
  status         ENUM('Published','Draft','Archived') NOT NULL DEFAULT 'Draft',
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
  is_public      TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (owner_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
  KEY idx_docs_updated (updated_at),
  KEY idx_docs_category (category),
  KEY idx_docs_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tags (
  tag_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tag_name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS document_tags (
  document_id INT UNSIGNED NOT NULL,
  tag_id      INT UNSIGNED NOT NULL,
  PRIMARY KEY (document_id, tag_id),
  FOREIGN KEY (document_id) REFERENCES documents(document_id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id)      REFERENCES tags(tag_id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS document_files (
  file_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  document_id      INT UNSIGNED NOT NULL,
  disk_path        VARCHAR(500) NOT NULL,   -- where helpers.php put the file (web path)
  original_name    VARCHAR(255) NOT NULL,
  mime_type        VARCHAR(120) NOT NULL,
  file_size        INT UNSIGNED NOT NULL,
  uploaded_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (document_id) REFERENCES documents(document_id) ON DELETE CASCADE,
  KEY idx_df_doc (document_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional view to simplify reads
CREATE OR REPLACE VIEW vw_documents_with_tags AS
SELECT
  d.document_id,
  d.name,
  d.file_type,
  d.category,
  d.version,
  d.owner_user_id,
  u.name AS owner_name,
  d.status,
  d.updated_at,
  d.is_public,
  GROUP_CONCAT(t.tag_name ORDER BY t.tag_name SEPARATOR ',') AS tags_concat
FROM documents d
LEFT JOIN users u ON u.user_id = d.owner_user_id
LEFT JOIN document_tags dt ON dt.document_id = d.document_id
LEFT JOIN tags t ON t.tag_id = dt.tag_id
GROUP BY d.document_id;

-- ===========================================================
-- TRAINING MODULE
-- (single, non-duplicated set)
-- ===========================================================
CREATE TABLE IF NOT EXISTS training_courses (
  course_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(255) NOT NULL,
  category      VARCHAR(120) NOT NULL,
  kind          ENUM('Course','Video','Document','Quiz','PDF','SCORM') NOT NULL DEFAULT 'Course',
  version       VARCHAR(20) NOT NULL DEFAULT '1.0',
  owner_user_id INT UNSIGNED NULL,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tc_owner FOREIGN KEY (owner_user_id)
    REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_assignments (
  assignment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  course_id     INT UNSIGNED NOT NULL,
  status        ENUM('Not Started','In Progress','Completed') NOT NULL DEFAULT 'Not Started',
  assigned_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  due_at        DATETIME NULL,
  CONSTRAINT fk_ta_user   FOREIGN KEY (user_id)   REFERENCES users(user_id)            ON DELETE CASCADE,
  CONSTRAINT fk_ta_course FOREIGN KEY (course_id) REFERENCES training_courses(course_id) ON DELETE CASCADE,
  INDEX idx_ta_user (user_id),
  INDEX idx_ta_user_status (user_id, status),
  INDEX idx_ta_user_due (user_id, due_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_progress (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  course_id     INT UNSIGNED NOT NULL,
  progress_pct  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  score_pct     TINYINT UNSIGNED NULL,
  last_opened_at DATETIME NULL,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_tp (course_id, user_id),
  CONSTRAINT fk_tp_user   FOREIGN KEY (user_id)   REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_tp_course FOREIGN KEY (course_id) REFERENCES training_courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_certificates (
  cert_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  course_id   INT UNSIGNED NOT NULL,
  cert_code   VARCHAR(64) NOT NULL UNIQUE,
  issued_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at  DATETIME NULL,
  CONSTRAINT fk_tc_user   FOREIGN KEY (user_id)   REFERENCES users(user_id)   ON DELETE CASCADE,
  CONSTRAINT fk_tc_course FOREIGN KEY (course_id) REFERENCES training_courses(course_id) ON DELETE CASCADE,
  INDEX idx_tc_user (user_id),
  INDEX idx_tc_user_course (user_id, course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Physical training files uploaded (what UploadsRepository uses)
CREATE TABLE IF NOT EXISTS training_assets (
  asset_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id     INT UNSIGNED NOT NULL,
  disk_path     VARCHAR(500) NOT NULL,      -- web path (e.g., 'uploads/ab12cd34.mp4')
  original_name VARCHAR(255) NOT NULL,
  mime_type     VARCHAR(120) NOT NULL,
  file_size     INT UNSIGNED NOT NULL,
  uploaded_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ta_course (course_id),
  CONSTRAINT fk_tassets_course FOREIGN KEY (course_id)
    REFERENCES training_courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- SEED DATA (safe to re-run)
-- =========================
INSERT INTO users (name, role, email, avatar)
VALUES ('Michael Thompson','Staff','michael@example.com','Michael Thompson.png')
ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role), avatar=VALUES(avatar);

SET @uid := (SELECT user_id FROM users WHERE email='michael@example.com' LIMIT 1);

INSERT INTO announcements (author_id, subject, body)
SELECT @uid, 'Welcome!', 'This is your first announcement 🎉'
WHERE NOT EXISTS (SELECT 1 FROM announcements LIMIT 1);

SET @ann_id := (SELECT announcement_id FROM announcements ORDER BY announcement_id ASC LIMIT 1);

INSERT INTO announcement_replies (announcement_id, author_id, body)
SELECT @ann_id, @uid, 'Thanks for the update!'
WHERE NOT EXISTS (SELECT 1 FROM announcement_replies LIMIT 1);

INSERT INTO tasks (title, description, due_at, status, created_by)
SELECT * FROM (SELECT 'Deep clean – Riverdale Hospital (Wing B)', 'Wing B checklist', DATE_ADD(NOW(), INTERVAL 6 HOUR), 'In Progress', @uid) x
WHERE NOT EXISTS (SELECT 1 FROM tasks LIMIT 1);
INSERT INTO tasks (title, description, due_at, status, created_by)
VALUES ('Inventory count – Supplies room', NULL, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Open', @uid),
       ('QA checklist – Lobby & Restrooms', NULL, DATE_ADD(NOW(), INTERVAL 2 DAY), 'Open', @uid)
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT IGNORE INTO task_assignments (task_id, user_id)
SELECT t.task_id, @uid FROM tasks t;

INSERT INTO task_progress (task_id, user_id, progress_pct)
SELECT t.task_id, @uid,
       CASE WHEN ROW_NUMBER() OVER (ORDER BY t.task_id) = 1 THEN 60
            WHEN ROW_NUMBER() OVER (ORDER BY t.task_id) = 2 THEN 20
            ELSE 80 END
FROM (SELECT task_id FROM tasks ORDER BY task_id) t
ON DUPLICATE KEY UPDATE progress_pct=VALUES(progress_pct);

INSERT INTO shifts (starts_at, ends_at, location, role_label)
SELECT * FROM (SELECT DATE_ADD(CURDATE(), INTERVAL 9 HOUR),  DATE_ADD(CURDATE(), INTERVAL 17 HOUR), 'Riverdale Hospital', 'Cleaning Supervisor') s
WHERE NOT EXISTS (SELECT 1 FROM shifts LIMIT 1);
INSERT INTO shifts (starts_at, ends_at, location, role_label)
VALUES (DATE_ADD(CURDATE(), INTERVAL 33 HOUR), DATE_ADD(CURDATE(), INTERVAL 41 HOUR), 'Westview Offices', 'Team Lead'),
       (DATE_ADD(CURDATE(), INTERVAL 55 HOUR), DATE_ADD(CURDATE(), INTERVAL 63 HOUR), 'City Hall', 'Supervisor');

INSERT IGNORE INTO shift_assignments (shift_id, user_id)
SELECT s.shift_id, @uid FROM shifts s;

INSERT INTO reports (title, owner_user_id, status)
VALUES ('Incident Follow-up – Westview', @uid, 'Pending'),
       ('Supply Audit – Q3', @uid, 'Pending')
ON DUPLICATE KEY UPDATE status=VALUES(status);

INSERT INTO user_activity (user_id, type, detail, status, occurred_at)
VALUES
  (@uid,'Report','Q2 Financial Summary uploaded by Sarah Johnson','Complete', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (@uid,'Training','Completed module: Chemical Safety','Passed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
  (@uid,'Message','New announcement: Cleaning Protocol Update','Unread', DATE_SUB(NOW(), INTERVAL 4 DAY)),
  (@uid,'Schedule','Shift swapped with Robert Chen','Approved', DATE_SUB(NOW(), INTERVAL 5 DAY))
ON DUPLICATE KEY UPDATE detail=VALUES(detail);

-- Seed tags
INSERT IGNORE INTO tags (tag_name)
VALUES ('safety'), ('training'), ('equipment'), ('customer'),
       ('guide'), ('hr'), ('holiday'), ('slides');

-- Seed documents (only if none)
INSERT INTO documents (name, file_type, category, version, owner_user_id, status, updated_at, is_public)
SELECT * FROM (
  SELECT 'Safety Protocols 2024.pdf','PDF','Policies','2.1',@uid,'Published','2023-10-15 09:00:00',1 UNION ALL
  SELECT 'Floor_Scrubber_Manual.docx','DOCX','Manuals','1.0',@uid,'Published','2023-10-12 09:00:00',1 UNION ALL
  SELECT 'Customer_Service_Guide.pdf','PDF','Customer Service','1.5',@uid,'Draft','2023-10-10 09:00:00',1 UNION ALL
  SELECT 'Holiday_Schedule.xlsx','XLSX','Schedules','3.2',@uid,'Published','2023-10-08 09:00:00',1 UNION ALL
  SELECT 'Chemical_Safety_Training.pptx','PPTX','Safety Training','1.1',@uid,'Archived','2023-10-05 09:00:00',1
) seed
WHERE NOT EXISTS (SELECT 1 FROM documents LIMIT 1);

-- Link tags to seeded documents
INSERT IGNORE INTO document_tags (document_id, tag_id)
SELECT d.document_id, t.tag_id
FROM documents d
JOIN tags t
  ON (d.name LIKE '%Safety%'           AND t.tag_name IN ('safety','training'))
  OR (d.name LIKE '%Floor_Scrubber%'   AND t.tag_name = 'equipment')
  OR (d.name LIKE '%Customer_Service%' AND t.tag_name IN ('customer','guide'))
  OR (d.name LIKE '%Holiday_Schedule%' AND t.tag_name IN ('hr','holiday'))
  OR (d.name LIKE '%Chemical_Safety%'  AND t.tag_name IN ('training','slides'));

-- -------------------------
-- Seed training data
-- -------------------------
INSERT INTO training_courses (title, category, kind, owner_user_id)
SELECT * FROM (SELECT 'Chemical Safety Training','Safety Training','Video', @uid) x
WHERE NOT EXISTS (SELECT 1 FROM training_courses LIMIT 1);
INSERT INTO training_courses (title, category, kind, owner_user_id) VALUES
  ('Customer Interaction Guidelines','Customer Service','Course', @uid),
  ('Equipment Manual – Floor Scrubber','Equipment','Document', @uid),
  ('Infection Control Basics','Safety Training','Course', @uid),
  ('New Employee Orientation','HR','Course', @uid)
ON DUPLICATE KEY UPDATE title=VALUES(title);

SET @c1 := (SELECT course_id FROM training_courses WHERE title='Chemical Safety Training' LIMIT 1);
SET @c2 := (SELECT course_id FROM training_courses WHERE title='Customer Interaction Guidelines' LIMIT 1);
SET @c3 := (SELECT course_id FROM training_courses WHERE title='Equipment Manual – Floor Scrubber' LIMIT 1);
SET @c4 := (SELECT course_id FROM training_courses WHERE title='Infection Control Basics' LIMIT 1);
SET @c5 := (SELECT course_id FROM training_courses WHERE title='New Employee Orientation' LIMIT 1);

INSERT IGNORE INTO training_assignments (course_id, user_id, due_at, status) VALUES
  (@c1, @uid, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'In Progress'),
  (@c2, @uid, DATE_ADD(CURDATE(), INTERVAL 17 DAY), 'Not Started'),
  (@c3, @uid, DATE_ADD(CURDATE(), INTERVAL -2 DAY), 'Completed'),
  (@c4, @uid, DATE_ADD(CURDATE(), INTERVAL -6 DAY), 'In Progress'),
  (@c5, @uid, DATE_ADD(CURDATE(), INTERVAL 25 DAY), 'Completed');

INSERT INTO training_progress (course_id, user_id, progress_pct, score_pct, last_opened_at)
VALUES
  (@c1, @uid, 55, NULL, NOW()),
  (@c2, @uid, 0,  NULL, NULL),
  (@c3, @uid, 100, 98, NOW()),
  (@c4, @uid, 15, NULL, NOW()),
  (@c5, @uid, 100, 88, NOW())
ON DUPLICATE KEY UPDATE progress_pct=VALUES(progress_pct), score_pct=VALUES(score_pct), last_opened_at=VALUES(last_opened_at);

INSERT IGNORE INTO training_certificates (user_id, course_id, issued_at, expires_at, cert_code)
VALUES
  (@uid, @c3, DATE_ADD(CURDATE(), INTERVAL -330 DAY), DATE_ADD(CURDATE(), INTERVAL 2 YEAR), 'CERT-004219'),
  (@uid, @c5, DATE_ADD(CURDATE(), INTERVAL -337 DAY), DATE_ADD(CURDATE(), INTERVAL 2 YEAR), 'CERT-003901');

-- ===========================================================
-- STORED PROCEDURES (CRUD for documents)
--  NOTE: use VARCHAR, not ENUM, in procedure params
-- ===========================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_create_document $$
CREATE PROCEDURE sp_create_document(
  IN p_name VARCHAR(255),
  IN p_file_type VARCHAR(10),
  IN p_category VARCHAR(120),
  IN p_version VARCHAR(20),
  IN p_owner_user_id INT,
  IN p_status VARCHAR(20),
  IN p_is_public TINYINT,
  OUT p_document_id INT
)
BEGIN
  INSERT INTO documents(name, file_type, category, version, owner_user_id, status, is_public)
  VALUES(p_name, p_file_type, p_category, p_version, p_owner_user_id, p_status, COALESCE(p_is_public,1));
  SET p_document_id = LAST_INSERT_ID();
END $$

DROP PROCEDURE IF EXISTS sp_get_document $$
CREATE PROCEDURE sp_get_document(IN p_document_id INT)
BEGIN
  SELECT *
  FROM vw_documents_with_tags
  WHERE document_id = p_document_id;
END $$

DROP PROCEDURE IF EXISTS sp_list_documents $$
CREATE PROCEDURE sp_list_documents(
  IN p_q VARCHAR(255),
  IN p_category VARCHAR(120),
  IN p_status VARCHAR(20),
  IN p_sort VARCHAR(20),         -- 'updated_desc' | 'alpha' | 'owner'
  IN p_page INT,
  IN p_per_page INT
)
BEGIN
  SET p_page := IFNULL(p_page,1);
  SET p_per_page := IFNULL(p_per_page,10);
  SET @offset := (p_page-1)*p_per_page;

  SET @order := CASE COALESCE(p_sort,'updated_desc')
                  WHEN 'alpha' THEN 'name ASC, document_id DESC'
                  WHEN 'owner' THEN 'owner_name ASC, updated_at DESC'
                  ELSE 'updated_at DESC, document_id DESC'
                END;

  SET @sql := CONCAT(
    "SELECT document_id, name, file_type, category, version, owner_user_id, owner_name, status, updated_at, is_public, tags_concat
     FROM vw_documents_with_tags WHERE 1=1");

  IF p_q IS NOT NULL AND p_q <> '' THEN
    SET @sql := CONCAT(@sql, " AND (name LIKE ? OR tags_concat LIKE ?)");
  END IF;
  IF p_category IS NOT NULL AND p_category <> '' THEN
    SET @sql := CONCAT(@sql, " AND category = ?");
  END IF;
  IF p_status IS NOT NULL AND p_status <> '' THEN
    SET @sql := CONCAT(@sql, " AND status = ?");
  END IF;

  SET @sql := CONCAT(@sql, " ORDER BY ", @order, " LIMIT ? OFFSET ?");

  PREPARE stmt FROM @sql;

  IF p_q IS NOT NULL AND p_q <> '' THEN
    SET @q1 := CONCAT('%', p_q, '%');
    SET @q2 := CONCAT('%', p_q, '%');
    IF (p_category IS NOT NULL AND p_category <> '') AND (p_status IS NOT NULL AND p_status <> '') THEN
      EXECUTE stmt USING @q1, @q2, p_category, p_status, p_per_page, @offset;
    ELSEIF (p_category IS NOT NULL AND p_category <> '') THEN
      EXECUTE stmt USING @q1, @q2, p_category, p_per_page, @offset;
    ELSEIF (p_status IS NOT NULL AND p_status <> '') THEN
      EXECUTE stmt USING @q1, @q2, p_status, p_per_page, @offset;
    ELSE
      EXECUTE stmt USING @q1, @q2, p_per_page, @offset;
    END IF;
  ELSE
    IF (p_category IS NOT NULL AND p_category <> '') AND (p_status IS NOT NULL AND p_status <> '') THEN
      EXECUTE stmt USING p_category, p_status, p_per_page, @offset;
    ELSEIF (p_category IS NOT NULL AND p_category <> '') THEN
      EXECUTE stmt USING p_category, p_per_page, @offset;
    ELSEIF (p_status IS NOT NULL AND p_status <> '') THEN
      EXECUTE stmt USING p_status, p_per_page, @offset;
    ELSE
      EXECUTE stmt USING p_per_page, @offset;
    END IF;
  END IF;

  DEALLOCATE PREPARE stmt;
END $$

DROP PROCEDURE IF EXISTS sp_update_document $$
CREATE PROCEDURE sp_update_document(
  IN p_document_id INT,
  IN p_name VARCHAR(255),
  IN p_file_type VARCHAR(10),
  IN p_category VARCHAR(120),
  IN p_version VARCHAR(20),
  IN p_owner_user_id INT,
  IN p_status VARCHAR(20),
  IN p_is_public TINYINT
)
BEGIN
  UPDATE documents
     SET name = COALESCE(p_name, name),
         file_type = COALESCE(p_file_type, file_type),
         category = COALESCE(p_category, category),
         version = COALESCE(p_version, version),
         owner_user_id = COALESCE(p_owner_user_id, owner_user_id),
         status = COALESCE(p_status, status),
         is_public = COALESCE(p_is_public, is_public)
   WHERE document_id = p_document_id;
END $$

DROP PROCEDURE IF EXISTS sp_delete_document $$
CREATE PROCEDURE sp_delete_document(IN p_document_id INT)
BEGIN
  DELETE FROM documents WHERE document_id = p_document_id;
END $$

DROP PROCEDURE IF EXISTS sp_add_document_tag $$
CREATE PROCEDURE sp_add_document_tag(IN p_document_id INT, IN p_tag_name VARCHAR(80))
BEGIN
  DECLARE v_tag_id INT;
  SELECT tag_id INTO v_tag_id FROM tags WHERE tag_name = p_tag_name LIMIT 1;
  IF v_tag_id IS NULL THEN
    INSERT INTO tags(tag_name) VALUES(p_tag_name);
    SET v_tag_id = LAST_INSERT_ID();
  END IF;
  INSERT IGNORE INTO document_tags(document_id, tag_id) VALUES (p_document_id, v_tag_id);
END $$

DROP PROCEDURE IF EXISTS sp_remove_document_tag $$
CREATE PROCEDURE sp_remove_document_tag(IN p_document_id INT, IN p_tag_name VARCHAR(80))
BEGIN
  DELETE dt FROM document_tags dt
  JOIN tags t ON t.tag_id = dt.tag_id
  WHERE dt.document_id = p_document_id AND t.tag_name = p_tag_name;
END $$

DROP PROCEDURE IF EXISTS sp_attach_file $$
CREATE PROCEDURE sp_attach_file(
  IN p_document_id INT,
  IN p_disk_path VARCHAR(500),
  IN p_original_name VARCHAR(255),
  IN p_mime_type VARCHAR(120),
  IN p_file_size INT,
  OUT p_file_id INT
)
BEGIN
  INSERT INTO document_files(document_id, disk_path, original_name, mime_type, file_size)
  VALUES(p_document_id, p_disk_path, p_original_name, p_mime_type, p_file_size);
  SET p_file_id = LAST_INSERT_ID();
END $$

DROP PROCEDURE IF EXISTS sp_detach_file $$
CREATE PROCEDURE sp_detach_file(IN p_file_id INT)
BEGIN
  DELETE FROM document_files WHERE file_id = p_file_id;
END $$

DELIMITER ;

-- =========================
-- Done
-- =========================
