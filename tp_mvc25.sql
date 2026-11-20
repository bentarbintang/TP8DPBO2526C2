-- =====================================================
-- Database : tp_mvc25
-- Tabel    : departments, lecturers, courses
-- Relasi   : departments 1 --- N lecturers 1 --- N courses
-- Import   : phpMyAdmin (Laragon) -> tab Import -> pilih file ini
-- =====================================================

CREATE DATABASE IF NOT EXISTS tp_mvc25
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE tp_mvc25;

-- Hapus tabel lama (urutan dari tabel anak ke tabel induk)
-- supaya file ini aman diimpor ulang.
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS lecturers;
DROP TABLE IF EXISTS departments;

-- =====================================================
-- Tabel: departments (BARU) - program studi
-- =====================================================
CREATE TABLE departments (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code      VARCHAR(10)  NOT NULL,
    name      VARCHAR(100) NOT NULL,
    head_name VARCHAR(100) NOT NULL,
    building  VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_departments_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabel: lecturers (LAMA, atribut ditambah)
-- Atribut lama : id, name, nidn, phone, join_date
-- Atribut baru : email, academic_rank, department_id (FK)
-- =====================================================
CREATE TABLE lecturers (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    nidn          VARCHAR(10)  NOT NULL,
    email         VARCHAR(100) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    academic_rank ENUM('Asisten Ahli','Lektor','Lektor Kepala','Profesor')
                  NOT NULL DEFAULT 'Asisten Ahli',
    join_date     DATE         NOT NULL,
    department_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_lecturers_nidn (nidn),
    UNIQUE KEY uq_lecturers_email (email),
    KEY idx_lecturers_department (department_id),
    CONSTRAINT fk_lecturers_department
        FOREIGN KEY (department_id) REFERENCES departments (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabel: courses (BARU) - mata kuliah
-- =====================================================
CREATE TABLE courses (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code        VARCHAR(10)  NOT NULL,
    name        VARCHAR(150) NOT NULL,
    credits     TINYINT UNSIGNED NOT NULL,
    semester    TINYINT UNSIGNED NOT NULL,
    lecturer_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courses_code (code),
    KEY idx_courses_lecturer (lecturer_id),
    CONSTRAINT chk_courses_credits CHECK (credits BETWEEN 1 AND 6),
    CONSTRAINT chk_courses_semester CHECK (semester BETWEEN 1 AND 8),
    CONSTRAINT fk_courses_lecturer
        FOREIGN KEY (lecturer_id) REFERENCES lecturers (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Data dummy: departments (4 baris)
-- =====================================================
INSERT INTO departments (code, name, head_name, building) VALUES
('IF',  'Informatika',              'Dr. Rina Kusumawati, M.T.',     'Gedung Ilmu Komputer Lt. 3'),
('SI',  'Sistem Informasi',         'Dr. Hendra Gunawan, M.Kom.',    'Gedung Ilmu Komputer Lt. 2'),
('TE',  'Teknik Elektro',           'Prof. Bambang Sutrisno, Ph.D.', 'Gedung Teknik B'),
('DKV', 'Desain Komunikasi Visual', 'Ayu Lestari, M.Ds.',            'Gedung Seni dan Desain');

-- =====================================================
-- Data dummy: lecturers (8 baris)
-- =====================================================
INSERT INTO lecturers (name, nidn, email, phone, academic_rank, join_date, department_id) VALUES
('Dr. Rina Kusumawati, M.T.',     '0412058801', 'rina.kusumawati@kampus.ac.id',  '081234567801', 'Lektor Kepala', '2012-08-15', 1),
('Budi Santoso, M.Kom.',          '0420039002', 'budi.santoso@kampus.ac.id',     '081234567802', 'Lektor',        '2016-02-01', 1),
('Siti Nurhaliza, M.Cs.',         '0405069203', 'siti.nurhaliza@kampus.ac.id',   '081234567803', 'Asisten Ahli',  '2020-09-01', 1),
('Dr. Hendra Gunawan, M.Kom.',    '0411078504', 'hendra.gunawan@kampus.ac.id',   '081234567804', 'Lektor Kepala', '2010-03-10', 2),
('Dewi Anggraini, M.M.',          '0417098805', 'dewi.anggraini@kampus.ac.id',   '081234567805', 'Lektor',        '2015-08-17', 2),
('Prof. Bambang Sutrisno, Ph.D.', '0402127506', 'bambang.sutrisno@kampus.ac.id', '081234567806', 'Profesor',      '2005-01-20', 3),
('Agus Prasetyo, M.T.',           '0422119007', 'agus.prasetyo@kampus.ac.id',    '081234567807', 'Asisten Ahli',  '2019-02-11', 3),
('Ayu Lestari, M.Ds.',            '0409058908', 'ayu.lestari@kampus.ac.id',      '081234567808', 'Lektor',        '2017-07-03', 4);

-- =====================================================
-- Data dummy: courses (12 baris)
-- =====================================================
INSERT INTO courses (code, name, credits, semester, lecturer_id) VALUES
('IF101',  'Algoritma dan Pemrograman',             4, 1, 3),
('IF201',  'Struktur Data',                         3, 3, 2),
('IF305',  'Desain dan Pemrograman Berbasis Objek', 3, 4, 2),
('IF410',  'Pemrograman Web',                       3, 4, 3),
('IF402',  'Kecerdasan Buatan',                     3, 5, 1),
('SI203',  'Basis Data',                            3, 3, 4),
('SI301',  'Analisis dan Perancangan Sistem',       3, 5, 4),
('SI405',  'Manajemen Proyek TI',                   2, 7, 5),
('TE102',  'Rangkaian Listrik',                     3, 2, 6),
('TE210',  'Elektronika Dasar',                     3, 3, 7),
('TE301',  'Sistem Kendali',                        3, 5, 7),
('DKV204', 'Tipografi',                             2, 3, 8);
