
const bookingForm = document.getElementById('bookingForm');
const messageDiv = document.getElementById('message');


const CABANG_COORDS = {
    1: { lat: -7.7956, lng: 110.3695, nama: 'Cabang Malioboro' },      
    2: { lat: -7.7975, lng: 110.4045, nama: 'Cabang Kraton' },         
    3: { lat: -7.8147, lng: 110.3944, nama: 'Cabang Taman' }          
};

let currentMap = null;
let currentMarker = null;

document.addEventListener('DOMContentLoaded', () => {
    loadCabang();
    loadLayanan();
});

function loadCabang() {
    fetch('../backend/api/get_cabang.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('cabang_id');
            const cabangData = {};
            data.data.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.nama_cabang + ' - ' + c.alamat;
                select.appendChild(option);
                cabangData[c.id] = c;
            });
            
            
            window.cabangData = cabangData;
            
            
            select.addEventListener('change', showCabangInfo);
        }
    })
    .catch(err => console.error('Error:', err));
}

function showCabangInfo() {
    const select = document.getElementById('cabang_id');
    const cabangId = select.value;
    const infoDiv = document.getElementById('cabangInfo');
    
    if (!cabangId || !window.cabangData[cabangId]) {
        infoDiv.innerHTML = '';
        return;
    }
    
    const cabang = window.cabangData[cabangId];
    const coords = CABANG_COORDS[cabangId];
    
    infoDiv.innerHTML = `
        <div style="background: #f5f5f5; padding: 12px; border-radius: 6px; border-left: 3px solid #d4a574; margin-bottom: 12px;">
            <strong>${cabang.nama_cabang}</strong><br>
            📍 ${cabang.alamat}<br>
            📞 ${cabang.no_telepon}
        </div>
        <div id="map-${cabangId}" class="map-container"></div>
    `;
    
    
    setTimeout(() => {
        initializeMap(cabangId, coords);
    }, 100);
}

function initializeMap(cabangId, coords) {
    const mapId = `map-${cabangId}`;
    const mapElement = document.getElementById(mapId);
    
    if (!mapElement) return;
    
    
    if (currentMap) {
        currentMap.remove();
    }
    
    
    currentMap = L.map(mapId).setView([coords.lat, coords.lng], 15);
    
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(currentMap);
    
    
    currentMarker = L.marker([coords.lat, coords.lng]).addTo(currentMap)
        .bindPopup(`<strong>${coords.nama}</strong>`);
}

function loadLayanan() {
    fetch('../backend/api/get_layanan.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('layanan_id');
            data.layanan.forEach(l => {
                const option = document.createElement('option');
                option.value = l.id;
                option.textContent = l.nama_layanan + ' - Rp ' + formatNumber(l.harga);
                select.appendChild(option);
            });
        }
    })
    .catch(err => console.error('Error:', err));
}

bookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const cabang_id = document.getElementById('cabang_id').value;
    const layanan_id = document.getElementById('layanan_id').value;
    const tanggal = document.getElementById('tanggal').value;
    const waktu = document.getElementById('waktu').value;
    const catatan = document.getElementById('catatan').value;

    if (!cabang_id || !layanan_id || !tanggal || !waktu) {
        showMessage('Silakan lengkapi data yang diperlukan', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('cabang_id', cabang_id);
    formData.append('layanan_id', layanan_id);
    formData.append('tanggal', tanggal);
    formData.append('waktu', waktu);
    formData.append('catatan', catatan);

    try {
        const res = await fetch('../backend/booking/simpan.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => window.location.href = 'riwayat.html', 1500);
        } else {
            showMessage(data.error || 'Gagal melakukan booking');
        }
    } catch (err) {
        showMessage('Error: ' + err.message);
    }
});

function showMessage(msg, type = 'error') {
    const className = type === 'success' ? 'alert success show' : 'alert error show';
    messageDiv.innerHTML = '<div class="' + className + '">' + msg + '</div>';
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
