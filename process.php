<?php
require 'vendor/autoload.php'; // Pastikan ini adalah path yang benar ke autoload Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi NIS
    $nis = $_POST['nis'];
    if ($nis < 10000 || $nis > 99999) {
        die('NIS harus 5 digit.');
    }

    // Validasi dan Resize Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto'];
        
        // Tentukan ukuran baru (400x600 piksel)
        $new_width = 400;
        $new_height = 600;
        
        // Cek tipe gambar
        $image_info = getimagesize($foto['tmp_name']);
        if ($image_info === false) {
            die('File bukan gambar yang valid.');
        }

        $mime_type = $image_info['mime'];
        switch ($mime_type) {
            case 'image/jpeg':
                $src_image = imagecreatefromjpeg($foto['tmp_name']);
                break;
            case 'image/png':
                $src_image = imagecreatefrompng($foto['tmp_name']);
                break;
            case 'image/gif':
                $src_image = imagecreatefromgif($foto['tmp_name']);
                break;
            default:
                die('Format gambar tidak didukung. Hanya JPEG, PNG, dan GIF yang diperbolehkan.');
        }

        // Resize gambar
        $dst_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $image_info[0], $image_info[1]);

        // Simpan gambar baru dengan ukuran file yang dioptimalkan (maks 200 KB)
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($foto['name']);

        // Coba simpan dengan kualitas yang diatur agar file <= 200 KB
        $quality = 90;  // Awal dari kualitas JPEG
        do {
            ob_start();  // Mulai buffer output
            imagejpeg($dst_image, null, $quality);  // Simpan ke buffer dengan kualitas saat ini
            $image_data = ob_get_contents();  // Ambil data dari buffer
            ob_end_clean();  // Hentikan buffer
            $file_size = strlen($image_data);  // Hitung ukuran file
            $quality -= 5;  // Kurangi kualitas setiap iterasi
        } while ($file_size > 200 * 1024 && $quality > 10);  // Pastikan ukuran <= 200 KB dan kualitas > 10

        // Simpan file jika berhasil di-resize
        if (file_put_contents($upload_file, $image_data)) {
            echo "Foto berhasil diunggah dan di-resize menjadi 4x6 serta tidak lebih dari 200 KB!<br>";
        } else {
            die('Terjadi kesalahan saat mengunggah file.');
        }

        // Hapus gambar dari memori
        imagedestroy($src_image);
        imagedestroy($dst_image);
    } else {
        die('Harap unggah foto.');
    }

    // Simpan Data ke Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tambahkan header
    $sheet->setCellValue('A1', 'NIS');
    $sheet->setCellValue('B1', 'Nama');
    $sheet->setCellValue('C1', 'Tempat Tanggal Lahir');
    $sheet->setCellValue('D1', 'Jenis Kelamin');
    $sheet->setCellValue('E1', 'Alamat');
    $sheet->setCellValue('F1', 'Kelas');

    // Tambahkan data dari form
    $sheet->setCellValue('A2', htmlspecialchars($_POST['nis']));
    $sheet->setCellValue('B2', htmlspecialchars($_POST['nama']));
    $sheet->setCellValue('C2', htmlspecialchars($_POST['ttl']));
    $sheet->setCellValue('D2', htmlspecialchars($_POST['gender']));
    $sheet->setCellValue('E2', htmlspecialchars($_POST['alamat']));
    $sheet->setCellValue('F2', htmlspecialchars($_POST['kelas']));

    // Tentukan nama file dan lokasi penyimpanan
    $excel_file = $upload_dir . 'data_siswa.xlsx';

    // Simpan file Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save($excel_file);

    echo "Data siswa berhasil disimpan ke file Excel!<br>";
    
    // Tampilkan data lainnya jika semua valid
    echo "<h3>Data Siswa</h3>";
    echo "NIS: " . htmlspecialchars($_POST['nis']) . "<br>";
    echo "Nama: " . htmlspecialchars($_POST['nama']) . "<br>";
    echo "Tempat Tanggal Lahir: " . htmlspecialchars($_POST['ttl']) . "<br>";
    echo "Jenis Kelamin: " . htmlspecialchars($_POST['gender']) . "<br>";
    echo "Alamat: " . htmlspecialchars($_POST['alamat']) . "<br>";
    echo "Kelas: " . htmlspecialchars($_POST['kelas']) . "<br>";
} else {
    echo "Form belum diisi.";
}
?>
