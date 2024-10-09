<?php
require 'vendor/autoload.php'; // Pastikan ini adalah path yang benar ke autoload Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Koneksi ke database
$host = 'localhost';  // Ganti sesuai dengan host database Anda
$user = 'root';       // Ganti sesuai dengan username database Anda
$pass = '';           // Ganti sesuai dengan password database Anda
$dbname = 'kartu_pelajar'; // Nama database

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

// Buat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Tambahkan header
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'NIS');
$sheet->setCellValue('C1', 'Nama');
$sheet->setCellValue('D1', 'Tempat Tanggal Lahir');
$sheet->setCellValue('E1', 'Jenis Kelamin');
$sheet->setCellValue('F1', 'Alamat');
$sheet->setCellValue('G1', 'Kelas');

// Ambil data siswa dari database
$sql = "SELECT id, nis, nama, ttl, gender, alamat, kelas FROM siswa";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rowIndex = 2; // Baris data mulai dari baris kedua
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['id']);
        $sheet->setCellValue('B' . $rowIndex, $row['nis']);
        $sheet->setCellValue('C' . $rowIndex, $row['nama']);
        $sheet->setCellValue('D' . $rowIndex, $row['ttl']);
        $sheet->setCellValue('E' . $rowIndex, $row['gender']);
        $sheet->setCellValue('F' . $rowIndex, $row['alamat']);
        $sheet->setCellValue('G' . $rowIndex, $row['kelas']);
        $rowIndex++;
    }
} else {
    die("Tidak ada data siswa.");
}

// Simpan file Excel
$excelFile = 'uploads/data_siswa.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($excelFile);

// Buat file ZIP
$zip = new ZipArchive();
$zipFile = 'uploads/laporan_siswa.zip';

if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
    die("Tidak dapat membuat file ZIP.");
}

// Tambahkan file Excel ke dalam ZIP
$zip->addFile($excelFile, 'data_siswa.xlsx');

// Tambahkan foto ke dalam ZIP
$sql = "SELECT id, foto FROM siswa";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Simpan foto sebagai file temporer
        $fotoFile = 'uploads/foto_' . $row['id'] . '.jpg';
        file_put_contents($fotoFile, $row['foto']);
        // Tambahkan foto ke dalam ZIP
        $zip->addFile($fotoFile, 'foto_' . $row['id'] . '.jpg');
    }
}

$zip->close();

// Set header untuk mendownload file ZIP
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename=laporan_siswa.zip');
header('Content-Length: ' . filesize($zipFile));
readfile($zipFile);

// Hapus file sementara
unlink($excelFile);
unlink($zipFile);
?>
