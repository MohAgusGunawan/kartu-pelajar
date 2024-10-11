<!-- resources/views/pegawai/create.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Siswa</title>
    <!-- Tambahkan CSS Bootstrap atau custom CSS sesuai kebutuhan -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>
    @if(Session::has('success'))
      <script>
          document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
              toast: true,
              position: "top",
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
              }
            });
            Toast.fire({
              icon: 'success',
              title: '{{ Session::get('success') }}'
            });
          });
      </script>
  @endif
    <div class="container">
        <h2 class="mt-4" style="margin-top: -15px !important;">Form Input Data Siswa</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('form.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nis">NIS</label>
                <input type="number" class="form-control" id="nis" name="nis" min="10000" max="99999" value="{{ old('nip') }}" required>
            </div>

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
            </div>
            <div class="form-group">
                <label for="tempat_lahir">Tempat Lahir</label>
                <select class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                    <option value="Pamekasan">Pamekasan</option>
                    <option value="Sumenep">Sumenep</option>
                    <option value="Sampang">Sampang</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
            </div>
            <div class="form-group">
                <label for="gender">Jenis Kelamin</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" maxlength="50" value="{{ old('alamat') }}" required>
            </div>
            <div class="form-group">
                <label for="wa">Nomor WA</label>
                <input type="number" class="form-control" id="wa" name="wa" pattern="[0-9]{10,15}" placeholder="08XXXXXXXXXXX" value="{{ old('wa') }}" required>
            </div>
            <div class="form-group">
                <label for="kelas">Kelas</label>
                <select class="form-control" id="kelas" name="kelas" required>
                    <option value="IPA IA">IPA IA</option>
                    <option value="IPA IB">IPA IB</option>
                    <option value="IPA IC">IPA IC</option>
                </select>
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="foto" name="foto" accept=".jpg, .jpeg, .png" onchange="previewImage(event)" required>
                    <label class="custom-file-label" for="foto">Choose file</label>
                </div>
                <img id="img-preview" src="" alt="Foto Pelajar" class="img-thumbnail img-preview mt-2">
            </div>


            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>    

    <div class="container d-flex justify-content-center align-items-center">
        <div class="table-container">
            <div class="head d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">Data Siswa</h4>
                <button type="submit" class="btn btn-success btn-sm d-flex align-items-center" id="bt-download">
                    <span id="spann" style="font-size: 1rem;">Download Laporan</span>
                </button>
            </div>
            <div class="table-wrapper">
                <table id="tbSiswa" class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Tempat, Tanggal Lahir</th>
                            <th>Jenis Kelamin</th>
                            <th>Alamat</th>
                            <th>Nomor WA</th>
                            <th>Kelas</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                </table>  
            </div>
        </div>
    </div>    
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    @include('partial.data')
</body>
</html>
