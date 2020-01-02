CREATE TABLE "project"
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    name       VARCHAR NOT NULL,
    color      VARCHAR NOT NULL,
    assignees  VARCHAR          DEFAULT NULL,
    status     INTEGER NOT NULL DEFAULT '0',
    created_at INTEGER          DEFAULT NULL,
    updated_at INTEGER          DEFAULT NULL
);
CREATE TABLE "user"
(
    id                   INTEGER PRIMARY KEY AUTOINCREMENT,
    email                VARCHAR NOT NULL,
    name                 VARCHAR NOT NULL,
    phone                VARCHAR          DEFAULT NULL,
    auth_key             VARCHAR NOT NULL,
    password_hash        VARCHAR NOT NULL,
    password_reset_token VARCHAR          DEFAULT NULL,
    role                 INTEGER NOT NULL DEFAULT '1',
    status               INTEGER NOT NULL DEFAULT '0',
    created_at           INTEGER          DEFAULT NULL,
    updated_at           INTEGER          DEFAULT NULL,
    theme                VARCHAR NOT NULL DEFAULT 'light',
    api_key              VARCHAR          DEFAULT NULL,
    pin_hash             VARCHAR          DEFAULT NULL,
    project_id           INTEGER          DEFAULT NULL,
    CONSTRAINT "email" UNIQUE (email),
    CONSTRAINT "password_reset_token" UNIQUE (password_reset_token),
    CONSTRAINT "fk-user-project_id" FOREIGN KEY (project_id) REFERENCES "project" (id) ON UPDATE CASCADE
);
CREATE TABLE "clock"
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    clock_in   INTEGER NOT NULL,
    clock_out  INTEGER DEFAULT NULL,
    note       VARCHAR,
    created_at INTEGER DEFAULT NULL,
    updated_at INTEGER DEFAULT NULL,
    project_id INTEGER DEFAULT NULL,
    CONSTRAINT "fk-clock-user" FOREIGN KEY (user_id) REFERENCES "user" (id) ON UPDATE CASCADE,
    CONSTRAINT "fk-clock-project_id" FOREIGN KEY (project_id) REFERENCES "project" (id) ON UPDATE CASCADE
);
CREATE TABLE "holiday"
(
    year  INTEGER NOT NULL,
    month INTEGER NOT NULL,
    day   INTEGER NOT NULL,
    PRIMARY KEY (year, month, day)
);
CREATE TABLE "off"
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL,
    start_at   DATE NOT NULL,
    end_at     DATE NOT NULL,
    note       VARCHAR,
    type       INTEGER NOT NULL DEFAULT 0,
    approved   INTEGER NOT NULL DEFAULT 0,
    created_at INTEGER DEFAULT NULL,
    updated_at INTEGER DEFAULT NULL,
    CONSTRAINT "fk-off-user" FOREIGN KEY (user_id) REFERENCES "user" (id) ON UPDATE CASCADE
);
