document.addEventListener('DOMContentLoaded', loadBookings);

function loadBookings() {
    fetch('../backend/booking/list.php', {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('bookingList');
            if (data.bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Belum ada booking</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.bookings.map(b => `
                <tr>
                    <td>${escapeHtml(b.nama_layanan)}</td>
                    <td>${b.tanggal}</td>
                    <td>${b.waktu}</td>
                    <td><strong>${escapeHtml(b.status)}</strong></td>
                    <td>${b.catatan ? escapeHtml(b.catatan) : '-'}</td>
                    <td>
                        <button class="btn-edit" onclick="editBooking(${b.id})">Edit</button>
                        <button class="btn-delete" onclick="deleteBooking(${b.id})">Hapus</button>
                    </td>
                </tr>
            `).join('');
        } else {
            showMessage('Gagal memuat booking');
        }
    })
    .catch(err => showMessage('Error: ' + err.message));
}

function editBooking(id) {
    fetch('../backend/booking/edit.php?id=' + id, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'booking.html?edit=' + id;
        } else {
            showMessage('Gagal memuat data booking');
        }
    });
}

function deleteBooking(id) {
    if (!confirm('Yakin ingin menghapus booking ini?')) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('../backend/booking/hapus.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => loadBookings(), 1000);
        } else {
            showMessage(data.error || 'Gagal menghapus booking');
        }
    });
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

function showMessage(msg, type = 'error') {
    const messageDiv = document.getElementById('message');
    const className = type === 'success' ? 'alert success show' : 'alert error show';
    messageDiv.innerHTML = '<div class="' + className + '">' + msg + '</div>';
}
