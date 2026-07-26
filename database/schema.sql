-- CMSR-TZ content management schema
-- Import into the `cmsr_tz` database (already created in XAMPP/phpMyAdmin):
--   mysql -u root cmsr_tz < database/schema.sql
-- or via phpMyAdmin: select cmsr_tz -> Import -> choose this file.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Staff accounts
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Admin','Editor') NOT NULL DEFAULT 'Editor',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin login: admin / 12345678 (change the password after first login).
-- Hash below is password_hash('12345678', PASSWORD_DEFAULT).
INSERT INTO users (username, password_hash, role) VALUES
  ('admin', '$2y$12$qBmhn2qyltIYrX11MzCS3.WLREzWoFLpCLUfXPPLKDaQOuJrknOFG', 'Admin')
ON DUPLICATE KEY UPDATE username = username;

-- ---------------------------------------------------------------------
-- Photo archive — every image the admin has on hand for reuse across sections
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS archive (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  url VARCHAR(500) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO archive (name, url) VALUES
  ('CMSR-TZ community overview', 'photos/cover/cmsr-tz-overview.jpeg'),
  ('PMHM Kondoa overview', 'photos/cover/pmhm-kondoa-overview.jpeg'),
  ('Rulenge women entrepreneurship', 'photos/rulenge/rulenge-women-entrepreneurship.jpeg'),
  ('Rulenge classroom office', 'photos/cover/rulenge-classroom-office.jpeg'),
  ('Sekondari ya Kilimo overview', 'photos/agriculture/sekondari-overview.jpeg'),
  ('PMHM students health', 'photos/cover/pmhm-students-health.jpeg'),
  ('IMaNHC overview', 'photos/health/imanHC/imanHC-overview.jpeg'),
  ('Wela Lukundo schools', 'photos/education/wela-lukundo-schools-1.png'),
  ('SWALA kitenge products', 'photos/women-empowerment/swala-kitenge-products.jpeg'),
  ('Education activity', 'photos/education/education-activity-2.png'),
  ('PMHM storage tank', 'photos/health/pmhm/pmhm-storage-tank-1.jpeg');

-- ---------------------------------------------------------------------
-- Homepage hero carousel
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS slideshow (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(500) NOT NULL,
  eyebrow VARCHAR(255) DEFAULT '',
  heading VARCHAR(255) NOT NULL,
  description TEXT,
  btn1_text VARCHAR(100) DEFAULT '',
  btn1_link VARCHAR(255) DEFAULT '',
  btn2_text VARCHAR(100) DEFAULT '',
  btn2_link VARCHAR(255) DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO slideshow (image, eyebrow, heading, description, btn1_text, btn1_link, btn2_text, btn2_link, sort_order) VALUES
  ('photos/health/pmhm/pmhm-storage-tank-1.jpeg', 'Water &amp; Sanitation', 'Water and Sanitation', 'Deep water wells, solar-powered pumps and water distribution stations built in Rulenge, Kagera Region, giving communities lasting access to clean water and improved sanitation.', 'Learn More', 'water-sanitation.html', 'Support Water Projects', 'donate.html', 1),
  ('photos/health/pmhm/pmhm-students-health.jpeg', 'Health', 'Health', 'Promoting Menstrual Health Management (PMHM) in 14 secondary schools and Maternal &amp; Neonatal Health Care (IMaNHC) across Dodoma and Zanzibar.', 'Learn More', 'health.html', 'Support Health', 'donate.html', 2),
  ('photos/education/school-visit-1.jpeg', 'Education &#8211; Shule Program', 'Education', 'Since 2001, our Shule Program has supported 245+ students across 8 secondary schools in Dodoma Region with school fees, materials and mentorship from Italian donors.', 'Learn More', 'education.html', 'Support a Student', 'donate.html', 3),
  ('photos/agriculture/sekondari-overview.jpeg', 'Agriculture & Livelihoods', 'Agriculture', 'Sekondari ya Kilimo project providing construction, solar energy systems, and water infrastructure for sustainable agriculture development.', 'Learn More', 'agriculture.html', 'Support Agriculture', 'donate.html', 4),
  ('photos/rulenge/rulenge-juvenile-justice.jpeg', 'Youth Empowerment', 'Youth Empowerment', 'Juvenile justice studies and gender &amp; minor rights training in Rulenge, Kagera Region, equipping young people with the skills and knowledge to build safer, more independent futures.', 'Learn More', 'youth-empowerment.html', 'Support Youth Programs', 'donate.html', 5);

-- ---------------------------------------------------------------------
-- Homepage "About" block (single record)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS overview (
  id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
  image VARCHAR(500) DEFAULT '',
  heading VARCHAR(255) DEFAULT '',
  paragraphs TEXT,
  CONSTRAINT single_row CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO overview (id, image, heading, paragraphs) VALUES (
  1,
  'photos/health/imanHC/imanHC-overview.jpeg',
  'Community Mobilisation for Reciprocal Development',
  'The Community Mobilisation for Reciprocal Development in Tanzania (CMSR-TZ) is a Non-Governmental Organization established in August 1997. We are registered under Certificate No. 00NGO/R1/00411 and based in Dodoma City, Tanzania.\nOur purpose is to complement and support the Government of Tanzania in implementing community development projects targeting rural populations to alleviate extreme poverty.\nWe work in partnership with local and international NGOs, Faith-Based Organizations, the Government of Tanzania, and local communities across four key sectors: Education, Health, Women Empowerment, and Agriculture.'
) ON DUPLICATE KEY UPDATE id = id;

-- ---------------------------------------------------------------------
-- Programs & Sectors cards
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS programs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(500) NOT NULL,
  category VARCHAR(100) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  link VARCHAR(255) DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO programs (image, category, title, description, link, sort_order) VALUES
  ('photos/education/wela-lukundo-schools-1.png', 'Education', 'Shule Program', 'Supporting 245+ students in Dodoma Region since 2001 with school fees, materials, and mentorship from Italian donors.', 'education.html', 1),
  ('photos/cover/pmhm-students-health.jpeg', 'Health', 'Health Programs', 'Promoting Menstrual Health Management (PMHM) and Maternal & Neonatal Health Care (IMaNHC) in Dodoma and Zanzibar.', 'health.html', 2),
  ('photos/women-empowerment/swala-kitenge-products.jpeg', 'Women Empowerment', 'SWALA Program', 'Economically empowering women in Chikopelo Bwawani through tailoring and production of handcrafted kitenge products since 2016.', 'women-empowerment.html', 3),
  ('photos/agriculture/sekondari-overview.jpeg', 'Agriculture', 'Agriculture & Livelihoods', 'Sekondari ya Kilimo and Rulenge projects providing agricultural training, water infrastructure and entrepreneurship in Kagera Region.', 'agriculture.html', 4);

-- ---------------------------------------------------------------------
-- Latest News & Updates cards + full article body for news-detail.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS news (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(500) NOT NULL,
  news_date VARCHAR(50) NOT NULL,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT,
  body TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO news (image, news_date, title, excerpt, body) VALUES
  ('photos/cover/cmsr-tz-overview.jpeg', 'December 31, 2025', 'CMSR-TZ Annual Report 2025 Released',
   'Our 2025 annual report covers successful implementation of the Shule Program, SWALA, PMHM, IMaNHC, and our Kagera agriculture and empowerment projects.',
   'Our 2025 annual report covers all program activities: Shule Program, SWALA, PMHM, IMaNHC, Rulenge project, and Sekondari ya Kilimo. 245+ students supported, water well completed in Kagera, and much more.\n\nThe report is available for download from the Resources page.'),
  ('photos/education/education-activity-2.png', 'August 2025', 'Shule Program: School Visits & Student Materials Distributed',
   'CMSR-TZ staff and Italian volunteers visited 7 secondary schools in Dodoma Region, distributing school materials to 245+ sponsored students.',
   'CMSR-TZ staff and Italian volunteers visited 7 secondary schools in Dodoma Region in August 2025, distributing materials to 245+ sponsored students and monitoring academic progress.'),
  ('photos/health/pmhm/pmhm-storage-tank-1.jpeg', 'December 2025', 'Water Well & Solar System Completed in Rulenge, Kagera',
   'Major milestone achieved as the deep water well, pump house, and solar-powered water distribution system were completed in Ngara District, Kagera Region.',
   'Major milestone achieved as the deep water well, pump house, and solar-powered water distribution system were completed in Ngara District, Kagera Region, as part of the comprehensive Rulenge project.');

-- ---------------------------------------------------------------------
-- Resources — publications and reference documents
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS resources (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(50) NOT NULL DEFAULT 'Other',
  file_link VARCHAR(500) DEFAULT '',
  description TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO resources (title, category, file_link, description) VALUES
  ('Shule Program Report', 'Publication', '', 'Detailed program report on the Shule distance education support programme, covering school visits, student achievements, home visits, and donor communication in 2025.'),
  ('SWALA Program Report', 'Publication', '', 'Documentation on the SWALA tailoring and women empowerment program in Chikopelo Bwawani, including production records and community impact.'),
  ('Rulenge Project Report', 'Publication', '', 'Full report on the Kagera Region Rulenge project, covering agricultural training, water infrastructure, solar systems, and community construction activities.');

-- ---------------------------------------------------------------------
-- Annual reports (Resources -> Annual Reports)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reports (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  year VARCHAR(10) NOT NULL,
  file_link VARCHAR(500) DEFAULT '',
  description TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO reports (title, year, file_link, description) VALUES
  ('CMSR-TZ Annual Report 2025', '2025', 'ANNUAL Final Reports -2025 -CMSR-TZ.docx', 'Comprehensive report covering all CMSR-TZ programs implemented January-December 2025: Shule Program, SWALA, PMHM, IMaNHC, Rulenge Project, and Sekondari ya Kilimo.');

-- ---------------------------------------------------------------------
-- Short announcements / in-brief updates
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS updates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  update_date VARCHAR(50) NOT NULL,
  title VARCHAR(255) NOT NULL,
  body TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
