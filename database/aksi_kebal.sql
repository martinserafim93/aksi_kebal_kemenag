-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: aksi_kebal
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `absensi`
--

DROP TABLE IF EXISTS `absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensi` (
  `id_absensi` int NOT NULL AUTO_INCREMENT,
  `kode_absensi` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kegiatan` int NOT NULL,
  `status_kehadiran` enum('Hadir','Tidak Hadir') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hadir',
  `latitude_absensi` decimal(10,8) DEFAULT NULL,
  `longitude_absensi` decimal(11,8) DEFAULT NULL,
  `jarak_meter` float DEFAULT NULL,
  `lokasi_valid` tinyint(1) DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_file_bukti` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absensi`),
  UNIQUE KEY `uk_absensi_pegawai_kegiatan` (`nip`,`id_kegiatan`),
  KEY `idx_absensi_nip` (`nip`),
  KEY `idx_absensi_kegiatan` (`id_kegiatan`),
  KEY `idx_absensi_created` (`created_at`),
  CONSTRAINT `fk_absensi_kegiatan` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id_kegiatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_absensi_pegawai` FOREIGN KEY (`nip`) REFERENCES `pegawai` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi`
--

LOCK TABLES `absensi` WRITE;
/*!40000 ALTER TABLE `absensi` DISABLE KEYS */;
/*!40000 ALTER TABLE `absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jabatan`
--

DROP TABLE IF EXISTS `jabatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jabatan` (
  `id_jabatan` int NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_jabatan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jabatan`
--

LOCK TABLES `jabatan` WRITE;
/*!40000 ALTER TABLE `jabatan` DISABLE KEYS */;
INSERT INTO `jabatan` VALUES (1,'Kabag TU','kabag-tu','2026-08-25 12:44:05','2026-08-25 12:51:47'),(2,'Pembimas Buddha','pembimas-buddha','2026-08-25 12:44:05','2026-08-25 12:51:47'),(3,'Kabid Haji dan Bimas Islam','kabid-haji-dan-bimas-islam','2026-08-25 12:44:05','2026-08-25 12:51:47'),(4,'Pembimas Katolik','pembimas-katolik','2026-08-25 12:44:05','2026-08-25 12:51:47'),(5,'Kabid Bimas Kristen','kabid-bimas-kristen','2026-08-25 12:44:05','2026-08-25 12:51:47'),(6,'Kabid Pendis','kabid-pendis','2026-08-25 12:44:05','2026-08-25 12:51:47'),(7,'Kakanwil','kakanwil','2026-08-25 12:44:05','2026-08-25 12:51:47'),(8,'Pembimbing Teknis Urusan Agama','pembimbing-teknis-urusan-agama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(9,'Penata Layanan Operasional','penata-layanan-operasional','2026-08-25 12:44:05','2026-08-25 12:51:47'),(10,'Penyuluh Agama Ahli Pertama','penyuluh-agama-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(11,'Operator Layanan Operasional','operator-layanan-operasional','2026-08-25 12:44:05','2026-08-25 12:51:47'),(12,'Analis Sumber Daya Manusia Aparatur Ahli Pertama','analis-sumber-daya-manusia-aparatur-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(13,'Pranata Hubungan Masyarakat Ahli Pertama','pranata-hubungan-masyarakat-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(14,'Pranata Komputer Ahli Pertama','pranata-komputer-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(15,'Analis Pengelolaan Keuangan APBN Ahli Madya','analis-pengelolaan-keuangan-apbn-ahli-madya','2026-08-25 12:44:05','2026-08-25 12:51:47'),(16,'Analis Pengelolaan Keuangan APBN Ahli Pertama','analis-pengelolaan-keuangan-apbn-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(17,'Penelaah Teknis Kebijakan','penelaah-teknis-kebijakan','2026-08-25 12:44:05','2026-08-25 12:51:47'),(18,'Penelaah Teknis Kebijakan ','penelaah-teknis-kebijakan-','2026-08-25 12:44:05','2026-08-25 12:51:47'),(19,'Pengelola Pengadaan Barang/Jasa Ahli Pertama','pengelola-pengadaan-barang/jasa-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(20,'Pranata Keuangan APBN Penyelia','pranata-keuangan-apbn-penyelia','2026-08-25 12:44:05','2026-08-25 12:51:47'),(21,'Pengadministrasi Perkantoran','pengadministrasi-perkantoran','2026-08-25 12:44:05','2026-08-25 12:51:47'),(22,'Analis Kebijakan Ahli Pertama','analis-kebijakan-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(23,'Pengembang Teknologi Pembelajaran Ahli Muda','pengembang-teknologi-pembelajaran-ahli-muda','2026-08-25 12:44:05','2026-08-25 12:51:47'),(24,'Stastisi Ahli Pertama','stastisi-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(25,'Arsiparis Ahli Pertama','arsiparis-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(26,'Penata Kelola Madrasah, Pendidikan Agama dan Keagamaan','penata-kelola-madrasah,-pendidikan-agama-dan-keagamaan','2026-08-25 12:44:05','2026-08-25 12:51:47'),(27,'Pamong Budaya Ahli Muda','pamong-budaya-ahli-muda','2026-08-25 12:44:05','2026-08-25 12:51:47'),(28,'Perencana Ahli Muda','perencana-ahli-muda','2026-08-25 12:44:05','2026-08-25 12:51:47'),(29,'Perencana Ahli Pertama','perencana-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(30,'Analis Hukum Ahli Pertama','analis-hukum-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(31,'Analis Sumber Daya Manusia Aparatur Ahli Muda','analis-sumber-daya-manusia-aparatur-ahli-muda','2026-08-25 12:44:05','2026-08-25 12:51:47'),(32,'Pengelola Layanan Operasional ','pengelola-layanan-operasional-','2026-08-25 12:44:05','2026-08-25 12:51:47'),(33,'Statistisi Ahli Pertama','statistisi-ahli-pertama','2026-08-25 12:44:05','2026-08-25 12:51:47'),(34,'Pengolah Data dan Informasi','pengolah-data-dan-informasi','2026-08-25 12:44:05','2026-08-25 12:51:47'),(35,'Test Jabatan','test-jabatan','2026-08-25 13:00:17','2026-08-25 13:00:17');
/*!40000 ALTER TABLE `jabatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kegiatan`
--

DROP TABLE IF EXISTS `kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kegiatan` (
  `id_kegiatan` int NOT NULL AUTO_INCREMENT,
  `kode_kegiatan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_kegiatan` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kegiatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `lokasi_kegiatan` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_kegiatan` text COLLATE utf8mb4_unicode_ci,
  `latitude_kegiatan` decimal(10,8) DEFAULT NULL,
  `longitude_kegiatan` decimal(11,8) DEFAULT NULL,
  `radius_meter` int DEFAULT '50',
  `status_kegiatan` enum('Draft','Published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `qr_code` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kegiatan`),
  KEY `idx_kegiatan_status` (`status_kegiatan`),
  KEY `idx_kegiatan_tanggal` (`tanggal_kegiatan`),
  KEY `idx_kegiatan_jenis` (`jenis_kegiatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kegiatan`
--

LOCK TABLES `kegiatan` WRITE;
/*!40000 ALTER TABLE `kegiatan` DISABLE KEYS */;
/*!40000 ALTER TABLE `kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pegawai`
--

DROP TABLE IF EXISTS `pegawai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pegawai` (
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jabatan` int NOT NULL,
  `id_tim_kerja` int NOT NULL,
  `id_unit_kerja` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','pegawai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pegawai',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`nip`),
  KEY `idx_pegawai_jabatan` (`id_jabatan`),
  KEY `idx_pegawai_tim_kerja` (`id_tim_kerja`),
  KEY `idx_pegawai_unit_kerja` (`id_unit_kerja`),
  KEY `idx_pegawai_role` (`role`),
  CONSTRAINT `fk_pegawai_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pegawai_tim_kerja` FOREIGN KEY (`id_tim_kerja`) REFERENCES `tim_kerja` (`id_tim_kerja`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pegawai_unit_kerja` FOREIGN KEY (`id_unit_kerja`) REFERENCES `unit_kerja` (`id_unit_kerja`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pegawai`
--

LOCK TABLES `pegawai` WRITE;
/*!40000 ALTER TABLE `pegawai` DISABLE KEYS */;
INSERT INTO `pegawai` VALUES ('197009201999031002','H. MUHAMMAD RAMLI M S.Ag, M.A.P',3,3,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197201142000032001','MARJUSRINA JON, S. PAK, M.Pd.K',27,19,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197208142014111002','AGUS IRAWAN',21,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197212072001121004','CHRISTIANUS LEMBANG, S.E',4,4,4,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197212312002121013','H. MUHAMAD SALEH S.Ag.,M.Pd.I',7,7,7,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197307102000032007','NUR MAHMADAH KH, S. Ag',17,21,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197310102009011010','ABDUL HALIL, S.Ag, M.Pd.I',26,16,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197410182000121001','H. ASPIAN NUR MT, S. Ag',17,24,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197410262001122002','Hj. LISA ARIYANTI, SE',16,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197501092001121005','H. SYAMSU FIRMAN, S.Ag',20,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197501102000121007','H. MANSUR S.Ag',1,1,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197504152005011010','LUKAS, S.Th.',23,17,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197601282025212003','ELLEN SUSAN SUDARSONO, S.Pd',9,17,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197610032005012003','SITTI RAODA, SE., MM',23,13,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197611102006041013','H. ARIE NOVA RACHMANSYAH S.E., M.Si',6,6,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197710212000031001','WARSITO, S.Ag',2,2,2,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197805182005011004','SURYANATA AL-ISLAMI, S.HI',18,12,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('197806172006041018','H. ANGGRAITO, SE, M.Si',15,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198003022025212007','RITA EKAWATI',21,25,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198009132009012002','MUTHMAINNAH, S.HI',8,18,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198010012005011003','OTTO SIMON TANDUK M.PdK',5,5,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198012292007101003','FIRMAN HJ ABDULLAH, S.E',16,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198103162025211009','NELSON HENOCH, S.Pd.K',9,17,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198106152025211008','PARMAN',11,18,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198201052009011010','QAMARUDDIN, S.Pd.I',17,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198203232025211008','ANDRI MUHAJIR, S.E.',9,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198208142009011013','WILDHA WARDHANI, S.IP',18,12,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198212282007012008','MARIANI, M.Pd.',28,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198301302009012005','GUMRRIATI, S.Pd.I',34,24,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198308082025212056','FIFELA IRHAMNA, S.E.',9,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198310022009011007','NUR ROCHMAN, S.Sos',34,24,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198312062019031003','TUDESSTIVAN, S.E',12,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198403132024212027','SITI RAEHANUN, S.EI',10,18,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198403232023211019','REMIGIUS HABIN, S. Fil',25,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198406022008012013','SUSILAWATI, S.Ag, M.Pd',8,8,2,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198407092025211010','MUZAKI ALI MUSTAFA',21,15,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198408112011011009','GAZALI, S.H.I.',18,25,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198410072025212014','OCTRIVIANA, S.E',9,19,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198501052025211009','JOKO RIYANTO, S.Ag',10,8,2,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198510162005011001','ZULKIFLI ISMAIL, S.Kom',9,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198511042011012006','SUGIARTI, S.Kom',20,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198605092019032013','PRATIWI PUJI LESTARI, S.E.',16,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198606122025211015','ONGGOM, S.Pd.I',9,15,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198607202025211017','TAUFIK',11,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198608102025211022','BUDIONO',21,16,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198702042011011007','MUHAMMAD YUSUF, S.E',18,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198703022023212035','IMSAKIAH NADJAMUDIN, S.Si',33,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198804242024212043','LILIS INRA YANI, S.H.',30,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198805142023212035','SITI NUR AINI, S.Kom',14,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198807122025212018','YOSEPLIN ARISTA, S.E.',9,9,4,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198807182025212038','MAYA EVASTIN ALBUGIS',21,17,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198812152019031013','CAESAR NATANIEL, S.Kom',14,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198812262025212032','SITI HADIJAH, S.Pd.',9,12,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198904022025211014','KADRI, S.Pd.I',9,16,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198905192025212011','EBTI CORITA',21,19,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198911082025211015','ANDI MUHAMMAD RAHMAT',11,24,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('198911292022032001','NOVIANTY LETOK, S.H.',18,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199009172023212044','NORMAYANTI, S.Kom',14,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199103112019031022','ANDRIAN SYABANI, S.E',29,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199103172023212052','FITHRIANY HADZIQAH, S.T',25,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199104082020122016','TITAH MUSTIKA ALAM, S.AB',25,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199109062025211010','JUANDI, S.E',9,19,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199110022020121011','JOHAN DURANES, S.IP',17,15,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199111142025211007','ASEP SURIYATMAN, S.Sos',12,13,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199201152023211027','NUR FADHILAT ISLAMY, S.Kom',14,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199202102025211016','SYAMSUL ALANG',11,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199202152025212028','OCTAVIA, S.Pd.K',9,8,2,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199205272025211009','RIKARDO SIMANJUNTAK, S.Pd',9,17,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199210142025212013','RAHMAWATI, S.Pd',9,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199211102025211019','NOPAN',11,12,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199211182025212012','LISDA ANGGRIANI',11,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199303082019031017','MARTINUS LUKAS, S.Kom',14,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','admin','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199305022020122027','CANTIK ZULKHARITSA, S.T.',29,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199309182025211007','SULAIMAN',11,12,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199401082025211010','USMAN, A.Md.',32,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199407062025212015','AZLINA',9,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199410082025211009','CHANDRA WIJAYA, ST',9,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199410092025212015','SUSI PRATIWI, S. Pd',9,15,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199411062019031011','JOHAN EDY PRATAMA, S.E',31,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199411072022032002','SAIDATUL MURI\'AH, S. Pd',26,16,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199411182022032001','CECE MIRANI, S.Ag',8,18,3,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199411302025052006','NURUL AFIDA, S.Pd',24,21,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199501112023212031','SILVYA, S.Si.',33,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','admin','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199501122025211006','ARMAN.P, S.Pd',9,21,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199501292025212004','SITI HADIJAH',21,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199503022023211014','DATU AHMAD MARHABAN, S.Sos',25,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199503062025211011','TIRTA UTAMA, S.Pd.',9,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199504142025052004','AMALIA HIDAYAH CHANIAGO, S.E',12,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199505312025211046','SAYID ABDUL KADIR',11,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199508212025211010','I KADEK DHARMA PRIADI, S.Pd',9,25,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199508222025211042','JUSMAN, S.Pd.',9,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199509102025212013','DARMAWATI',21,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199601232020121012','MUHAMMAD RACHMAT MULYANUS, S.IP',25,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199603162022032002','TRI PANDU UTAMI, S.E.',18,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199604302023211014','AHMAD ARRUSYD, S.Kom',14,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199606152025052004','JUNELSYA GULTOM, S.Pd',33,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199608062025212012','NURLELA',11,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199612022023212027','LETI EKAYANTI, S.Pd',25,23,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199701032023212018','FERLITA, S.I.P.',13,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199703082023211008','ZIYAN AZHAR QINTHARA, S.P.',25,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199704192025051004','ALDI ALFRIANZA SINULINGGA, S.Pd',10,9,4,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199706082023212029','ASRIANI EKA FITRI WARDANI, S.Kom',14,20,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199708162025052005','ENI RAHAYU ISTIQOMAH, S.Sos',13,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199709082020121006','MIFTAH FARID, S.Pd',17,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199801282023212015','KATARINA, S.Pd.',10,9,4,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199806072025052003','NUR ASIAH, S.H',22,15,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199810062022032001','LANI NUR AISYAH, S.Sos',18,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199904032025051008','RIKARDUS BOLI, S.Th.',10,19,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199905142025051005','MUHAMMAD RIZAL, S.Mat',24,13,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199906192025051008','RACHMAT AGUNG AMIR, S.Sos.',13,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199907072025212004','MARNI',11,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199910042025052005','DEWI SARTIKA KABAN, S.Th',10,19,5,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('199910082025212028','ROSTINA RATNA MAYANG SARI',11,9,4,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200001312025212006','SANTI NURHAYATI',21,22,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200007172025051004','ANGGRAITO REFINALDI WIDAYAT, S. Kom',12,14,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200011072025212014','SYARIFAH NADIA',11,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200012182025051005','ALDI HUSEINI MILYANTO, S.Pd',22,13,6,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200101052025052006','FRISCA RIRIN AMELIA, S.P',19,11,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200105302025052005','FISKA AMALIA, S.Sos',13,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200111032025052007','SHOFWAT QONITA KHANIFA, S.Sos',13,10,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13'),('200204252025212007','SYARIFAH SAKINAH',21,12,1,NULL,'$2y$10$gooY8S8tclY7kTfoubku0eDhpZaXAjx4P5h6Lfs8BTT3lB/3Eqiqi','pegawai','2026-08-25 12:44:05','2026-08-25 12:48:13');
/*!40000 ALTER TABLE `pegawai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tim_kerja`
--

DROP TABLE IF EXISTS `tim_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tim_kerja` (
  `id_tim_kerja` int NOT NULL AUTO_INCREMENT,
  `nama_tim_kerja` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug_tim_kerja` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tim_kerja`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tim_kerja`
--

LOCK TABLES `tim_kerja` WRITE;
/*!40000 ALTER TABLE `tim_kerja` DISABLE KEYS */;
INSERT INTO `tim_kerja` VALUES (1,'Bagian Tata Usaha','bagian-tata-usaha','2026-08-25 12:44:05','2026-08-25 12:51:47'),(2,'Bidang Bimbingan Masyarakat Buddha ','bidang-bimbingan-masyarakat-buddha-','2026-08-25 12:44:05','2026-08-25 12:51:47'),(3,'Bidang Bimbingan Masyarakat Islam','bidang-bimbingan-masyarakat-islam','2026-08-25 12:44:05','2026-08-25 12:51:47'),(4,'Bidang Bimbingan Masyarakat Katolik ','bidang-bimbingan-masyarakat-katolik-','2026-08-25 12:44:05','2026-08-25 12:51:47'),(5,'Bidang Bimbingan Masyarakat Kristen','bidang-bimbingan-masyarakat-kristen','2026-08-25 12:44:05','2026-08-25 12:51:47'),(6,'Bidang Pendidikan Islam','bidang-pendidikan-islam','2026-08-25 12:44:05','2026-08-25 12:51:47'),(7,'Kanwil Kemenag Kaltara','kanwil-kemenag-kaltara','2026-08-25 12:44:05','2026-08-25 12:51:47'),(8,'Pembimbing Masyarakat Buddha','pembimbing-masyarakat-buddha','2026-08-25 12:44:05','2026-08-25 12:51:47'),(9,'Pembimbing Masyarakat Katolik','pembimbing-masyarakat-katolik','2026-08-25 12:44:05','2026-08-25 12:51:47'),(10,'Tim Humas dan Komunikasi Publik','tim-humas-dan-komunikasi-publik','2026-08-25 12:44:05','2026-08-25 12:51:47'),(11,'Tim Keuangan dan BMN','tim-keuangan-dan-bmn','2026-08-25 12:44:05','2026-08-25 12:51:47'),(12,'Tim KUB','tim-kub','2026-08-25 12:44:05','2026-08-25 12:51:47'),(13,'Tim Kurikulum, Kesiswaan, Guru dan Tenaga Kependidikan Madrasah','tim-kurikulum,-kesiswaan,-guru-dan-tenaga-kependidikan-madrasah','2026-08-25 12:44:05','2026-08-25 12:51:47'),(14,'Tim Ortala','tim-ortala','2026-08-25 12:44:05','2026-08-25 12:51:47'),(15,'Tim Pendidikan Agama Islam','tim-pendidikan-agama-islam','2026-08-25 12:44:05','2026-08-25 12:51:47'),(16,'Tim Pendidikan Diniyah dan Pondok Pesantren','tim-pendidikan-diniyah-dan-pondok-pesantren','2026-08-25 12:44:05','2026-08-25 12:51:47'),(17,'Tim Pendidikan Kristen','tim-pendidikan-kristen','2026-08-25 12:44:05','2026-08-25 12:51:47'),(18,'Tim Penerangan Agama Islam','tim-penerangan-agama-islam','2026-08-25 12:44:05','2026-08-25 12:51:47'),(19,'Tim Penyuluhan dan Budaya Keagamaan Kristen','tim-penyuluhan-dan-budaya-keagamaan-kristen','2026-08-25 12:44:05','2026-08-25 12:51:47'),(20,'Tim Perencanaan','tim-perencanaan','2026-08-25 12:44:05','2026-08-25 12:51:47'),(21,'Tim Sarana Prasarana dan Kelembagaan','tim-sarana-prasarana-dan-kelembagaan','2026-08-25 12:44:05','2026-08-25 12:51:47'),(22,'Tim SDM, Hukum dan Data','tim-sdm,-hukum-dan-data','2026-08-25 12:44:05','2026-08-25 12:51:47'),(23,'Tim Umum','tim-umum','2026-08-25 12:44:05','2026-08-25 12:51:47'),(24,'Tim Urusan Agama Islam dan Bina KUA','tim-urusan-agama-islam-dan-bina-kua','2026-08-25 12:44:05','2026-08-25 12:51:47'),(25,'Tim Zakat dan Wakaf','tim-zakat-dan-wakaf','2026-08-25 12:44:05','2026-08-25 12:51:47'),(26,'Tim Test','tim-test','2026-08-25 12:59:59','2026-08-25 12:59:59');
/*!40000 ALTER TABLE `tim_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_kerja`
--

DROP TABLE IF EXISTS `unit_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_kerja` (
  `id_unit_kerja` int NOT NULL AUTO_INCREMENT,
  `nama_unit_kerja` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_unit_kerja`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_kerja`
--

LOCK TABLES `unit_kerja` WRITE;
/*!40000 ALTER TABLE `unit_kerja` DISABLE KEYS */;
INSERT INTO `unit_kerja` VALUES (1,'Bagian Tata Usaha','2026-08-25 12:44:05','2026-08-25 12:44:05'),(2,'Pembimbing Masyarakat Buddha','2026-08-25 12:44:05','2026-08-25 12:44:05'),(3,'Bidang Bimbingan Masyarakat Islam','2026-08-25 12:44:05','2026-08-25 12:44:05'),(4,'Pembimbing Masyarakat Katolik','2026-08-25 12:44:05','2026-08-25 12:44:05'),(5,'Bidang Bimbingan Masyarakat Kristen','2026-08-25 12:44:05','2026-08-25 12:44:05'),(6,'Bidang Pendidikan Islam','2026-08-25 12:44:05','2026-08-25 12:44:05'),(7,'Kanwil Kemenag Kaltara','2026-08-25 12:44:05','2026-08-25 12:44:05'),(8,'Bidang Test','2026-08-25 13:00:07','2026-08-25 13:00:07');
/*!40000 ALTER TABLE `unit_kerja` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-25 21:26:11
