ALTER TABLE users ADD COLUMN owner_id VARCHAR(64);
ALTER TABLE cases ADD COLUMN owner_id VARCHAR(64);
ALTER TABLE cases ADD COLUMN collection_closed_at VARCHAR(32);
ALTER TABLE assignments ADD COLUMN allow_late INT NOT NULL DEFAULT 0;
ALTER TABLE definitions ADD COLUMN owner_id VARCHAR(64);
ALTER TABLE definitions ADD COLUMN deleted_at VARCHAR(32);
CREATE INDEX users_owner ON users(owner_id);
CREATE INDEX cases_owner ON cases(owner_id);
CREATE INDEX definitions_owner ON definitions(owner_id);
