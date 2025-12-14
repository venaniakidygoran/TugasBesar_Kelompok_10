document.addEventListener('DOMContentLoaded', loadLayanan);

function loadLayanan() {
    fetch('../backend/api/get_layanan.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const html = data.layanan.map(l => `
                <div class="service-item">
                    <span class="service-name">${escapeHtml(l.nama_layanan)}</span>
                    <span class="price-tag">Rp ${formatNumber(l.harga)}</span>
                </div>
            `).join('');
            
            document.getElementById('layananList').innerHTML = html;
        } else {
            showError('Gagal memuat layanan');
        }
    })
    .catch(err => showError('Error: ' + err.message));
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function showError(msg) {
    document.getElementById('layananList').innerHTML = '<p style="color: red;">' + msg + '</p>';
}
