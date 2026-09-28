<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" title="Mi Dashboard">
            <i class="fas fa-th-large"></i>
            <span class="sidebar-text">Mi Dashboard</span>
        </a>
        
        <!-- Añade aquí los enlaces propios del rol usuario conforme los necesites -->

        <!-- Soporte fijado al fondo -->
        <a href="#" class="nav-item-bottom" title="Soporte">
            <i class="fas fa-headset"></i>
            <span class="sidebar-text">Soporte</span>
        </a>
    </nav>
</aside>