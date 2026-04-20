<header class="admin-header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Admin Dashboard</h1>
        </div>
        <div class="header-right">
            <a class="th-btn p-2 px-4 style3 th-icon" href="../index.php" target="_blank">View Site</a>
        </div>
    </div>
</header>

<style>
.admin-header {
    position: fixed;
    top: 0;
    left: 250px;
    right: 0;
    height: 60px;
    background: white;
    border-bottom: 1px solid #e0e0e0;
    z-index: 999;
    transition: left 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-sidebar.collapsed ~ .admin-header {
    left: 70px;
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
    padding: 0 20px;
}

.page-title {
    margin: 0;
    font-size: 24px;
    color: #2c3e50;
}

.header-right .th-btn {
    margin-left: 10px;
}

@media (max-width: 768px) {
    .admin-header {
        left: 0;
    }

    .admin-sidebar.collapsed ~ .admin-header {
        left: 0;
    }
}
</style>