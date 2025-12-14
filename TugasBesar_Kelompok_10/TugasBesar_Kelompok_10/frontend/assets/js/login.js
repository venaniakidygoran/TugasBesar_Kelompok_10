const loginForm = document.getElementById('loginForm');
const errorDiv = document.getElementById('error');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    try {
        const res = await fetch('../backend/auth/login.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if (data.success) {
            localStorage.setItem('isAdmin', data.is_admin);
            window.location.href = 'dashboard.html';
        } else {
            showError(data.error || 'Login gagal');
        }
    } catch (err) {
        showError('Terjadi kesalahan: ' + err.message);
    }
});

function showError(msg) {
    errorDiv.innerHTML = '<div class="alert error show">' + msg + '</div>';
}
