-- Doctor Appointment System - database schema
-- Works on MySQL 8+ and MariaDB 10.4+
-- WARNING: running this file drops and recreates all tables.

CREATE DATABASE IF NOT EXISTS doctor_appointment
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE doctor_appointment;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctor_schedules;
DROP TABLE IF EXISTS doctor_category;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS hospitals;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Everyone who can log in: admin, doctor, patient
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role          ENUM('admin', 'doctor', 'patient') NOT NULL DEFAULT 'patient',
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    password      VARCHAR(255) NOT NULL,
    gender        ENUM('male', 'female', 'other') NULL,
    date_of_birth DATE NULL,
    address       VARCHAR(255) NULL,
    status        ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_phone (phone),
    KEY idx_users_role_status (role, status)
) ENGINE=InnoDB;

CREATE TABLE hospitals (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    address     VARCHAR(255) NOT NULL,
    city        VARCHAR(80)  NOT NULL,
    country     VARCHAR(80)  NOT NULL DEFAULT 'Bangladesh',
    phone       VARCHAR(20)  NULL,
    email       VARCHAR(150) NULL,
    description TEXT NULL,
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_hospitals_name_city (name, city),
    KEY idx_hospitals_city (city),
    KEY idx_hospitals_country (country),
    KEY idx_hospitals_status (status)
) ENGINE=InnoDB;

-- Doctor specializations, e.g. "Heart Specialist"
CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

-- Doctor profile. Login details (name, email, phone, password) are in users.
CREATE TABLE doctors (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED NOT NULL,
    hospital_id      INT UNSIGNED NOT NULL,
    qualification    VARCHAR(255) NOT NULL,
    experience_years TINYINT UNSIGNED NOT NULL DEFAULT 0,
    consultation_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
    chamber_info     VARCHAR(255) NULL,
    bio              TEXT NULL,
    image            VARCHAR(255) NULL,
    status           ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_doctors_user (user_id),
    KEY idx_doctors_hospital (hospital_id),
    KEY idx_doctors_status (status),
    CONSTRAINT fk_doctors_user     FOREIGN KEY (user_id)     REFERENCES users (id)     ON DELETE RESTRICT,
    CONSTRAINT fk_doctors_hospital FOREIGN KEY (hospital_id) REFERENCES hospitals (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- A doctor can have one or more specializations
CREATE TABLE doctor_category (
    doctor_id   INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (doctor_id, category_id),
    KEY idx_doctor_category_category (category_id),
    CONSTRAINT fk_dc_doctor   FOREIGN KEY (doctor_id)   REFERENCES doctors (id)    ON DELETE CASCADE,
    CONSTRAINT fk_dc_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Weekly schedule. day_of_week: 0 = Sunday ... 6 = Saturday
CREATE TABLE doctor_schedules (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doctor_id    INT UNSIGNED NOT NULL,
    day_of_week  TINYINT UNSIGNED NOT NULL,
    start_time   TIME NOT NULL,
    end_time     TIME NOT NULL,
    slot_minutes TINYINT UNSIGNED NOT NULL DEFAULT 15,
    status       ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_schedule_start (doctor_id, day_of_week, start_time),
    CONSTRAINT fk_schedules_doctor FOREIGN KEY (doctor_id) REFERENCES doctors (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE appointments (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_no   VARCHAR(20)  NOT NULL,
    patient_id       INT UNSIGNED NOT NULL,
    doctor_id        INT UNSIGNED NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    patient_name     VARCHAR(100) NOT NULL,
    patient_phone    VARCHAR(20)  NOT NULL,
    note             VARCHAR(500) NULL,
    fee              DECIMAL(10, 2) NOT NULL DEFAULT 0,
    status           ENUM('confirmed', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'confirmed',

    -- 1 while the appointment holds the slot, NULL once it is cancelled.
    -- UNIQUE allows many NULLs, so a cancelled slot can be booked again,
    -- but two active bookings for the same doctor + date + time are impossible.
    slot_lock        TINYINT GENERATED ALWAYS AS (IF(status = 'cancelled', NULL, 1)) STORED,

    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_appointment_no (appointment_no),
    UNIQUE KEY uq_doctor_slot (doctor_id, appointment_date, appointment_time, slot_lock),
    KEY idx_appointments_patient (patient_id, appointment_date),
    KEY idx_appointments_date_status (appointment_date, status),
    CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES users (id)   ON DELETE RESTRICT,
    CONSTRAINT fk_appointments_doctor  FOREIGN KEY (doctor_id)  REFERENCES doctors (id) ON DELETE RESTRICT
) ENGINE=InnoDB;
