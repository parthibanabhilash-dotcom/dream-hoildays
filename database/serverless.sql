-- Apply once to the external MySQL database, after schema.sql (also safe for existing databases).
CREATE TABLE IF NOT EXISTS sessions (
 id_hash CHAR(64) PRIMARY KEY,
 data MEDIUMBLOB NOT NULL,
 expires_at BIGINT UNSIGNED NOT NULL,
 INDEX sessions_expiry (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
