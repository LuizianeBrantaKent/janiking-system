<?php
declare(strict_types=1);

// Match the DB you create below: "janiking"
const JK_DB_DSN  = 'mysql:host=127.0.0.1;port=3306;dbname=janiking;charset=utf8mb4';
const JK_DB_USER = 'root';
const JK_DB_PASS = '';

// uploads
const JK_MAX_UPLOAD_BYTES = 10 * 1024 * 1024; // 10 MB
const JK_UPLOAD_DIR = __DIR__ . '/../uploads';
