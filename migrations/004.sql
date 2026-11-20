CREATE TABLE IF NOT EXISTS password_resets (
    user_id VARCHAR(64) PRIMARY KEY,
    requested_at VARCHAR(32) NOT NULL,
    token_hash CHAR(64),
    password_stamp CHAR(64),
    expires_at BIGINT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
