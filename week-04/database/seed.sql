DELETE FROM transfers;
DELETE FROM discharges;
DELETE FROM admissions;
DELETE FROM beds;

INSERT INTO beds (id, code, status) VALUES
    (1, 'CAMA-FICT-A101', 'occupied'),
    (2, 'CAMA-FICT-A102', 'available'),
    (3, 'CAMA-FICT-A103', 'available');

INSERT INTO admissions (id, patient_code, bed_id, status) VALUES
    (1, 'PAC-FICT-001', 1, 'active');
