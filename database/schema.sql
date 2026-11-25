PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL CHECK(length(name) BETWEEN 2 AND 120),
    email TEXT NOT NULL COLLATE NOCASE UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('admin','student','ci','management','auditor')),
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0,1)),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS institutions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    institution_type TEXT NOT NULL DEFAULT 'Perguruan Tinggi',
    contact_name TEXT,
    contact_email TEXT,
    phone TEXT,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0,1)),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_institutions_active_name ON institutions(is_active, name);

CREATE TABLE IF NOT EXISTS study_programs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    institution_id INTEGER NOT NULL REFERENCES institutions(id) ON DELETE RESTRICT,
    name TEXT NOT NULL,
    degree_level TEXT,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0,1)),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    UNIQUE(institution_id, name)
);

CREATE TABLE IF NOT EXISTS practice_units (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0,1)),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS practice_periods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    start_date TEXT NOT NULL,
    end_date TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'planned' CHECK(status IN ('planned','active','completed','cancelled')),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    CHECK(end_date >= start_date)
);

CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE RESTRICT,
    institution_id INTEGER NOT NULL REFERENCES institutions(id) ON DELETE RESTRICT,
    study_program_id INTEGER NOT NULL REFERENCES study_programs(id) ON DELETE RESTRICT,
    student_number TEXT NOT NULL,
    phone TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    UNIQUE(institution_id, student_number)
);

CREATE TABLE IF NOT EXISTS clinical_instructors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE RESTRICT,
    instructor_type TEXT NOT NULL CHECK(instructor_type IN ('internal','external')),
    institution_name TEXT,
    professional_title TEXT,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0,1)),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS placements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE RESTRICT,
    practice_unit_id INTEGER NOT NULL REFERENCES practice_units(id) ON DELETE RESTRICT,
    practice_period_id INTEGER NOT NULL REFERENCES practice_periods(id) ON DELETE RESTRICT,
    start_date TEXT NOT NULL,
    end_date TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'planned' CHECK(status IN ('planned','active','completed','cancelled')),
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    CHECK(end_date >= start_date)
);
CREATE INDEX IF NOT EXISTS idx_placements_period_status ON placements(practice_period_id, status);
CREATE INDEX IF NOT EXISTS idx_placements_student ON placements(student_id, start_date);

CREATE TABLE IF NOT EXISTS ci_assignments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    placement_id INTEGER NOT NULL REFERENCES placements(id) ON DELETE RESTRICT,
    ci_id INTEGER NOT NULL REFERENCES clinical_instructors(id) ON DELETE RESTRICT,
    assigned_at TEXT NOT NULL,
    UNIQUE(placement_id, ci_id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    action TEXT NOT NULL,
    entity_type TEXT NOT NULL,
    entity_id INTEGER,
    ip_address TEXT,
    created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_audit_created ON audit_logs(created_at);
CREATE INDEX IF NOT EXISTS idx_audit_entity ON audit_logs(entity_type, entity_id);
