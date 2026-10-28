/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_fasilitas` (
  `fasilitas_key` int NOT NULL AUTO_INCREMENT,
  `id_jenis_fasilitas` varchar(20) NOT NULL,
  `nama_jenis_fasilitas` varchar(150) DEFAULT NULL,
  `nama_fasilitas` varchar(150) DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fasilitas_key`),
  UNIQUE KEY `uk_fasilitas` (`id_jenis_fasilitas`,`nama_fasilitas`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_kelompok_umur` (
  `kelompok_umur_key` int NOT NULL,
  `kelompok` varchar(20) NOT NULL,
  `umur_min` int DEFAULT NULL,
  `umur_max` int DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`kelompok_umur_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_ketersediaan_fasilitas` (
  `target_fasilitas_key` int NOT NULL AUTO_INCREMENT,
  `jenis_fasilitas` varchar(50) NOT NULL,
  `rasio_penduduk` int NOT NULL,
  PRIMARY KEY (`target_fasilitas_key`),
  UNIQUE KEY `uk_jenis_ketersediaan` (`jenis_fasilitas`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_layanan` (
  `layanan_key` int NOT NULL AUTO_INCREMENT,
  `id_jenis_layanan_rs` varchar(20) NOT NULL,
  `nama_layanan` varchar(150) DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`layanan_key`),
  UNIQUE KEY `uk_layanan` (`id_jenis_layanan_rs`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_penyakit` (
  `penyakit_key` int NOT NULL AUTO_INCREMENT,
  `id_penyakit` varchar(20) NOT NULL,
  `nama_penyakit` varchar(150) DEFAULT NULL,
  `id_kategori_penyakit` varchar(20) DEFAULT NULL,
  `nama_kategori` varchar(150) DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`penyakit_key`),
  UNIQUE KEY `uk_penyakit` (`id_penyakit`)
) ENGINE=InnoDB AUTO_INCREMENT=352 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_puskesmas` (
  `puskesmas_key` int NOT NULL AUTO_INCREMENT,
  `id_puskesmas` varchar(50) NOT NULL,
  `nama_puskesmas` varchar(150) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `wilayah_key` int DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`puskesmas_key`),
  UNIQUE KEY `id_puskesmas` (`id_puskesmas`)
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_rs` (
  `rs_key` int NOT NULL AUTO_INCREMENT,
  `id_rs` varchar(20) NOT NULL,
  `nama_rs` varchar(150) DEFAULT NULL,
  `alamat` text,
  `telepon` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `id_kelas_rs` varchar(10) DEFAULT NULL,
  `nama_kelas` varchar(30) DEFAULT NULL,
  `id_jenis_rs` varchar(10) DEFAULT NULL,
  `nama_jenis_rs` varchar(60) DEFAULT NULL,
  `id_pemilik_rs` varchar(10) DEFAULT NULL,
  `nama_pemilik` varchar(60) DEFAULT NULL,
  `wilayah_key` int DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`rs_key`),
  UNIQUE KEY `uk_rs` (`id_rs`),
  KEY `idx_rs_wilayah` (`wilayah_key`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_target_fasilitas` (
  `target_fasilitas_key` int NOT NULL AUTO_INCREMENT,
  `jenis_fasilitas` varchar(50) DEFAULT NULL,
  `rasio_penduduk` int NOT NULL,
  PRIMARY KEY (`target_fasilitas_key`),
  UNIQUE KEY `jenis_fasilitas` (`jenis_fasilitas`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_tenaga_medis` (
  `tenaga_key` int NOT NULL AUTO_INCREMENT,
  `id_jenis_tenaga_medis` varchar(20) NOT NULL,
  `nama_jenis_tenaga` varchar(150) DEFAULT NULL,
  `id_kelompok_tenaga_medis` varchar(20) DEFAULT NULL,
  `nama_kelompok` varchar(100) DEFAULT NULL,
  `kategori_profesi` varchar(30) DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`tenaga_key`),
  UNIQUE KEY `uk_jenis_tenaga` (`id_jenis_tenaga_medis`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `dim_kesehatan_wilayah` (
  `wilayah_kesehatan_key` int NOT NULL AUTO_INCREMENT,
  `id_kabupaten_kota` varchar(20) NOT NULL,
  `nama_kabupaten_kota` varchar(100) DEFAULT NULL,
  `id_provinsi` varchar(20) DEFAULT NULL,
  `nama_provinsi` varchar(100) DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime DEFAULT NULL,
  PRIMARY KEY (`wilayah_kesehatan_key`),
  UNIQUE KEY `id_kabupaten_kota` (`id_kabupaten_kota`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `fact_kesehatan_fasilitas_rs` (
  `fact_fasilitas_key` bigint NOT NULL AUTO_INCREMENT,
  `waktu_key` int NOT NULL,
  `rs_key` int DEFAULT NULL,
  `fasilitas_key` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fact_fasilitas_key`),
  UNIQUE KEY `uk_fact_fasilitas` (`rs_key`,`fasilitas_key`,`waktu_key`),
  KEY `idx_ff_waktu` (`waktu_key`)
) ENGINE=InnoDB AUTO_INCREMENT=5035 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=3513 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `fact_kesehatan_ketersediaan_fasilitas` (
  `fact_ketersediaan_key` bigint NOT NULL AUTO_INCREMENT,
  `wilayah_kesehatan_key` int NOT NULL,
  `waktu_key` int NOT NULL,
  `jumlah_penduduk` int DEFAULT NULL,
  `jumlah_rs_aktual` int DEFAULT NULL,
  `jumlah_puskesmas_aktual` int DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fact_ketersediaan_key`),
  UNIQUE KEY `uk_fact_ketersediaan` (`wilayah_kesehatan_key`,`waktu_key`),
  KEY `idx_fkt_waktu` (`waktu_key`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `fact_kesehatan_layanan_rs` (
  `fact_layanan_key` bigint NOT NULL AUTO_INCREMENT,
  `waktu_key` int NOT NULL,
  `rs_key` int DEFAULT NULL,
  `layanan_key` int DEFAULT NULL,
  `jumlah_layanan` int DEFAULT '1',
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fact_layanan_key`),
  UNIQUE KEY `uk_fact_layanan` (`rs_key`,`layanan_key`,`waktu_key`),
  KEY `idx_fl_waktu` (`waktu_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2923 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `fact_kesehatan_tenaga_medis` (
  `fact_tenaga_key` bigint NOT NULL AUTO_INCREMENT,
  `waktu_key` int NOT NULL,
  `rs_key` int DEFAULT NULL,
  `tenaga_key` int DEFAULT NULL,
  `jumlah_tenaga` int DEFAULT NULL,
  `sumber_database` varchar(255) DEFAULT NULL,
  `waktu_load` datetime NOT NULL,
  PRIMARY KEY (`fact_tenaga_key`),
  UNIQUE KEY `uk_fact_tenaga` (`rs_key`,`tenaga_key`,`waktu_key`),
  KEY `idx_ft_waktu` (`waktu_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3983 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
