CREATE TABLE IF NOT EXISTS beds (
    id INTEGER PRIMARY KEY,
    code TEXT NOT NULL UNIQUE,
    status TEXT NOT NULL CHECK (status IN ('available', 'occupied', 'cleaning'))
);

CREATE TABLE IF NOT EXISTS admissions (
    id INTEGER PRIMARY KEY,
    patient_code TEXT NOT NULL,
    bed_id INTEGER NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('active', 'closed')),
    FOREIGN KEY (bed_id) REFERENCES beds(id)
);

CREATE TABLE IF NOT EXISTS transfers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admission_id INTEGER NOT NULL,
    origin_bed_id INTEGER NOT NULL,
    destination_bed_id INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (admission_id) REFERENCES admissions(id)
);

CREATE TABLE IF NOT EXISTS discharges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admission_id INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (admission_id) REFERENCES admissions(id)
);
