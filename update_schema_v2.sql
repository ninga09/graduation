-- SQL Update Script for Graduation Form Enhancements

-- 1. Add new columns for payment details and guest list
ALTER TABLE graduation_applications
ADD COLUMN payment_reference VARCHAR(100) AFTER payment_status,
ADD COLUMN payment_date DATE AFTER payment_reference,
ADD COLUMN guest_list_path VARCHAR(255) AFTER num_attendees;

-- 2. Ensure attendance_mode can handle 'In Absentia' (VARCHAR is flexible)
-- If it was already VARCHAR, this command just confirms the type and default.
ALTER TABLE graduation_applications MODIFY COLUMN attendance_mode VARCHAR(50) NOT NULL DEFAULT 'Physical';
