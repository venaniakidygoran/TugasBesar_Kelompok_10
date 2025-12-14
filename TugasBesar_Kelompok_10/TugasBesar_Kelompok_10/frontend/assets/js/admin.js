
document.addEventListener('DOMContentLoaded', () => {
    loadLayanan();
    loadBookings();
});

function loadLayanan() {
    fetch('../backend/api/get_layanan.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('layananList');
            tbody.innerHTML = data.layanan.map(l => `
                <tr>
                    <td>${l.id}</td>
                    <td>${escapeHtml(l.nama_layanan)}</td>
                    <td>Rp ${formatNumber(l.harga)}</td>
                    <td>
                        <button class="btn-edit" onclick="editLayanan(${l.id})">Edit</button>
                        <button class="btn-delete" onclick="deleteLayanan(${l.id})">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }
    });
}

function loadBookings() {
    fetch('../backend/api/get_bookings_admin.php')
    .then(res => res.json())
    .then(data => {
        console.log('Bookings data:', data);
        if (data.success && data.bookings) {
            const tbody = document.getElementById('bookingList');
            tbody.innerHTML = data.bookings.map(b => `
                <tr>
                    <td>${escapeHtml(b.email || 'N/A')}</td>
                    <td>${escapeHtml(b.nama_layanan)}</td>
                    <td>${b.tanggal}</td>
                    <td>${b.waktu}</td>
                    <td>${escapeHtml(b.catatan || '-')}</td>
                    <td>
                        <select class="status-select status-${b.status}" onchange="updateBookingStatus(${b.id}, this.value)">
                            <option value="pending" ${b.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="confirmed" ${b.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                            <option value="completed" ${b.status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="cancelled" ${b.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn-delete" onclick="deleteBookingAdmin(${b.id})">Hapus</button>
                    </td>
                </tr>
            `).join('');
        } else {
            console.error('Failed to load bookings:', data);
            document.getElementById('bookingList').innerHTML = '<tr><td colspan="7">Gagal memuat data booking</td></tr>';
        }
    })
    .catch(err => {
        console.error('Error fetching bookings:', err);
        document.getElementById('bookingList').innerHTML = '<tr><td colspan="7">Error: ' + err.message + '</td></tr>';
    });
}

function showAddLayananForm() {
    document.getElementById('addLayananForm').style.display = 'block';
}

function hideAddLayananForm() {
    document.getElementById('addLayananForm').style.display = 'none';
    document.getElementById('layananName').value = '';
    document.getElementById('layananPrice').value = '';
}

function addLayanan() {
    const nama = document.getElementById('layananName').value;
    const harga = document.getElementById('layananPrice').value;

    if (!nama || !harga) {
        alert('Silakan lengkapi data');
        return;
    }

    const formData = new FormData();
    formData.append('nama_layanan', nama);
    formData.append('harga', harga);

    fetch('../backend/layanan/tambah.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            hideAddLayananForm();
            loadLayanan();
        } else {
            alert(data.error || 'Gagal menambahkan layanan');
        }
    });
}

function editLayanan(id) {
    fetch('../backend/layanan/edit.php?id=' + id, {
        method: 'GET'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const nama = prompt('Nama Layanan:', data.layanan.nama_layanan);
            if (nama === null) return;
            
            const harga = prompt('Harga:', data.layanan.harga);
            if (harga === null) return;

            const fd = new FormData();
            fd.append('id', id);
            fd.append('nama_layanan', nama);
            fd.append('harga', harga);

            fetch('../backend/layanan/edit.php', {
                method: 'POST',
                body: fd
            })
            .then(res => res.json())
            .then(d => {
                if (d.success) {
                    alert(d.message);
                    loadLayanan();
                } else {
                    alert(d.error);
                }
            });
        }
    });
}

function deleteLayanan(id) {
    if (!confirm('Yakin ingin menghapus layanan ini?')) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('../backend/layanan/hapus.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadLayanan();
        } else {
            alert(data.error);
        }
    });
}

function deleteBookingAdmin(id) {
    if (!confirm('Yakin ingin menghapus booking ini?')) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('../backend/booking/hapus_admin.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadBookings();
        } else {
            alert(data.error || 'Gagal menghapus booking');
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}

function updateBookingStatus(id, newStatus) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', newStatus);

    fetch('../backend/booking/update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadBookings();
        } else {
            alert(data.error || 'Gagal update status');
            loadBookings();
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
        loadBookings();
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

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
