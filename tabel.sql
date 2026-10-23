/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE IF NOT EXISTS `dim_dukcapil_penduduk` (
  `penduduk_key` bigint NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `kewarganegaraan` varchar(30) DEFAULT NULL,
  `sumber_database` varchar(100) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`penduduk_key`),
  UNIQUE KEY `uk_penduduk` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=2051 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_wilayah_penduduk` (
  `wilayah_key` int NOT NULL AUTO_INCREMENT,
  `id_alamat` varchar(10) NOT NULL,
  `jalan` text,
  `kode_pos` varchar(10) DEFAULT NULL,
  `id_desa` varchar(10) DEFAULT NULL,
  `nama_desa` varchar(100) DEFAULT NULL,
  `id_kecamatan` varchar(10) DEFAULT NULL,
  `nama_kecamatan` varchar(100) DEFAULT NULL,
  `id_kabupaten_kota` varchar(10) DEFAULT NULL,
  `nama_kabupaten_kota` varchar(100) DEFAULT NULL,
  `id_provinsi` varchar(10) DEFAULT NULL,
  `nama_provinsi` varchar(100) DEFAULT NULL,
  `sumber_database` varchar(100) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`wilayah_key`),
  UNIQUE KEY `uk_wilayah` (`id_alamat`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `fact_dukcapil_penduduk` (
  `fact_dukcapil_penduduk_key` bigint NOT NULL AUTO_INCREMENT,
  `penduduk_key` bigint NOT NULL,
  `wilayah_key` int DEFAULT NULL,
  `agama_key` int DEFAULT NULL,
  `status_perkawinan_key` int DEFAULT NULL,
  `status_penduduk_key` int DEFAULT NULL,
  `kartu_keluarga_key` int DEFAULT NULL,
  `waktu_key` int NOT NULL,
  `umur` int DEFAULT NULL,
  `jumlah_penduduk` int DEFAULT '1',
  `sumber_database` varchar(100) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fact_dukcapil_penduduk_key`),
  UNIQUE KEY `uk_fact_snapshot` (`penduduk_key`,`waktu_key`),
  KEY `idx_fact_wilayah` (`wilayah_key`),
  KEY `idx_fact_agama` (`agama_key`),
  KEY `idx_fact_waktu` (`waktu_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4097 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `fact_kesehatan_kasus_penyakit` (
  `fact_kasus_key` bigint NOT NULL AUTO_INCREMENT,
  `id_kasus` varchar(20) NOT NULL,
  `waktu_key` int NOT NULL,
  `rs_key` int DEFAULT NULL,
  `penyakit_key` int DEFAULT NULL,
  `penduduk_key` bigint DEFAULT NULL,
  `kelompok_umur_key` int DEFAULT NULL,
  `umur` int DEFAULT NULL,
  `jumlah_kasus` int DEFAULT '1',
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fact_kasus_key`),
  UNIQUE KEY `uk_fact_kasus` (`id_kasus`),
  KEY `idx_fk_waktu` (`waktu_key`),
  KEY `idx_fk_rs` (`rs_key`),
  KEY `idx_fk_penyakit` (`penyakit_key`),
  KEY `idx_fk_penduduk` (`penduduk_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4098 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
