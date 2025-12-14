const profileForm = document.getElementById('profileForm');
const messageDiv = document.getElementById('message');
const fotoPreview = document.getElementById('fotoPreview');

document.addEventListener('DOMContentLoaded', loadProfile);

function loadProfile() {
    fetch('../backend/auth/profile.php', {
        method: 'GET'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('nama').value = data.user.nama;
            document.getElementById('email').value = data.user.email;
            document.getElementById('no_hp').value = data.user.no_hp || '';
            
            if (data.user.foto) {
                fotoPreview.innerHTML = '<img src="../backend/uploads/profile/' + data.user.foto + '" style="max-width:150px; border-radius: 8px; margin-top: 10px;">';
            }
        } else {
            window.location.href = 'login.html';
        }
    })
    .catch(err => showMessage('Error: ' + err.message));
}

profileForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(profileForm);

    try {
        const res = await fetch('../backend/auth/profile.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => loadProfile(), 1000);
        } else {
            showMessage(data.error || 'Gagal menyimpan profil');
        }
    } catch (err) {
        showMessage('Error: ' + err.message);
    }
});

function showMessage(msg, type = 'error') {
    const className = type === 'success' ? 'alert success show' : 'alert error show';
    messageDiv.innerHTML = '<div class="' + className + '">' + msg + '</div>';
}
