ALTER TABLE graduation_applications
ADD COLUMN guardian_contact VARCHAR(100) AFTER email,
ADD COLUMN attendance_reason TEXT AFTER attendance_mode;
