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

                @can('home_access')
                    <li class="nav-header">HOME</li>

                    @can('qrsprofile_access')
                    <li class="nav-item">
                         <a href="{{ route('qrsprofiles.index') }}"
                            class="nav-link {{ request()->is('qrsprofiles') || request()->is('qrsprofiles/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-unlock-alt nav-icon"></i>
                            <p>QRS Profile</p>
                        </a>
                    </li>
                    @endcan

                    @can('officerdata_access')
                    <li class="nav-item">
                        <a href="{{ route('officers.index') }}"
                        class="nav-link {{ request()->is('officers') || request()->is('officers/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-unlock-alt nav-icon"></i>
                            <p>List of Officers</p>
                        </a>
                    </li>
                    @endcan

                    @can('schooling_access')
                    <li class="nav-item">
                        <a href="{{ route('schoolings.index') }}"
                        class="nav-link {{ request()->is('schoolings') || request()->is('schoolings/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-briefcase nav-icon"></i>
                            <p>Schooling</p>
                        </a>
                    </li>
                    @endcan

                    @can('assignmenthistory_access')
                    <li class="nav-item">
                        <a href="{{ route('assignmenthistories.index') }}"
                        class="nav-link {{ request()->is('assignmenthistories') || request()->is('assignmenthistories/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-user nav-icon"></i>
                            <p>Assignment History</p>
                        </a>
                    </li>
                    @endcan

                    @can('rankpoint_access')
                    <li class="nav-item">
                        <a href="#"
                        class="nav-link">
                            <i class="fa-fw fas fa-list nav-icon"></i>
                            <p>Awards Data</p>
                        </a>
                    </li>
                    @endcan

                    @can('sourcedata_access')
                    <li class="nav-item">
                        <a href="#"
                        class="nav-link">
                            <i class="fa-fw fas fa-database nav-icon"></i>
                            <p>PFT</p>
                        </a>
                    </li>
                    @endcan

                    @can('sourcedata_access')
                    <li class="nav-item">
                        <a href="#"
                        class="nav-link">
                            <i class="fa-fw fas fa-database nav-icon"></i>
                            <p>Career Advising Records</p>
                        </a>
                    </li>
                    @endcan
                @endcan

                @can('reference_access')
                    <li class="nav-item has-treeview {{ request()->is('assignments*') ? 'menu-open' : '' }} {{ request()->is('types*') ? 'menu-open' : '' }} {{ request()->is('ranks*') ? 'menu-open' : '' }} {{ request()->is('rankpoints*') ? 'menu-open' : '' }} {{ request()->is('sourcedatas*') ? 'menu-open' : '' }} {{ request()->is('schoolingunits*') ? 'menu-open' : '' }} {{ request()->is('schoolingentries*') ? 'menu-open' : '' }} {{ request()->is('awards*') ? 'menu-open' : '' }} {{ request()->is('pfts*') ? 'menu-open' : '' }} {{ request()->is('designations*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-users">

                            </i>
                            <p>
                                <span>References</span>
                                <i class="right fa fa-fw fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="margin-left: 20px;">
                            <li class="nav-item">
                                @can('assignment_access')
                                <a href="{{ route("assignments.index") }}"  class="nav-link {{ request()->is('assignments') || request()->is('assignments/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-unlock-alt">

                                    </i>
                                    <p>
                                        <span>Assignments</span>
                                    </p>
                                </a>
                                @endcan
                                @can('type_access')
                                <a href="{{ route("types.index") }}" class="nav-link {{ request()->is('types') || request()->is('types/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-briefcase">

                                    </i>
                                    <p>
                                        <span>Type of Assignment</span>
                                    </p>
                                </a>
                                @endcan
                                @can('rank_access')
                                <a href="{{ route("ranks.index") }}" class="nav-link {{ request()->is('ranks') || request()->is('ranks/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user">

                                    </i>
                                    <p>
                                        <span>Ranks</span>
                                    </p>
                                </a>
                                @endcan
                                @can('rankpoint_access')
                                <a href="{{ route("rankpoints.index") }}" class="nav-link {{ request()->is('rankpoints') || request()->is('rankpoints/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list">

                                    </i>
                                    <p>
                                        <span>Rank points</span>
                                    </p>
                                </a>
                                @endcan
                                @can(abilities: 'award_access')
                                <a href="{{ route("awards.index") }}" class="nav-link {{ request()->is('awards') || request()->is('awards/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user">

                                    </i>
                                    <p>
                                        <span>Awards</span>
                                    </p>
                                </a>
                                @endcan

                                @can(abilities: 'pft_access')
                                <a href="{{ route("pfts.index") }}" class="nav-link {{ request()->is('pfts') || request()->is('pfts/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user">

                                    </i>
                                    <p>
                                        <span>PFTs</span>
                                    </p>
                                </a>
                                @endcan

                                @can('designation_access')
                                <a href="{{ route("designations.index") }}" class="nav-link {{ request()->is('designations') || request()->is('designations/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list">
                                    </i>
                                    <p>
                                        <span>Designation/Unit</span>
                                    </p>
                                </a>
                                @endcan
                                @can('schoolingunit_access')
                                <a href="{{ route("schoolingunits.index") }}" class="nav-link {{ request()->is('schoolingunits') || request()->is('schoolingunits/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list">

                                    </i>
                                    <p>
                                        <span>School and Units</span>
                                    </p>
                                </a>
                                @endcan
                                @can('schoolingentry_access')
                                <a href="{{ route("schoolingentries.index") }}" class="nav-link {{ request()->is('schoolingentries') || request()->is('schoolingentries/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list">

                                    </i>
                                    <p>
                                        <span>Schooling entries</span>
                                    </p>
                                </a>
                                @endcan
                                @can('sourcedata_access')
                                <a href="{{ route("sourcedatas.index") }}" class="nav-link {{ request()->is('sourcedatas') || request()->is('sourcedatas/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list">

                                    </i>
                                    <p>
                                        <span>Source Data</span>
                                    </p>
                                </a>
                                @endcan                                
                            </li>
                        </ul>
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
                        <ul class="nav nav-treeview" style="margin-left: 20px;">
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