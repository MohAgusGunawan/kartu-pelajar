// Untuk menampilkan nama file yang dipilih
$('.custom-file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
});

// Untuk menampilkan gambar yang dipilih
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('img-preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}