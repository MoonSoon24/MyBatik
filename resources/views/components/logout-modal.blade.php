<div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl text-center">
        <h3 class="text-xl font-bold mb-4">Confirm Logout</h3>
        <p class="mb-6">Are you sure you want to log out?</p>
        <div class="flex justify-center gap-4">
            <button id="confirm-logout-btn" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg">Logout</button>
            <button id="cancel-logout-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
        </div>
    </div>
</div>

<script>
const logoutLink = document.getElementById('logout-link');
const logoutForm = document.getElementById('logout-form');
const logoutModal = document.getElementById('logout-modal');
const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
const cancelLogoutBtn = document.getElementById('cancel-logout-btn');

if (logoutLink && logoutForm && logoutModal && confirmLogoutBtn && cancelLogoutBtn) {
    logoutLink.addEventListener('click', (e) => {
        e.preventDefault();
        logoutModal.classList.remove('hidden');
    });

    confirmLogoutBtn.addEventListener('click', () => {
        logoutForm.submit();
    });

    cancelLogoutBtn.addEventListener('click', () => {
        logoutModal.classList.add('hidden');
    });

    logoutModal.addEventListener('click', (e) => {
        if (e.target.id === 'logout-modal') {
            logoutModal.classList.add('hidden');
        }
    });
}
</script>