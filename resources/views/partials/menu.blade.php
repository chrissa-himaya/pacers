<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="{{ route("dashboard") }}" class="brand-link text-decoration-none">
        <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @can('dashboard_access')
                <li class="nav-item">
                    <a href="{{ route("dashboard") }}" class="nav-link {{request()->is('dashboard*') ? 'active' : '' }}">
                        <i class="fa-fw fas fa-chart-line">

                        </i>
                        <p>
                            <span>Dashboard</span>
                        </p>
                    </a>
                </li>
                @endcan
        
                @can('user_management_access')
                <li class="nav-item has-treeview {{ request()->is('audit-trails*') ? 'menu-open' : '' }} {{ request()->is('users*') ? 'menu-open' : '' }} {{ request()->is('roles*') ? 'menu-open' : '' }} {{ request()->is('permissions*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-users">

                            </i>
                            <p>
                                <span>User Management</span>
                                <i class="right fa fa-fw fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                @can('permission_access')
                                <a href="{{ route("permissions.index") }}"  class="nav-link {{ request()->is('permissions') || request()->is('permissions/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-unlock-alt">

                                    </i>
                                    <p>
                                        <span>Permissions</span>
                                    </p>
                                </a>
                                @endcan
                                @can('role_access')
                                <a href="{{ route("roles.index") }}" class="nav-link {{ request()->is('roles') || request()->is('roles/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-briefcase">

                                    </i>
                                    <p>
                                        <span>Roles</span>
                                    </p>
                                </a>
                                @endcan
                                @can('user_access')
                                <a href="{{ route("users.index") }}" class="nav-link {{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user">

                                    </i>
                                    <p>
                                        <span>Users</span>
                                    </p>
                                </a>
                                @endcan
                                @can('audit_access')
                                <a href="{{ route("audit-trails.index") }}" class="nav-link {{ request()->is('audit-trails') || request()->is('audit-trails/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list">

                                    </i>
                                    <p>
                                        <span>Audit Trails</span>
                                    </p>
                                </a>
                                @endcan                                
                            </li>
                        </ul>
                    </li>
                    @endcan
        


                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt">

                            </i>
                            <span>Logout</span>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>