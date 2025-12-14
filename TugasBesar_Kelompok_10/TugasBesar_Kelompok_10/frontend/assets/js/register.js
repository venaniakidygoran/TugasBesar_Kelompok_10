const registerForm = document.getElementById('registerForm');
const emailInput = document.getElementById('email');
const emailCheck = document.getElementById('emailCheck');
const errorsDiv = document.getElementById('errors');

emailInput.addEventListener('blur', async () => {
    const email = emailInput.value;
    if (!email) return;

    try {
        const res = await fetch('../backend/api/check_email.php?email=' + encodeURIComponent(email));
        const data = await res.json();
        
        if (data.exists) {
            emailCheck.textContent = '❌ Email sudah terdaftar';
            emailCheck.className = 'email-check taken';
        } else {
            emailCheck.textContent = '✓ Email tersedia';
            emailCheck.className = 'email-check available';
        }
    } catch (err) {
        console.error('Error checking email:', err);
    }
});

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nama = document.getElementById('nama').value;
    const email = document.getElementById('email').value;
    const no_hp = document.getElementById('no_hp').value;
    const password = document.getElementById('password').value;
    const password2 = document.getElementById('password2').value;

    if (password !== password2) {
        showError('Password dan konfirmasi tidak sama');
        return;
    }

    const formData = new FormData();
    formData.append('nama', nama);
    formData.append('email', email);
    formData.append('no_hp', no_hp);
    formData.append('password', password);
    formData.append('password2', password2);

    try {
        const res = await fetch('../backend/auth/register.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if (data.success) {
            alert(data.message);
            window.location.href = 'login.html';
        } else {
            showError(data.errors ? data.errors.join('<br>') : 'Error');
        }
    } catch (err) {
        showError('Terjadi kesalahan: ' + err.message);
    }
});

function showError(msg) {
    errorsDiv.innerHTML = '<div class="alert error show">' + msg + '</div>';
}
