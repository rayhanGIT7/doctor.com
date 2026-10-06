-- Sample data. Run after schema.sql.
--
-- Login accounts (email or phone + password):
--   Admin   : admin@medibook.test     / admin123
--   Patient : patient@medibook.test   / patient123
--   Doctors : <see users below>       / doctor123

USE doctor_appointment;

INSERT INTO users (id, role, name, email, phone, password, gender) VALUES
(1,  'admin',   'System Admin',          'admin@medibook.test',    '01700000000', '$2y$10$5T53RGSVSt/EurVCGfK2GOyCS3ipGKlKa6eCy.O76psRzOeyD1y4q', NULL),
(2,  'patient', 'Rahim Uddin',           'patient@medibook.test',  '01711111111', '$2y$10$YIsT8KkZ/vZIvjF/N78xtOM9yh10FlMiHmMW6ym5v2UBkXuja2k6y', 'male'),
(3,  'doctor',  'Dr. Ayesha Rahman',     'ayesha@medibook.test',   '01811000001', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'female'),
(4,  'doctor',  'Dr. Kamal Hossain',     'kamal@medibook.test',    '01811000002', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'male'),
(5,  'doctor',  'Dr. Nusrat Jahan',      'nusrat@medibook.test',   '01811000003', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'female'),
(6,  'doctor',  'Dr. Mahmudul Hasan',    'mahmud@medibook.test',   '01811000004', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'male'),
(7,  'doctor',  'Dr. Farhana Akter',     'farhana@medibook.test',  '01811000005', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'female'),
(8,  'doctor',  'Dr. Tanvir Ahmed',      'tanvir@medibook.test',   '01811000006', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'male'),
(9,  'doctor',  'Dr. Sadia Islam',       'sadia@medibook.test',    '01811000007', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'female'),
(10, 'doctor',  'Dr. Rafiqul Karim',     'rafiq@medibook.test',    '01811000008', '$2y$10$tolXATZNgMPYqXciU3Rc7exsV6paBeHCb6/.mfzyweccPhHgU7bWK', 'male');

INSERT INTO categories (id, name, description) VALUES
(1, 'Heart Specialist',      'Cardiology - heart and blood vessel problems'),
(2, 'Kidney Specialist',     'Nephrology - kidney diseases'),
(3, 'Neurologist',           'Brain, spine and nerve disorders'),
(4, 'Child Specialist',      'Pediatrics - children''s health'),
(5, 'Medicine Specialist',   'General and internal medicine'),
(6, 'Dental Specialist',     'Teeth and oral health'),
(7, 'Skin Specialist',       'Dermatology - skin, hair and nails'),
(8, 'Eye Specialist',        'Ophthalmology - eye care'),
(9, 'Orthopedic Specialist', 'Bones, joints and muscles');

INSERT INTO hospitals (id, name, address, city, phone, email, description) VALUES
(1, 'Square Hospital',               '18/F Bir Uttam Qazi Nuruzzaman Sarak, Panthapath', 'Dhaka',      '01713141414', 'info@square.test',    'Multi-specialty tertiary care hospital.'),
(2, 'Evercare Hospital',             'Plot 81, Block E, Bashundhara R/A',                'Dhaka',      '01714090000', 'info@evercare.test',  'Full-service hospital with 24/7 emergency.'),
(3, 'United Hospital',               'Plot 15, Road 71, Gulshan',                        'Dhaka',      '01914001234', 'info@united.test',    'Private hospital with specialist care.'),
(4, 'Chevron Clinical Laboratory',   '316/A Panchlaish',                                 'Chattogram', '01811001100', 'info@chevron.test',   'Diagnostic center and specialist chambers.'),
(5, 'Popular Diagnostic Centre',     'Laxmipur, Rajshahi',                               'Rajshahi',   '01755660000', 'info@popular.test',   'Diagnostic center with doctor chambers.'),
(6, 'Ibn Sina Hospital Sylhet',      'Subhanighat',                                      'Sylhet',     '01713000999', 'info@ibnsina.test',   'Specialist hospital in Sylhet.');

INSERT INTO doctors (id, user_id, hospital_id, qualification, experience_years, consultation_fee, chamber_info, bio) VALUES
(1, 3,  1, 'MBBS, FCPS (Cardiology)',            15, 1500, 'Room 504, 5th floor', 'Senior consultant cardiologist with special interest in heart failure.'),
(2, 4,  2, 'MBBS, MD (Nephrology)',              12, 1200, 'Room 210, 2nd floor', 'Treats chronic kidney disease and dialysis patients.'),
(3, 5,  3, 'MBBS, FCPS (Pediatrics)',             8, 1000, 'Room 101, Ground floor', 'Child specialist focused on newborn and child care.'),
(4, 6,  1, 'MBBS, MD (Neurology)',               18, 1600, 'Room 612, 6th floor', 'Neurologist treating stroke, epilepsy and headache.'),
(5, 7,  4, 'BDS, MS (Orthodontics)',              6,  700, 'Chamber 3', 'Dental surgeon and orthodontist.'),
(6, 8,  5, 'MBBS, FCPS (Medicine)',              10,  800, 'Chamber 7', 'Medicine specialist for diabetes, BP and general illness.'),
(7, 9,  6, 'MBBS, DDV (Dermatology)',             7,  900, 'Room 305', 'Skin, hair and nail problems.'),
(8, 10, 2, 'MBBS, MS (Orthopedics)',             20, 1500, 'Room 401, 4th floor', 'Joint replacement and sports injury surgeon.');

INSERT INTO doctor_category (doctor_id, category_id) VALUES
(1, 1), (2, 2), (3, 4), (4, 3), (5, 6), (6, 5), (6, 1), (7, 7), (8, 9);

-- 0 = Sunday ... 6 = Saturday
INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, slot_minutes) VALUES
(1, 6, '10:00', '13:00', 15), (1, 0, '17:00', '21:00', 15), (1, 2, '17:00', '21:00', 15),
(2, 0, '16:00', '20:00', 20), (2, 1, '16:00', '20:00', 20), (2, 3, '16:00', '20:00', 20),
(3, 6, '09:00', '12:00', 15), (3, 1, '18:00', '21:00', 15), (3, 4, '18:00', '21:00', 15),
(4, 0, '15:00', '19:00', 20), (4, 2, '15:00', '19:00', 20),
(5, 6, '16:00', '21:00', 30), (5, 1, '16:00', '21:00', 30), (5, 3, '16:00', '21:00', 30),
(6, 0, '17:00', '22:00', 15), (6, 1, '17:00', '22:00', 15), (6, 2, '17:00', '22:00', 15), (6, 3, '17:00', '22:00', 15), (6, 4, '17:00', '22:00', 15),
(7, 6, '15:00', '19:00', 15), (7, 2, '15:00', '19:00', 15),
(8, 1, '10:00', '14:00', 20), (8, 4, '10:00', '14:00', 20);
