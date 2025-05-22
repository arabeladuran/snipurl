<nav class="container d-flex justify-content-between align-items-center px-3 pt-4 mb-5" style="max-width: 1300px;">
    <a href="dashboard.php" class="nav-logo"><img src="assets/logo.png" alt="SnipURL Logo" style="height: 40px;"></a>

    <div class="nav-right">
        <a href="dashboard.php" class="btn">Dashboard</a>

        <div class="dropdown">
            <button class="btn dropdown-toggle" id="profileBtn">Profile</button>
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="links.php" class="dropdown-item">Link History</a>
                <a href="profile.php" class="dropdown-item">Edit Profile</a>
                <a href="logout.php" class="dropdown-item">Logout</a>
            </div>
        </div>
    </div>
</nav>

<script>
    const profileBtn = document.getElementById('profileBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');

    profileBtn.addEventListener('click', () => {
        const isShown = dropdownMenu.style.display === 'block';
        dropdownMenu.style.display = isShown ? 'none' : 'block';
    });

    // Close dropdown if clicked outside
    window.addEventListener('click', (e) => {
        if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
</script>