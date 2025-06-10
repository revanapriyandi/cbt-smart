mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: cbt_smart
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ci_sessions` (
  `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ci_sessions`
--

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `level` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `capacity` int unsigned NOT NULL DEFAULT '30',
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `academic_year` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `homeroom_teacher_id` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homeroom_teacher_id` (`homeroom_teacher_id`),
  CONSTRAINT `classes_homeroom_teacher_id_foreign` FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (2,'Cleo Carver','11',2,'Aut provident ad qu',0,'2024/2025',NULL,'2025-06-09 00:06:49','2025-06-09 00:35:02'),(4,'Kelas 7A','Grade 7',30,'Kelas 7A untuk tahun ajaran 2024-2025',1,'2024-2025',2,'2025-06-09 00:13:17','2025-06-09 00:13:17'),(5,'Kelas 7B','Grade 7',30,'Kelas 7B untuk tahun ajaran 2024-2025',1,'2024-2025',17,'2025-06-09 00:13:17','2025-06-09 00:13:17'),(6,'Kelas 8A','Grade 8',28,'Kelas 8A untuk tahun ajaran 2024-2025',1,'2024-2025',2,'2025-06-09 00:13:17','2025-06-09 00:13:17'),(7,'Kelas 8B','Grade 8',25,'Kelas 8B untuk tahun ajaran 2024-2025',0,'2024-2025',17,'2025-06-09 00:13:17','2025-06-09 00:13:17'),(8,'Kelas 9A','Grade 9',32,'Kelas 9A untuk tahun ajaran 2024-2025',1,'2024-2025',2,'2025-06-09 00:13:17','2025-06-09 00:13:17'),(9,'Sharon Cochran','11',3,'Ducimus et ipsum fa',0,'2024/2025',NULL,'2025-06-09 00:36:09','2025-06-09 00:36:09');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_activity_logs`
--

DROP TABLE IF EXISTS `exam_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int unsigned NOT NULL,
  `student_id` int unsigned NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_activity_logs`
--

LOCK TABLES `exam_activity_logs` WRITE;
/*!40000 ALTER TABLE `exam_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_participants`
--

DROP TABLE IF EXISTS `exam_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_participants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `exam_session_id` int unsigned NOT NULL,
  `exam_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `status` enum('not_started','in_progress','completed','absent') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'not_started',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `submission_time` datetime DEFAULT NULL,
  `total_time_spent` int unsigned DEFAULT NULL COMMENT 'Time spent in seconds',
  `score` decimal(5,2) DEFAULT NULL,
  `total_questions` int unsigned DEFAULT NULL,
  `answered_questions` int unsigned NOT NULL DEFAULT '0',
  `correct_answers` int unsigned NOT NULL DEFAULT '0',
  `wrong_answers` int unsigned NOT NULL DEFAULT '0',
  `unanswered_questions` int unsigned NOT NULL DEFAULT '0',
  `is_force_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `browser_info` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_session_id_user_id` (`exam_session_id`,`user_id`),
  KEY `exam_session_id` (`exam_session_id`),
  KEY `exam_id` (`exam_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `exam_participants_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_participants_exam_session_id_foreign` FOREIGN KEY (`exam_session_id`) REFERENCES `exam_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_participants`
--

LOCK TABLES `exam_participants` WRITE;
/*!40000 ALTER TABLE `exam_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_questions`
--

DROP TABLE IF EXISTS `exam_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int unsigned NOT NULL,
  `question_number` int NOT NULL,
  `question_text` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `max_score` decimal(5,2) NOT NULL DEFAULT '10.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_questions_exam_id_foreign` (`exam_id`),
  CONSTRAINT `exam_questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_questions`
--

LOCK TABLES `exam_questions` WRITE;
/*!40000 ALTER TABLE `exam_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_results`
--

DROP TABLE IF EXISTS `exam_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_results` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int unsigned NOT NULL,
  `student_id` int unsigned NOT NULL,
  `total_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `max_total_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('ongoing','submitted','graded') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ongoing',
  `started_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_id_student_id` (`exam_id`,`student_id`),
  KEY `exam_results_student_id_foreign` (`student_id`),
  CONSTRAINT `exam_results_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_results`
--

LOCK TABLES `exam_results` WRITE;
/*!40000 ALTER TABLE `exam_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_sessions`
--

DROP TABLE IF EXISTS `exam_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int unsigned NOT NULL,
  `class_id` int unsigned NOT NULL,
  `session_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `max_participants` int unsigned NOT NULL DEFAULT '50',
  `room_location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_general_ci,
  `security_settings` json DEFAULT NULL,
  `status` enum('scheduled','active','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'scheduled',
  `actual_start_time` datetime DEFAULT NULL,
  `actual_end_time` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_id` (`exam_id`),
  KEY `class_id` (`class_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `exam_sessions_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_sessions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `exam_sessions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_sessions`
--

LOCK TABLES `exam_sessions` WRITE;
/*!40000 ALTER TABLE `exam_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_types`
--

DROP TABLE IF EXISTS `exam_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `duration_minutes` int unsigned NOT NULL DEFAULT '60',
  `max_attempts` int unsigned NOT NULL DEFAULT '1',
  `passing_score` decimal(5,2) NOT NULL DEFAULT '60.00',
  `show_result_immediately` tinyint(1) NOT NULL DEFAULT '1',
  `allow_review` tinyint(1) NOT NULL DEFAULT '1',
  `randomize_questions` tinyint(1) NOT NULL DEFAULT '0',
  `randomize_options` tinyint(1) NOT NULL DEFAULT '0',
  `auto_submit` tinyint(1) NOT NULL DEFAULT '1',
  `instructions` text COLLATE utf8mb4_general_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `exam_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `exam_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_types`
--

LOCK TABLES `exam_types` WRITE;
/*!40000 ALTER TABLE `exam_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `subject_id` int unsigned NOT NULL,
  `exam_type_id` int unsigned DEFAULT NULL,
  `teacher_id` int unsigned NOT NULL,
  `pdf_url` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `pdf_content` longtext COLLATE utf8mb4_general_ci,
  `question_count` int NOT NULL DEFAULT '1',
  `total_questions` int unsigned DEFAULT '0',
  `duration` int unsigned DEFAULT '60',
  `duration_minutes` int NOT NULL DEFAULT '60',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exams_subject_id_foreign` (`subject_id`),
  KEY `exams_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `exams_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exams_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES (1,'Consectetur nesciun','Pariatur In non eni',1,NULL,2,'http://cbt-smart.test/uploads/pdfs/exam_1749294232_68441c9853293.pdf',NULL,14,0,60,7,'2025-05-07 20:26:00','2025-06-07 21:18:00',1,'2025-06-07 11:03:54','2025-06-07 22:40:37');
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025-06-06-191832','App\\Database\\Migrations\\CreateUsersTable','default','App',1749240011,1),(2,'2025-06-06-191838','App\\Database\\Migrations\\CreateSubjectsTable','default','App',1749240011,1),(3,'2025-06-06-191842','App\\Database\\Migrations\\CreateExamsTable','default','App',1749240011,1),(4,'2025-06-06-191846','App\\Database\\Migrations\\CreateExamQuestionsTable','default','App',1749240011,1),(5,'2025-06-06-191852','App\\Database\\Migrations\\CreateStudentAnswersTable','default','App',1749240011,1),(6,'2025-06-06-191856','App\\Database\\Migrations\\CreateExamResultsTable','default','App',1749240012,1),(7,'2025-06-06-203500','App\\Database\\Migrations\\CreateExamActivityLogs','default','App',1749331319,2),(8,'2025-06-07-212114','App\\Database\\Migrations\\UserActivityLogs','default','App',1749331319,2),(9,'2025-06-08-005258','App\\Database\\Migrations\\AddLastLoginToUsers','default','App',1749343999,3),(10,'2025-06-08-023638','App\\Database\\Migrations\\CreateSecuritySettingsTable','default','App',1749350326,4),(11,'2025-06-08-023648','App\\Database\\Migrations\\CreateSecuritySettingsTable','default','App',1749350349,5),(12,'2025-06-08-025249','App\\Database\\Migrations\\CreateSystemSettingsTable','default','App',1749351267,6),(13,'2025-06-08-034013','App\\Database\\Migrations\\CreateSessionsTable','default','App',1749354039,7),(15,'2025-06-08-103453','App\\Database\\Migrations\\CreateClassesTable','default','App',1749379305,8),(16,'2025-06-08-103501','App\\Database\\Migrations\\CreateAcademicYearsTable','default','App',1749379306,8),(17,'2025-06-08-103512','App\\Database\\Migrations\\CreateExamTypesTable','default','App',1749379306,8),(18,'2025-06-08-103520','App\\Database\\Migrations\\CreateQuestionBanksTable','default','App',1749379307,8),(19,'2025-06-08-103529','App\\Database\\Migrations\\CreateSchedulesTable','default','App',1749379309,8),(20,'2025-06-08-103547','App\\Database\\Migrations\\CreateExamSessionsTable','default','App',1749379311,8),(21,'2025-06-08-103554','App\\Database\\Migrations\\CreateExamParticipantsTable','default','App',1749379311,8),(22,'2025-06-08-104436','App\\Database\\Migrations\\AddExamTypeIdToExamsTable','default','App',1749379495,9),(23,'2025-06-08-104537','App\\Database\\Migrations\\AddMissingColumnsToExamsTable','default','App',1749379662,10),(24,'2025-06-08-222755','App\\Database\\Migrations\\CreateUserClassesTable','default','App',1749421719,11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_banks`
--

DROP TABLE IF EXISTS `question_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_banks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `subject_id` int unsigned NOT NULL,
  `exam_type_id` int unsigned DEFAULT NULL,
  `difficulty_level` enum('easy','medium','hard') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'medium',
  `description` text COLLATE utf8mb4_general_ci,
  `instructions` text COLLATE utf8mb4_general_ci,
  `time_per_question` int unsigned DEFAULT NULL,
  `negative_marking` tinyint(1) NOT NULL DEFAULT '0',
  `negative_marks` decimal(5,2) NOT NULL DEFAULT '0.00',
  `randomize_questions` tinyint(1) NOT NULL DEFAULT '0',
  `show_correct_answer` tinyint(1) NOT NULL DEFAULT '1',
  `allow_calculator` tinyint(1) NOT NULL DEFAULT '0',
  `tags` json DEFAULT NULL,
  `status` enum('active','inactive','draft') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_banks_updated_by_foreign` (`updated_by`),
  KEY `subject_id` (`subject_id`),
  KEY `exam_type_id` (`exam_type_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `question_banks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `question_banks_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `question_banks_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `question_banks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_banks`
--

LOCK TABLES `question_banks` WRITE;
/*!40000 ALTER TABLE `question_banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int unsigned NOT NULL,
  `class_id` int unsigned NOT NULL,
  `subject_id` int unsigned NOT NULL,
  `teacher_id` int unsigned NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') COLLATE utf8mb4_general_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` int unsigned NOT NULL COMMENT 'Duration in minutes',
  `room` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_created_by_foreign` (`created_by`),
  KEY `schedules_updated_by_foreign` (`updated_by`),
  KEY `academic_year_id` (`academic_year_id`),
  KEY `class_id` (`class_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `schedules_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `schedules_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `schedules_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `schedules_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `schedules_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedules`
--

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_settings`
--

DROP TABLE IF EXISTS `security_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('general','password','session','network','blocked_ips') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'general',
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_settings`
--

LOCK TABLES `security_settings` WRITE;
/*!40000 ALTER TABLE `security_settings` DISABLE KEYS */;
INSERT INTO `security_settings` VALUES (1,'two_factor_required','0','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(2,'password_reset_required','0','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(3,'account_lockout_enabled','1','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(4,'max_login_attempts','5','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(5,'lockout_duration','900','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(6,'ip_whitelist_enabled','0','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(7,'maintenance_mode','0','general',NULL,'2025-06-08 02:39:34','2025-06-08 03:40:55'),(8,'min_length','8','password','Minimum password length','2025-06-08 02:39:34','2025-06-08 02:39:34'),(9,'require_uppercase','1','password','Require uppercase letters','2025-06-08 02:39:34','2025-06-08 02:39:34'),(10,'require_lowercase','1','password','Require lowercase letters','2025-06-08 02:39:34','2025-06-08 02:39:34'),(11,'require_numbers','1','password','Require numbers','2025-06-08 02:39:34','2025-06-08 02:39:34'),(12,'require_symbols','0','password','Require special symbols','2025-06-08 02:39:34','2025-06-08 02:39:34'),(13,'password_history','5','password','Remember last N passwords','2025-06-08 02:39:34','2025-06-08 02:39:34'),(14,'password_expiry_days','90','password','Password expires after N days','2025-06-08 02:39:34','2025-06-08 02:39:34'),(15,'session_timeout','7200','session','Session timeout in seconds','2025-06-08 02:39:34','2025-06-08 02:39:34'),(16,'idle_timeout','1800','session','Idle timeout in seconds','2025-06-08 02:39:34','2025-06-08 02:39:34'),(17,'concurrent_sessions','3','session','Maximum concurrent sessions','2025-06-08 02:39:34','2025-06-08 02:39:34'),(18,'remember_me_duration','2592000','session','Remember me duration in seconds','2025-06-08 02:39:34','2025-06-08 02:39:34'),(19,'secure_cookies','1','session','Use secure cookies','2025-06-08 02:39:34','2025-06-08 02:39:34'),(20,'force_logout_on_password_change','1','session','Force logout when password changes','2025-06-08 02:39:34','2025-06-08 02:39:34');
/*!40000 ALTER TABLE `security_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_answers`
--

DROP TABLE IF EXISTS `student_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_answers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int unsigned NOT NULL,
  `student_id` int unsigned NOT NULL,
  `question_number` int NOT NULL,
  `answer_text` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `ai_score` decimal(5,2) DEFAULT NULL,
  `ai_feedback` longtext COLLATE utf8mb4_general_ci,
  `manual_score` decimal(5,2) DEFAULT NULL,
  `manual_feedback` longtext COLLATE utf8mb4_general_ci,
  `final_score` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_answers_exam_id_foreign` (`exam_id`),
  KEY `student_answers_student_id_foreign` (`student_id`),
  CONSTRAINT `student_answers_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `student_answers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_answers`
--

LOCK TABLES `student_answers` WRITE;
/*!40000 ALTER TABLE `student_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `teacher_id` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `subjects_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'Matematka','MTK001','',2,'2025-06-07 10:44:09','2025-06-07 22:19:40'),(2,'Tester2','MTK0012','',NULL,'2025-06-07 20:42:48','2025-06-07 22:40:25');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `setting_type` enum('string','integer','boolean','json') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'string',
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'general',
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `category_setting_key` (`category`,`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'site_name','CBT Smart System','string','general','Name of the website/system','2025-06-08 02:54:36','2025-06-08 02:54:36'),(2,'site_description','Computer Based Testing System','string','general','Description of the website/system','2025-06-08 02:54:36','2025-06-08 02:54:36'),(3,'admin_email','admin@example.com','string','general','Primary admin email address','2025-06-08 02:54:36','2025-06-08 02:54:36'),(4,'timezone','Asia/Jakarta','string','general','System timezone','2025-06-08 02:54:36','2025-06-08 02:54:36'),(5,'date_format','Y-m-d','string','general','Date display format','2025-06-08 02:54:36','2025-06-08 02:54:36'),(6,'smtp_host','smtp.gmail.com','string','email','SMTP server hostname','2025-06-08 02:54:36','2025-06-08 02:54:36'),(7,'smtp_port','587','integer','email','SMTP server port','2025-06-08 02:54:36','2025-06-08 02:54:36'),(8,'smtp_username','','string','email','SMTP username','2025-06-08 02:54:36','2025-06-08 02:54:36'),(9,'smtp_password','','string','email','SMTP password','2025-06-08 02:54:36','2025-06-08 02:54:36'),(10,'smtp_encryption','tls','string','email','SMTP encryption method','2025-06-08 02:54:36','2025-06-08 02:54:36'),(11,'from_email','noreply@example.com','string','email','Default from email address','2025-06-08 02:54:36','2025-06-08 02:54:36'),(12,'from_name','CBT Smart System','string','email','Default from name','2025-06-08 02:54:36','2025-06-08 02:54:36'),(13,'default_exam_duration','120','integer','exam','Default exam duration in minutes','2025-06-08 02:54:36','2025-06-08 02:54:36'),(14,'auto_submit_buffer','5','integer','exam','Auto submit buffer time in minutes','2025-06-08 02:54:36','2025-06-08 02:54:36'),(15,'passing_score','60','integer','exam','Default passing score percentage','2025-06-08 02:54:36','2025-06-08 02:54:36'),(16,'max_attempts','1','integer','exam','Maximum exam attempts allowed','2025-06-08 02:54:36','2025-06-08 02:54:36'),(17,'allow_review','1','boolean','exam','Allow students to review questions','2025-06-08 02:54:36','2025-06-08 02:54:36'),(18,'shuffle_questions','0','boolean','exam','Shuffle questions by default','2025-06-08 02:54:36','2025-06-08 02:54:36'),(19,'email_notifications','1','boolean','notification','Enable email notifications','2025-06-08 02:54:36','2025-06-08 02:54:36'),(20,'exam_start_notification','1','boolean','notification','Send notification when exam starts','2025-06-08 02:54:36','2025-06-08 02:54:36'),(21,'exam_end_notification','1','boolean','notification','Send notification when exam completes','2025-06-08 02:54:36','2025-06-08 02:54:36'),(22,'user_registration_notification','1','boolean','notification','Send notification for new user registrations','2025-06-08 02:54:36','2025-06-08 02:54:36'),(23,'maintenance_mode','0','boolean','maintenance','Enable maintenance mode','2025-06-08 02:54:36','2025-06-08 02:54:36'),(24,'maintenance_message','System is under maintenance. Please try again later.','string','maintenance','Maintenance mode message','2025-06-08 02:54:36','2025-06-08 02:54:36'),(25,'backup_retention_days','30','integer','maintenance','Days to retain backup files','2025-06-08 02:54:36','2025-06-08 02:54:36'),(26,'log_retention_days','90','integer','maintenance','Days to retain log files','2025-06-08 02:54:36','2025-06-08 02:54:36');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activity_logs`
--

DROP TABLE IF EXISTS `user_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `activity_description` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_type_created_at` (`activity_type`,`created_at`),
  CONSTRAINT `user_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity_logs`
--

LOCK TABLES `user_activity_logs` WRITE;
/*!40000 ALTER TABLE `user_activity_logs` DISABLE KEYS */;
INSERT INTO `user_activity_logs` VALUES (2,5,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-07 21:31:13'),(8,14,'login','User logged in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','2025-06-07 23:21:17'),(9,14,'login','User logged in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','2025-06-07 23:25:51'),(17,17,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:00:58'),(18,17,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:01:10'),(19,17,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:03:08'),(20,17,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:03:14'),(21,20,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:06:00'),(22,21,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:09:19'),(23,21,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:09:25'),(24,21,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:12:10'),(25,21,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:12:10'),(26,22,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 01:12:27'),(27,14,'login','User logged in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 10:20:06'),(28,14,'login','User logged in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 22:10:11'),(29,14,'user_update','Updated user: tykuco','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 22:21:39'),(30,22,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 22:21:39'),(31,14,'user_update','Updated user: tykuco','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 22:23:08'),(32,22,'profile_update','Profile updated by administrator','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 22:23:09'),(33,14,'login','User logged in successfully','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','2025-06-08 23:42:57'),(34,14,'class_create',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-08 23:54:52'),(35,14,'class_bulk_action',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-09 00:01:23'),(36,14,'class_delete',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-09 00:06:32'),(37,14,'class_create',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-09 00:06:49'),(38,14,'class_delete',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-09 00:32:22'),(39,14,'class_bulk_action',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-09 00:35:02'),(40,14,'class_create',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36','2025-06-09 00:36:09');
/*!40000 ALTER TABLE `user_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_classes`
--

DROP TABLE IF EXISTS `user_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_classes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `class_id` int unsigned NOT NULL,
  `enrolled_at` datetime DEFAULT NULL,
  `status` enum('active','inactive','transferred') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_class_id` (`user_id`,`class_id`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `user_classes_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_classes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_classes`
--

LOCK TABLES `user_classes` WRITE;
/*!40000 ALTER TABLE `user_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','teacher','student') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'student',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'teacher1','teacher@cbt.com','$2y$10$hsWnxOCBHJWNPJl.2YOo/ONA/bwBcvhek68vR91MmYtqxmo7B3DeO','Guru Matematika','teacher',1,NULL,'2025-06-06 20:00:46','2025-06-06 20:00:46'),(5,'student3','student3@cbt.com','$2y$10$PyRfMUL5plhkFh0DsbtNfepj9dH4xBtJwg4vbR7E/.EriEGk0YI0q','Siswa 3','student',1,NULL,'2025-06-06 20:00:47','2025-06-07 21:31:13'),(11,'mizum','ricakoceho@mailinator.com','$2y$10$fmmA7XmF9OQ38iTZiQJEo.QKPo66LNcY97HWOqyuxttEEPJg6V9/e','Tanya Nichols','admin',1,NULL,'2025-06-07 22:24:17','2025-06-07 22:24:17'),(12,'xyjazemep','myhe@mailinator.com','$2y$10$5s14fNNGAn9a/D2EJkYWvuRBDvAbgihxZPoEcu6VGxWGyIYAzD9bC','Sawyer Russell','admin',1,NULL,'2025-06-07 22:56:31','2025-06-07 22:56:31'),(13,'jujaquzig','nygokito@mailinator.com','$2y$10$gps3AngbXMus8QN8Kw/NFubNUE.39aFzuyo1qh1xihmO8vx3eKyui','Rosalyn Hampton','student',1,NULL,'2025-06-07 23:12:58','2025-06-07 23:12:58'),(14,'admin','admin@cbt.com','$2y$10$x6NMXCj3040CkWrp7lvzGuGAwZ8yapf1nBA1SKNboT8H6F/YWiKsK','Administrator','admin',1,'2025-06-08 23:42:57','2025-06-07 23:19:51','2025-06-08 23:42:57'),(16,'toleqary','dapakif@mailinator.com','$2y$10$bb2ZXyePp1ZJFr7dfebCMOmGMd7.3Fx4yrSkIE3F.Tn.1apnKzNQK','Indira Ballarda','admin',1,NULL,'2025-06-07 23:40:19','2025-06-07 23:42:55'),(17,'sylafi','qyboku@mailinator.com','$2y$10$7Rjr5ACJYqaHxwyK/W912Oiv6wbiIYCvH86O2VTWNHNNiEiNB2am2','Emerald Schultz','teacher',1,NULL,'2025-06-07 23:43:00','2025-06-08 01:03:14'),(19,'lopuve','bovemy@mailinator.com','$2y$10$SAASy.K11iR2szQJgWP0/OmaCHeEGoJ0oZFcACprLTnKSk5eo8RNy','Myra Wade','teacher',0,NULL,'2025-06-08 01:03:37','2025-06-08 01:03:37'),(20,'qyxoduho','tupo@mailinator.com','$2y$10$mbgKuzXuqSJKZzoykFl2buLMH1Wf2P5kAC55Mg3U4DcWHZudr.cyu','Ursula Gomez','admin',1,NULL,'2025-06-08 01:05:48','2025-06-08 01:06:00'),(21,'cuzawuxevo','bydusebi@mailinator.com','$2y$10$UnP1xF8c221Hxq/D.P7kV.B/CH9AzoyVjFkJzDx9qDOTen9ahRhSm','Quinn Gillespie','teacher',0,NULL,'2025-06-08 01:07:27','2025-06-08 01:12:10'),(22,'tykuco','husicif@mailinator.com','$2y$10$tkXQZrqkf2XVzQVsdmrwxeQMCCkCVunKVBsPSZabEhv/e8TmedfRG','Leslie Pierce','student',1,NULL,'2025-06-08 01:12:17','2025-06-08 22:23:07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'cbt_smart'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-09  7:40:10
