
document.addEventListener('DOMContentLoaded', () => {
    const userName = document.getElementById('userName');
    const adminLink = document.getElementById('adminLink');
    
    fetch('../backend/auth/profile.php', {
        method: 'GET'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            userName.textContent = data.user.nama;
            
            if (data.user.is_admin == 1) {
                adminLink.style.display = 'flex';
            }
            
            const photoEl = document.getElementById('profilePhoto');
            if (photoEl) {
                if (data.user.foto) {
                    console.log('Loading profile foto:', data.user.foto);
                    photoEl.src = '../backend/uploads/profile/' + data.user.foto;
                    photoEl.onerror = () => showInitials(data.user.nama);
                } else {
                    console.log('No foto found, using default');
                    photoEl.src = '../backend/uploads/profile/default.png';
                    photoEl.onerror = () => showInitials(data.user.nama);
                }
            }
        } else {
            window.location.href = 'login.html';
        }
    })
    .catch(() => window.location.href = 'login.html');
});

function logout() {
    if (confirm('Yakin ingin logout?')) {
        window.location.href = '../backend/auth/logout.php';
    }
}

function showInitials(nama) {
    const photoEl = document.getElementById('profilePhoto');
    if (!photoEl) return;
    
    const initials = nama.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
    const circle = photoEl.parentElement;
    
    photoEl.style.display = 'none';
    circle.innerHTML = `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #d4a574, #c49565); color: white; font-weight: 700; font-size: 1.1rem;">${initials}</div>`;
}
    
