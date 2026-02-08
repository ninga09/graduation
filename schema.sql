CREATE TABLE IF NOT EXISTS students_master (
    admission_number VARCHAR(50) PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    course VARCHAR(255) NOT NULL,
    certificate_level ENUM('Degree', 'Diploma', 'Certificate') NOT NULL,
    email VARCHAR(100)
);

-- Application submissions table
CREATE TABLE IF NOT EXISTS graduation_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    admission_number VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    graduation_year INT NOT NULL,
    course VARCHAR(255) NOT NULL,
    certificate_level ENUM('Degree', 'Diploma', 'Certificate') NOT NULL,
    payment_status ENUM('paid', 'not_paid') DEFAULT 'not_paid',
    receipt_path VARCHAR(255),
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data for testing
INSERT IGNORE INTO students_master (admission_number, first_name, middle_name, last_name, course, certificate_level, email) VALUES 
('2025-06', 'John', 'Kamau', 'Njoroge', 'Diploma in Special Needs Education', 'Diploma', 'john.kamau@kisecollege.ac.ke'),
('2025-07', 'Mary', '', 'Wambui', 'Certificate in Early Childhood Education', 'Certificate', 'mary.wambui@kisecollege.ac.ke');
