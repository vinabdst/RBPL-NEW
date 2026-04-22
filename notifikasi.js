function showNotification(message, type = 'success') {
    let notif = document.getElementById('notification');
    if (!notif) {
        notif = document.createElement('div');
        notif.id = 'notification';
        notif.className = 'notification';
        document.body.appendChild(notif);
    }
    notif.textContent = message;
    notif.className = `notification ${type} show`;
    setTimeout(() => {
        notif.classList.remove('show');
    }, 3000);
}

const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');
if (status === 'added') showNotification('Data berhasil ditambahkan.', 'success');
if (status === 'updated') showNotification('Data berhasil diperbarui.', 'success');
if (status === 'deleted') showNotification('Data berhasil dihapus.', 'success');
if (status === 'error') showNotification('Terjadi kesalahan!', 'error');

if (status) {
    const newUrl = window.location.pathname;
    window.history.replaceState({}, document.title, newUrl);
}