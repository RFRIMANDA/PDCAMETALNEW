function loadDetail(url) {
    // Menggunakan AJAX untuk mengambil data dari route
    fetch(url)
    .then(response => response.text())
    .then(html => {
        // Masukkan konten yang didapat ke dalam modal
        document.getElementById('modalContent').innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading detail:', error);
        document.getElementById('modalContent').innerHTML = 'Gagal memuat data.';
    });
}