
-- 1. CREATE DATABASE

CREATE DATABASE IF NOT EXISTS medicore
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE medicore;

-- 2. USERS

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,

    role ENUM(
        'Admin',
        'Doctor',
        'Nurse',
        'Receptionist',
        'Pharmacist',
        'Laboratory',
        'Accountant'
    ) NOT NULL DEFAULT 'Receptionist',

    last_login_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. DEPARTMENTS

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. STAFF

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,

    staff_number VARCHAR(30) NOT NULL UNIQUE,

    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,

    phone VARCHAR(30),
    email VARCHAR(120),

    role ENUM(
        'Nurse',
        'Receptionist',
        'Pharmacist',
        'Laboratory',
        'Accountant',
        'Administrator'
    ) NOT NULL,

    department_id INT NULL,
    user_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_staff_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_staff_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. PATIENTS

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_number VARCHAR(30) NOT NULL UNIQUE,

    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,

    gender ENUM('Male', 'Female', 'Other') NOT NULL,

    date_of_birth DATE,

    phone VARCHAR(30),
    email VARCHAR(120),
    address VARCHAR(255),

    emergency_contact VARCHAR(120),
    emergency_phone VARCHAR(30),

    blood_group VARCHAR(10),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 6. DOCTORS

CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,

    doctor_number VARCHAR(30) NOT NULL UNIQUE,

    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,

    specialization VARCHAR(120),

    phone VARCHAR(30),
    email VARCHAR(120),

    department_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_doctor_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. APPOINTMENTS

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,

    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,

    reason TEXT,

    status ENUM(
        'Scheduled',
        'Completed',
        'Cancelled'
    ) NOT NULL DEFAULT 'Scheduled',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_appointment_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_appointment_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON DELETE CASCADE,

    INDEX idx_appointment_date (appointment_date),
    INDEX idx_appointment_doctor (doctor_id),
    INDEX idx_appointment_patient (patient_id)
) ENGINE=InnoDB;

-- 8. MEDICAL RECORDS

CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,

    symptoms TEXT,
    diagnosis TEXT,
    treatment TEXT,
    notes TEXT,

    record_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_record_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_record_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON DELETE CASCADE,

    INDEX idx_record_patient (patient_id),
    INDEX idx_record_doctor (doctor_id)
) ENGINE=InnoDB;

-- 9. PATIENT VITALS

CREATE TABLE patient_vitals (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    recorded_by INT NULL,

    temperature DECIMAL(4,1),
    blood_pressure VARCHAR(20),
    pulse_rate INT,
    respiratory_rate INT,
    oxygen_saturation DECIMAL(5,2),
    weight DECIMAL(6,2),

    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_vitals_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_vitals_user
        FOREIGN KEY (recorded_by)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT chk_temperature
        CHECK (
            temperature IS NULL
            OR temperature BETWEEN 25 AND 45
        ),

    CONSTRAINT chk_pulse
        CHECK (
            pulse_rate IS NULL
            OR pulse_rate > 0
        ),

    CONSTRAINT chk_oxygen
        CHECK (
            oxygen_saturation IS NULL
            OR oxygen_saturation BETWEEN 0 AND 100
        )
) ENGINE=InnoDB;

-- 10. MEDICINES

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,

    medicine_name VARCHAR(150) NOT NULL,
    category VARCHAR(100),

    quantity INT NOT NULL DEFAULT 0,

    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,

    expiry_date DATE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_medicine_quantity
        CHECK (quantity >= 0),

    CONSTRAINT chk_medicine_price
        CHECK (unit_price >= 0),

    INDEX idx_medicine_name (medicine_name)
) ENGINE=InnoDB;

-- 11. PRESCRIPTIONS

CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,

    prescription_date DATE NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_prescription_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_prescription_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON DELETE CASCADE,

    INDEX idx_prescription_patient (patient_id)
) ENGINE=InnoDB;

-- 12. PRESCRIPTION ITEMS

CREATE TABLE prescription_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    prescription_id INT NOT NULL,
    medicine_id INT NOT NULL,

    dosage VARCHAR(100),
    frequency VARCHAR(100),
    duration VARCHAR(100),

    quantity INT NOT NULL,

    CONSTRAINT fk_prescription_item_prescription
        FOREIGN KEY (prescription_id)
        REFERENCES prescriptions(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_prescription_item_medicine
        FOREIGN KEY (medicine_id)
        REFERENCES medicines(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_prescription_quantity
        CHECK (quantity > 0)
) ENGINE=InnoDB;

-- 13. MEDICINE DISPENSING

CREATE TABLE medicine_dispensing (
    id INT AUTO_INCREMENT PRIMARY KEY,

    prescription_item_id INT NOT NULL,
    pharmacist_id INT NULL,

    quantity_dispensed INT NOT NULL,

    dispensed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_dispensing_item
        FOREIGN KEY (prescription_item_id)
        REFERENCES prescription_items(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_dispensing_pharmacist
        FOREIGN KEY (pharmacist_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT chk_quantity_dispensed
        CHECK (quantity_dispensed > 0)
) ENGINE=InnoDB;

-- 14. LABORATORY TESTS

CREATE TABLE lab_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NULL,

    test_name VARCHAR(150) NOT NULL,

    test_date DATE NOT NULL,

    result TEXT,

    status ENUM(
        'Pending',
        'Completed'
    ) NOT NULL DEFAULT 'Pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_lab_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_lab_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON DELETE SET NULL,

    INDEX idx_lab_patient (patient_id),
    INDEX idx_lab_date (test_date)
) ENGINE=InnoDB;

-- 15. WARDS

CREATE TABLE wards (
    id INT AUTO_INCREMENT PRIMARY KEY,

    ward_name VARCHAR(100) NOT NULL UNIQUE,
    ward_type VARCHAR(100),

    capacity INT NOT NULL DEFAULT 0,

    CONSTRAINT chk_ward_capacity
        CHECK (capacity >= 0)
) ENGINE=InnoDB;

-- 16. BEDS

CREATE TABLE beds (
    id INT AUTO_INCREMENT PRIMARY KEY,

    ward_id INT NOT NULL,

    bed_number VARCHAR(30) NOT NULL,

    status ENUM(
        'Available',
        'Occupied',
        'Maintenance'
    ) NOT NULL DEFAULT 'Available',

    CONSTRAINT fk_bed_ward
        FOREIGN KEY (ward_id)
        REFERENCES wards(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_bed_number
        UNIQUE (ward_id, bed_number)
) ENGINE=InnoDB;

-- 17. ADMISSIONS

CREATE TABLE admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    bed_id INT NOT NULL,

    admission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    discharge_date DATETIME NULL,

    reason TEXT,

    status ENUM(
        'Admitted',
        'Discharged'
    ) NOT NULL DEFAULT 'Admitted',

    CONSTRAINT fk_admission_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_admission_bed
        FOREIGN KEY (bed_id)
        REFERENCES beds(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_discharge_date
        CHECK (
            discharge_date IS NULL
            OR discharge_date >= admission_date
        )
) ENGINE=InnoDB;

-- 18. SERVICES

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,

    service_name VARCHAR(150) NOT NULL UNIQUE,

    price DECIMAL(12,2) NOT NULL DEFAULT 0,

    CONSTRAINT chk_service_price
        CHECK (price >= 0)
) ENGINE=InnoDB;

-- 19. BILLS

CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,

    bill_date DATE NOT NULL DEFAULT (CURRENT_DATE),

    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,

    status ENUM(
        'Unpaid',
        'Partially Paid',
        'Paid'
    ) NOT NULL DEFAULT 'Unpaid',

    CONSTRAINT fk_bill_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_bill_total
        CHECK (total_amount >= 0),

    INDEX idx_bill_patient (patient_id),
    INDEX idx_bill_date (bill_date)
) ENGINE=InnoDB;

-- 20. BILL ITEMS

CREATE TABLE bill_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    bill_id INT NOT NULL,
    service_id INT NOT NULL,

    quantity INT NOT NULL DEFAULT 1,

    amount DECIMAL(12,2) NOT NULL,

    CONSTRAINT fk_bill_item_bill
        FOREIGN KEY (bill_id)
        REFERENCES bills(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_bill_item_service
        FOREIGN KEY (service_id)
        REFERENCES services(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_bill_item_quantity
        CHECK (quantity > 0),

    CONSTRAINT chk_bill_item_amount
        CHECK (amount >= 0)
) ENGINE=InnoDB;

-- 21. PAYMENTS

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    bill_id INT NOT NULL,

    amount DECIMAL(12,2) NOT NULL,

    payment_method ENUM(
        'Cash',
        'Mobile Money',
        'Insurance',
        'Card'
    ) NOT NULL DEFAULT 'Cash',

    reference_number VARCHAR(100),

    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payment_bill
        FOREIGN KEY (bill_id)
        REFERENCES bills(id)
        ON DELETE CASCADE,

    CONSTRAINT chk_payment_amount
        CHECK (amount > 0),

    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_bill (bill_id)
) ENGINE=InnoDB;

-- 22. AUDIT LOGS

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NULL,

    action VARCHAR(100) NOT NULL,

    table_name VARCHAR(100),

    record_id INT,

    description TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    INDEX idx_audit_user (user_id),
    INDEX idx_audit_date (created_at)
) ENGINE=InnoDB;

-- 23. TRIGGERS

DELIMITER $$

-- Trigger 1:
-- Prevent medicine quantity from becoming negative

CREATE TRIGGER before_medicine_update
BEFORE UPDATE ON medicines
FOR EACH ROW
BEGIN

    IF NEW.quantity < 0 THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Medicine quantity cannot be negative';

    END IF;

END$$

-- Trigger 2:
-- Calculate bill total after inserting a bill item

CREATE TRIGGER after_bill_item_insert
AFTER INSERT ON bill_items
FOR EACH ROW
BEGIN

    UPDATE bills
    SET total_amount = (
        SELECT COALESCE(
            SUM(amount * quantity),
            0
        )
        FROM bill_items
        WHERE bill_id = NEW.bill_id
    )
    WHERE id = NEW.bill_id;

END$$

-- Trigger 3:
-- Recalculate bill total after updating bill item

CREATE TRIGGER after_bill_item_update
AFTER UPDATE ON bill_items
FOR EACH ROW
BEGIN

    UPDATE bills
    SET total_amount = (
        SELECT COALESCE(
            SUM(amount * quantity),
            0
        )
        FROM bill_items
        WHERE bill_id = NEW.bill_id
    )
    WHERE id = NEW.bill_id;

END$$

-- Trigger 4:
-- Recalculate bill total after deleting bill item

CREATE TRIGGER after_bill_item_delete
AFTER DELETE ON bill_items
FOR EACH ROW
BEGIN

    UPDATE bills
    SET total_amount = (
        SELECT COALESCE(
            SUM(amount * quantity),
            0
        )
        FROM bill_items
        WHERE bill_id = OLD.bill_id
    )
    WHERE id = OLD.bill_id;

END$$

-- Trigger 5:
-- Prevent payment greater than outstanding balance

CREATE TRIGGER before_payment_insert
BEFORE INSERT ON payments
FOR EACH ROW
BEGIN

    DECLARE bill_total DECIMAL(12,2);
    DECLARE amount_paid DECIMAL(12,2);

    SELECT total_amount
    INTO bill_total
    FROM bills
    WHERE id = NEW.bill_id;

    SELECT COALESCE(SUM(amount), 0)
    INTO amount_paid
    FROM payments
    WHERE bill_id = NEW.bill_id;

    IF amount_paid + NEW.amount > bill_total THEN

        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Payment exceeds outstanding bill amount';

    END IF;

END$$

-- Trigger 6:
-- Automatically update bill payment status

CREATE TRIGGER after_payment_insert
AFTER INSERT ON payments
FOR EACH ROW
BEGIN

    DECLARE bill_total DECIMAL(12,2);
    DECLARE total_paid DECIMAL(12,2);

    SELECT total_amount
    INTO bill_total
    FROM bills
    WHERE id = NEW.bill_id;

    SELECT COALESCE(SUM(amount), 0)
    INTO total_paid
    FROM payments
    WHERE bill_id = NEW.bill_id;

    UPDATE bills
    SET status =
        CASE

            WHEN total_paid = 0
                THEN 'Unpaid'

            WHEN total_paid < bill_total
                THEN 'Partially Paid'

            ELSE 'Paid'

        END

    WHERE id = NEW.bill_id;

END$$

DELIMITER ;

-- 24. REPORTING VIEWS

-- Patient Summary

CREATE OR REPLACE VIEW patient_summary AS

SELECT
    p.id,
    p.patient_number,
    CONCAT(
        p.first_name,
        ' ',
        p.last_name
    ) AS patient_name,

    p.gender,
    p.date_of_birth,
    p.phone,
    p.blood_group,
    p.created_at

FROM patients p;

-- Appointment Report

CREATE OR REPLACE VIEW appointment_report AS

SELECT
    a.id,

    a.appointment_date,
    a.appointment_time,

    p.patient_number,

    CONCAT(
        p.first_name,
        ' ',
        p.last_name
    ) AS patient_name,

    CONCAT(
        d.first_name,
        ' ',
        d.last_name
    ) AS doctor_name,

    d.specialization,

    a.reason,
    a.status

FROM appointments a

JOIN patients p
    ON a.patient_id = p.id

JOIN doctors d
    ON a.doctor_id = d.id;

-- Financial Report

CREATE OR REPLACE VIEW financial_report AS

SELECT

    b.id AS bill_id,

    b.bill_date,

    p.patient_number,

    CONCAT(
        p.first_name,
        ' ',
        p.last_name
    ) AS patient_name,

    b.total_amount,

    COALESCE(
        SUM(pay.amount),
        0
    ) AS amount_paid,

    b.total_amount -
    COALESCE(
        SUM(pay.amount),
        0
    ) AS balance,

    b.status

FROM bills b

JOIN patients p
    ON b.patient_id = p.id

LEFT JOIN payments pay
    ON b.id = pay.bill_id

GROUP BY
    b.id,
    b.bill_date,
    p.patient_number,
    p.first_name,
    p.last_name,
    b.total_amount,
    b.status;

-- Medicine Stock Report

CREATE OR REPLACE VIEW medicine_stock_report AS

SELECT

    id,
    medicine_name,
    category,
    quantity,
    unit_price,
    expiry_date,

    CASE

        WHEN quantity = 0
            THEN 'Out of Stock'

        WHEN quantity <= 10
            THEN 'Low Stock'

        ELSE 'In Stock'

    END AS stock_status

FROM medicines;

-- Laboratory Report

CREATE OR REPLACE VIEW laboratory_report AS

SELECT

    l.id,

    l.test_date,

    p.patient_number,

    CONCAT(
        p.first_name,
        ' ',
        p.last_name
    ) AS patient_name,

    l.test_name,
    l.result,
    l.status,

    CASE

        WHEN d.id IS NULL
            THEN 'Not Assigned'

        ELSE CONCAT(
            d.first_name,
            ' ',
            d.last_name
        )

    END AS doctor_name

FROM lab_tests l

JOIN patients p
    ON l.patient_id = p.id

LEFT JOIN doctors d
    ON l.doctor_id = d.id;

-- Admission Report

CREATE OR REPLACE VIEW admission_report AS

SELECT

    a.id,

    p.patient_number,

    CONCAT(
        p.first_name,
        ' ',
        p.last_name
    ) AS patient_name,

    w.ward_name,

    b.bed_number,

    a.admission_date,
    a.discharge_date,

    a.reason,
    a.status

FROM admissions a

JOIN patients p
    ON a.patient_id = p.id

JOIN beds b
    ON a.bed_id = b.id

JOIN wards w
    ON b.ward_id = w.id;

-- 25. INITIAL DEPARTMENT DATA

INSERT INTO departments
(
    department_name,
    description
)
VALUES

(
    'Outpatient',
    'General outpatient medical services'
),

(
    'Emergency',
    'Emergency and urgent medical care'
),

(
    'Pediatrics',
    'Medical care for children'
),

(
    'Maternity',
    'Maternal and childbirth services'
),

(
    'Laboratory',
    'Laboratory and diagnostic services'
),

(
    'Pharmacy',
    'Medicine dispensing and management'
),

(
    'Surgery',
    'Surgical procedures and post-operative care'
),

(
    'Radiology',
    'Medical imaging and diagnostic services'
);

-- 26. INITIAL WARD DATA

INSERT INTO wards
(
    ward_name,
    ward_type,
    capacity
)
VALUES

(
    'General Ward',
    'General',
    30
),

(
    'Maternity Ward',
    'Maternity',
    20
),

(
    'Pediatric Ward',
    'Pediatric',
    20
),

(
    'Surgical Ward',
    'Surgical',
    15
);

-- 27. INITIAL BED DATA

INSERT INTO beds
(
    ward_id,
    bed_number,
    status
)
VALUES

(1, 'G001', 'Available'),
(1, 'G002', 'Available'),
(1, 'G003', 'Available'),
(1, 'G004', 'Available'),
(1, 'G005', 'Available'),

(2, 'M001', 'Available'),
(2, 'M002', 'Available'),
(2, 'M003', 'Available'),

(3, 'P001', 'Available'),
(3, 'P002', 'Available'),
(3, 'P003', 'Available'),

(4, 'S001', 'Available'),
(4, 'S002', 'Available'),
(4, 'S003', 'Available');

-- 28. INITIAL SERVICES

INSERT INTO services
(
    service_name,
    price
)
VALUES

(
    'Consultation',
    20000.00
),

(
    'Laboratory Test',
    15000.00
),

(
    'Admission',
    50000.00
),

(
    'General Treatment',
    30000.00
),

(
    'X-Ray',
    50000.00
),

(
    'Ultrasound',
    40000.00
),

(
    'Surgery',
    300000.00
),

(
    'Emergency Treatment',
    75000.00
),

(
    'Maternity Consultation',
    30000.00
),

(
    'Dental Consultation',
    25000.00
);

-- 29. INITIAL MEDICINE DATA

INSERT INTO medicines
(
    medicine_name,
    category,
    quantity,
    unit_price,
    expiry_date
)
VALUES

(
    'Paracetamol',
    'Painkiller',
    100,
    500.00,
    '2028-12-31'
),

(
    'Amoxicillin',
    'Antibiotic',
    80,
    1000.00,
    '2028-10-31'
),

(
    'Ibuprofen',
    'Painkiller',
    70,
    800.00,
    '2028-09-30'
),

(
    'Ceftriaxone',
    'Antibiotic',
    40,
    5000.00,
    '2028-08-31'
),

(
    'Metformin',
    'Diabetes',
    60,
    1200.00,
    '2029-01-31'
),

(
    'Amlodipine',
    'Hypertension',
    90,
    900.00,
    '2029-02-28'
),

(
    'Omeprazole',
    'Gastric',
    75,
    700.00,
    '2028-11-30'
),

(
    'Artemether',
    'Antimalarial',
    50,
    3500.00,
    '2028-07-31'
),

(
    'Azithromycin',
    'Antibiotic',
    45,
    2000.00,
    '2028-06-30'
),

(
    'ORS',
    'Rehydration',
    120,
    500.00,
    '2028-05-31'
);
