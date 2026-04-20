<!-- Sidebar -->
<div id="sidebar" class="admin-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../assets/img/logo/logo-footer.webp" alt="India Day Trip" class="sidebar-logo">
        </div>
    </div>
    <div class="sidebar-separator"></div>
    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="index.php" class="menu-link">
                <i class="fas fa-tachometer-alt menu-icon"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="tours.php" class="menu-link">
                <i class="fas fa-route menu-icon"></i>
                <span class="menu-text">Tours Management</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="blogs.php" class="menu-link">
                <i class="fas fa-blog menu-icon"></i>
                <span class="menu-text">Blogs Management</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="gallery.php" class="menu-link">
                <i class="fas fa-images menu-icon"></i>
                <span class="menu-text">Gallery Management</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="seo.php" class="menu-link">
                <i class="fas fa-search menu-icon"></i>
                <span class="menu-text">SEO Management</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="settings.php" class="menu-link">
                <i class="fas fa-cog menu-icon"></i>
                <span class="menu-text">Settings</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt menu-icon"></i>
            <span class="menu-text">Logout</span>
        </a>
        <div class="developer-info">
            Developed by <a class="text-white" href="https://dynexia.netlify.app/" target="_blank">Dynexia IT Solutions</a>
        </div>
    </div>
</div>

<!-- Sidebar Toggle Button -->
<button id="sidebarToggle" class="sidebar-toggle">
    <i class="fas fa-bars toggle-icon"></i>
</button>

<style>
.admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    transition: width 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    overflow-x: hidden;
}

.admin-sidebar.collapsed {
    width: 70px;
    overflow-y: hidden;
}

.sidebar-header {
    padding: 20px;
    text-align: start;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-logo {
    width: 80%;
    height: auto;
    margin-bottom: 15px;
}

.sidebar-separator {
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 0 20px;
}

.sidebar-menu {
    list-style: none;
    padding: 20px 0;
    margin: 0;
}

.menu-item {
    margin: 5px 0;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
}

.menu-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

.menu-icon {
    width: 20px;
    margin-right: 15px;
    text-align: center;
}

.menu-text {
    transition: opacity 0.3s ease;
}

.admin-sidebar.collapsed .menu-text {
    opacity: 0;
    visibility: hidden;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
}

.logout-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ff6b6b;
    background: rgba(255, 107, 107, 0.1);
    text-decoration: none;
    margin-bottom: 20px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.logout-link:hover {
    color: white;
    background: rgba(255, 107, 107, 0.2);
}

.developer-info {
    font-size: 12px;
    color: rgba(255,255,255,0.6);
    text-align: center;
    line-height: 1.4;
    transition: opacity 0.3s ease;
}

.admin-sidebar.collapsed .developer-info {
    opacity: 0;
    display: none;
    visibility: hidden;
}

.sidebar-toggle {
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1001;
    background: #1087c3ff;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.admin-sidebar:not(.collapsed) ~ .sidebar-toggle {
    left: 200px;
}

.sidebar-toggle:hover {
    background: #2980b9;
}

.main-content {
    margin-left: 250px;
    margin-top: 60px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}

.admin-sidebar.collapsed ~ .main-content {
    margin-left: 70px;
}

.content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .admin-sidebar.open {
        transform: translateX(0);
    }

    .sidebar-toggle {
        left: 15px;
    }

    .admin-sidebar:not(.collapsed) ~ .sidebar-toggle {
        left: 15px;
    }

    .main-content {
        margin-left: 0;
        margin-top: 60px;
    }

    .admin-sidebar.collapsed ~ .main-content {
        margin-left: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    // Toggle sidebar
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open');
        }

        // Update toggle icon
        const icon = toggleBtn.querySelector('.toggle-icon');
        if (sidebar.classList.contains('collapsed')) {
            icon.className = 'fas fa-angle-right toggle-icon';
        } else {
            icon.className = 'fas fa-bars toggle-icon';
        }
    });

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && !sidebar.contains(e.target) && e.target !== toggleBtn) {
            sidebar.classList.remove('open');
        }
    });
});
</script>