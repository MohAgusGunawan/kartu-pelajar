<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Rap2hpoutre\FastExcel\FastExcel;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Siswa::all();
            $data = $data->map(function($item) {
                return array_map('utf8_encode', $item->toArray());
            });
            // dd($data);

            return Datatables::of($data)->make(true);
        }
        return view('form');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nis' => 'required|integer',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:50',
            'wa' => 'required|string|max:20',
            'kelas' => 'required|string|max:10',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $siswa = new Siswa();
        $siswa->nis = $request->nis;
        $siswa->nama = $request->nama;
        $tempatLahir = $request->tempat_lahir;
        $tanggalLahir = $request->tanggal_lahir;
        $siswa->ttl = $tempatLahir . ', ' . $tanggalLahir;
        $siswa->gender = $request->gender;
        $siswa->alamat = $request->alamat;
        $siswa->wa = $request->wa;
        $siswa->kelas = $request->kelas;

        // Jika ada file foto yang di-upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');

            // Resize gambar menggunakan GD atau Imagick
            $img = imagecreatefromstring(file_get_contents($foto->getRealPath()));

            // Ukuran baru untuk 4x6 (472x709 pixel)
            $newWidth = 472;
            $newHeight = 709;

            // Membuat gambar baru dengan ukuran yang diinginkan
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($img), imagesy($img));

            // Kompres gambar ke 200KB jika diperlukan
            ob_start();
            imagejpeg($resizedImage, null, 75); // Menggunakan kualitas 75%
            $compressedImageData = ob_get_contents();
            ob_end_clean();

            // Jika ukuran masih lebih dari 200KB, kompres lebih lanjut
            if (strlen($compressedImageData) > 200 * 1024) {
                ob_start();
                imagejpeg($resizedImage, null, 50); // Kompres lebih lanjut jika perlu
                $compressedImageData = ob_get_contents();
                ob_end_clean();
            }

            // Simpan gambar yang sudah di-resize ke dalam file temporer
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
            file_put_contents($tempPath, $compressedImageData);

            // Simpan gambar ke folder storage/app/public/images/siswa
            $path = $foto->storeAs('images/siswa', basename($tempPath), 'public');

            // Simpan path file ke dalam database
            $siswa->foto = $path;

            // Membersihkan memori
            imagedestroy($img);
            imagedestroy($resizedImage);
        }

        // Simpan data siswa ke database
        $siswa->save();

        // Redirect setelah data berhasil disimpan
        return redirect()->route('form.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function downloadReport()
    {
        $data = Siswa::all();
        $no = 1;

        $pelajarData = $data->map(function ($pelajar) use (&$no) {
            return [
                'NO' => $no++,
                'NIS' => $pelajar->nis,
                'Nama' => $pelajar->nama,
                'Tempat, Tanggal Lahir' => $pelajar->ttl,
                'Jenis Kelamin' => $pelajar->gender,
                'Alamat' => $pelajar->alamat,
                'Nomor HP (WA)' => $pelajar->wa,
                'Kelas' => $pelajar->kelas,
            ];
        });

        return (new FastExcel($pelajarData))->download('siswa_report.xlsx');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
