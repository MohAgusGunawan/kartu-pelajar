<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Koneksi ke database
$host = 'localhost';  
$user = 'root';       
$pass = '';           
$dbname = 'kartu_pelajar'; 

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $ttl = $_POST['ttl'];
    $gender = $_POST['gender'];
    $alamat = $_POST['alamat'];
    $kelas = $_POST['kelas'];

    // Validasi dan Unggah Foto ke Database
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto'];
        $fotoData = file_get_contents($foto['tmp_name']);  // Membaca file sebagai binary

        // Masukkan data ke dalam database
        $stmt = $conn->prepare("INSERT INTO siswa (nis, nama, ttl, gender, alamat, kelas, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $nis, $nama, $ttl, $gender, $alamat, $kelas, $fotoData);

        if ($stmt->execute()) {
            echo "Data siswa berhasil disimpan ke database!<br>";
        } else {
            echo "Terjadi kesalahan: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        die('Harap unggah foto.');
    }
}

// Ambil data dari database untuk ditampilkan dalam tabel
$sql = "SELECT * FROM siswa";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        h2 {
            color: #4CAF50;
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .table-container {
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .download-btn-container {
            text-align: right;
            margin-bottom: 10px;
        }

        .download-btn-container button {
            float: right;
            margin-bottom: 10px;
        }

        .download-btn-container form {
            display: inline-block;  /* Form hanya selebar tombol */
            margin-bottom: 0;       /* Menghilangkan margin tambahan */
        }

        .download-btn-container button {
            color: white;              /* Warna teks tombol */
            border: none !important;
            cursor: pointer;
            font-size: 16px;
            float: right;              /* Tombol tetap berada di kanan */
            margin-top: 0;             /* Menghilangkan margin atas */
        }

        .download-btn-container button:hover {
            background-color: #45a049;  /* Warna saat di-hover */
        }
    </style>
</head>
<body>
    <h2>Form Input Data Siswa</h2>
    <form action="form.php" method="post" enctype="multipart/form-data">
        <label for="nis">NIS:</label>
        <input type="number" id="nis" name="nis" min="10000" max="99999" required>

        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required>

        <label for="ttl">Tempat Tanggal Lahir:</label>
        <input type="text" id="ttl" name="ttl" placeholder="Kota, DD-MM-YYYY" required>

        <label for="gender">Jenis Kelamin:</label>
        <input type="radio" id="laki-laki" name="gender" value="Laki-laki" required>
        <label for="laki-laki">Laki-laki</label>
        <input type="radio" id="perempuan" name="gender" value="Perempuan" required>
        <label for="perempuan">Perempuan</label><br><br>

        <label for="alamat">Alamat:</label>
        <input type="text" id="alamat" name="alamat" maxlength="50" required>

        <label for="kelas">Kelas:</label>
        <select id="kelas" name="kelas" required>
            <option value="IPA IA">IPA IA</option>
            <option value="IPA IB">IPA IB</option>
            <option value="IPA IC">IPA IC</option>
        </select>

        <label for="foto">Foto:</label>
        <input type="file" id="foto" name="foto" accept="image/*" required>

        <button type="submit">Submit</button>
    </form>

    <div class="table-container">
        
        <h2>Data Siswa</h2>
        <div class="download-btn-container">
            <form action="download.php" method="post">
                <button type="submit">Download Laporan</button>
            </form>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>TTL</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Kelas</th>
                <th>Foto</th>
            </tr>

            <?php
            // Tampilkan data siswa dari database
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nis'] . "</td>";
                    echo "<td>" . $row['nama'] . "</td>";
                    echo "<td>" . $row['ttl'] . "</td>";
                    echo "<td>" . $row['gender'] . "</td>";
                    echo "<td>" . $row['alamat'] . "</td>";
                    echo "<td>" . $row['kelas'] . "</td>";
                    echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['foto']) . "' width='100' height='150'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Tidak ada data.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
