<aside class="main-sidebar elevation-4" style="min-height: 917px; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);">
    <!-- Brand Logo -->
    <a href="{{ route("dashboard") }}" class="brand-link text-decoration-none" style="background: rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 1.25rem 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                <i class="fas fa-shield-alt" style="color: white; font-size: 1.25rem;"></i>
            </div>
            <span class="brand-text" style="font-weight: 700; font-size: 1.1rem; color: #fff; letter-spacing: -0.025em;">{{ config('app.name', 'Laravel') }}</span>
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="padding-top: 1rem;">
        <style>
            /* Modern Sidebar Styling */
            .sidebar {
                background: transparent !important;
            }

            .nav-sidebar {
                padding: 0 0.75rem;
            }

            .nav-header {
                color: rgba(255, 255, 255, 0.5);
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                padding: 1.25rem 1rem 0.5rem 1rem;
                margin-top: 0.5rem;
            }

            .nav-item {
                margin-bottom: 0.25rem;
            }

            .nav-link {
                color: rgba(255, 255, 255, 0.75) !important;
                border-radius: 10px;
                padding: 0.75rem 1rem !important;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 0.9rem;
                font-weight: 500;
            }

            .nav-link:hover {
                background: rgba(255, 255, 255, 0.08) !important;
                color: #fff !important;
                transform: translateX(4px);
            }

            .nav-link.active {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
                color: #fff !important;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
                font-weight: 600;
            }

            .nav-link i {
                width: 20px;
                text-align: center;
                font-size: 1rem;
                opacity: 0.9;
            }

            .nav-link.active i {
                opacity: 1;
            }

            /* Dropdown Styling */
            .nav-treeview {
                padding-left: 0 !important;
                margin-top: 0.25rem;
                margin-bottom: 0.5rem;
            }

            .nav-treeview .nav-link {
                padding-left: 3rem !important;
                font-size: 0.85rem;
                color: rgba(255, 255, 255, 0.65) !important;
            }

            .nav-treeview .nav-link:hover {
                color: rgba(255, 255, 255, 0.95) !important;
            }

            .nav-treeview .nav-link.active {
                background: rgba(59, 130, 246, 0.15) !important;
                border-left: 3px solid #3b82f6;
                padding-left: calc(3rem - 3px) !important;
            }

            .has-treeview > .nav-link::after {
                content: '\f107';
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                margin-left: auto;
                transition: transform 0.3s ease;
            }

            .has-treeview.menu-open > .nav-link::after {
                transform: rotate(-180deg);
            }

            /* Badge Styling */
            .nav-badge {
                background: rgba(59, 130, 246, 0.2);
                color: #3b82f6;
                padding: 0.15rem 0.5rem;
                border-radius: 6px;
                font-size: 0.7rem;
                font-weight: 600;
                margin-left: auto;
            }

            /* Logout Button Special Styling */
            .logout-link {
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 1rem;
                padding-top: 1rem;
            }

            .logout-link .nav-link {
                background: rgba(220, 38, 38, 0.1);
                color: #fca5a5 !important;
            }

            .logout-link .nav-link:hover {
                background: rgba(220, 38, 38, 0.2) !important;
                color: #fff !important;
            }
        </style>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @can('dashboard_access')
                <li class="nav-item">
                    <a href="{{ route("dashboard") }}" class="nav-link {{request()->is('dashboard*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endcan

                @can('home_access')
                    <li class="nav-header">PERSONNEL MANAGEMENT</li>

                    @can('qrsprofile_access')
                    <li class="nav-item">
                        <a href="{{ route('qrsprofiles.index') }}"
                            class="nav-link {{ request()->is('qrsprofiles') || request()->is('qrsprofiles/*') ? 'active' : '' }}">
                            <i class="fas fa-id-card"></i>
                            <span>QRS Profile</span>
                        </a>
                    </li>
                    @endcan

                    @can('officerdata_access')
                    <li class="nav-item">
                        <a href="{{ route('officers.index') }}"
                            class="nav-link {{ request()->is('officers') || request()->is('officers/*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>List of Officers</span>
                        </a>
                    </li>
                    @endcan

                    @can('schooling_access')
                    <li class="nav-item">
                        <a href="{{ route('schoolings.index') }}"
                            class="nav-link {{ request()->is('schoolings') || request()->is('schoolings/*') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Schooling</span>
                        </a>
                    </li>
                    @endcan

                    @can('assignmenthistory_access')
                    <li class="nav-item">
                        <a href="{{ route('assignmenthistories.index') }}"
                            class="nav-link {{ request()->is('assignmenthistories') || request()->is('assignmenthistories/*') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Assignment History</span>
                        </a>
                    </li>
                    @endcan

                    @can('awardhistory_access')
                    <li class="nav-item">
                        <a href="{{ route('awardhistories.index') }}"
                            class="nav-link {{ request()->is('awardhistories') || request()->is('awardhistories/*') ? 'active' : '' }}">
                            <i class="fas fa-medal"></i>
                            <span>Awards History</span>
                        </a>
                    </li>
                    @endcan

                    @can('pfthistory_access')
                    <li class="nav-item">
                        <a href="{{ route('pfthistories.index') }}"
                            class="nav-link {{ request()->is('pfthistories') || request()->is('pfthistories/*') ? 'active' : '' }}">
                            <i class="fas fa-running"></i>
                            <span>PFT History</span>
                        </a>
                    </li>
                    @endcan

                    @can('careeradvising_access')
                    <li class="nav-item">
                        <a href="{{ route('careeradvising.index') }}"
                            class="nav-link {{ request()->is('careeradvising') || request()->is('careeradvising/*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Career Advising Records</span>
                        </a>
                    </li>
                    @endcan
                @endcan

                @can('reference_access')
                    @php
                        $referencesOpen = request()->is(
                            'assignments*',
                            'types*',
                            'ranks*',
                            'dateranks',
                            'rankpoints*',
                            'sourcedatas*',
                            'classnames',
                            'schoolingunits*',
                            'schoolingentries*',
                            'awards*',
                            'pfts*',
                            'designations*',
                            'units*',
                            'pamus*'
                        );
                    @endphp

                    <li class="nav-header">CONFIGURATION</li>

                    <li class="nav-item has-treeview {{ $referencesOpen ? 'menu-open' : '' }}">
                        <a class="nav-link {{ $referencesOpen ? 'active' : '' }}" href="#">
                            <i class="fas fa-database"></i>
                            <span>References</span>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('assignment_access')
                            <li class="nav-item">
                                <a href="{{ route("assignments.index") }}" class="nav-link {{ request()->is('assignments') || request()->is('assignments/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Assignments</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('type_access')
                            <li class="nav-item">
                                <a href="{{ route("types.index") }}" class="nav-link {{ request()->is('types') || request()->is('types/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Type of Assignment</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('rank_access')
                            <li class="nav-item">
                                <a href="{{ route("ranks.index") }}" class="nav-link {{ request()->is('ranks') || request()->is('ranks/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Ranks</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('daterank_access')
                            <li class="nav-item">
                                <a href="{{ route("dateranks.index") }}" class="nav-link {{ request()->is('dateranks') || request()->is('dateranks/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Date of Rank</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('rankpoint_access')
                            <li class="nav-item">
                                <a href="{{ route("rankpoints.index") }}" class="nav-link {{ request()->is('rankpoints') || request()->is('rankpoints/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Rank Points</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('award_access')
                            <li class="nav-item">
                                <a href="{{ route("awards.index") }}" class="nav-link {{ request()->is('awards') || request()->is('awards/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Awards</span>
                                </a>
                            </li>
                            @endcan

                            @can('pft_access')
                            <li class="nav-item">
                                <a href="{{ route("pfts.index") }}" class="nav-link {{ request()->is('pfts') || request()->is('pfts/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>PFTs</span>
                                </a>
                            </li>
                            @endcan

                            @can('designation_access')
                            <li class="nav-item">
                                <a href="{{ route("designations.index") }}" class="nav-link {{ request()->is('designations') || request()->is('designations/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Designation</span>
                                </a>
                            </li>
                            @endcan

                            @can('unit_access')
                            <li class="nav-item">
                                <a href="{{ route("units.index") }}" class="nav-link {{ request()->is('units') || request()->is('units/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Units</span>
                                </a>
                            </li>
                            @endcan

                            @can('pamu_access')
                            <li class="nav-item">
                                <a href="{{ route("pamus.index") }}" class="nav-link {{ request()->is('pamus') || request()->is('pamus/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>PAMU / GUA</span>
                                </a>
                            </li>
                            @endcan

                            @can('schoolingname_access')
                            <li class="nav-item">
                                <a href="{{ route("schoolingnames.index") }}" class="nav-link {{ request()->is('schoolingnames') || request()->is('schoolingnames/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Type of Schooling</span>
                                </a>
                            </li>
                            @endcan

                            @can('classname_access')
                            <li class="nav-item">
                                <a href="{{ route("classnames.index") }}" class="nav-link {{ request()->is('classnames') || request()->is('classnames/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Class Years</span>
                                </a>
                            </li>
                            @endcan

                            @can('schoolingunit_access')
                            <li class="nav-item">
                                <a href="{{ route("schoolingunits.index") }}" class="nav-link {{ request()->is('schoolingunits') || request()->is('schoolingunits/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>School and Units</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('sourcedata_access')
                            <li class="nav-item">
                                <a href="{{ route("sourcedatas.index") }}" class="nav-link {{ request()->is('sourcedatas') || request()->is('sourcedatas/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Source Data</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('user_management_access')
                    @php
                        $userManagementOpen = request()->is('audit-trails*') || 
                                               request()->is('users*') || 
                                               request()->is('roles*') || 
                                               request()->is('permissions*');
                    @endphp

                    <li class="nav-header">SYSTEM</li>

                    <li class="nav-item has-treeview {{ $userManagementOpen ? 'menu-open' : '' }}">
                        <a class="nav-link {{ $userManagementOpen ? 'active' : '' }}" href="#">
                            <i class="fas fa-users-cog"></i>
                            <span>User Management</span>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                            <li class="nav-item">
                                <a href="{{ route("permissions.index") }}" class="nav-link {{ request()->is('permissions') || request()->is('permissions/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Permissions</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('role_access')
                            <li class="nav-item">
                                <a href="{{ route("roles.index") }}" class="nav-link {{ request()->is('roles') || request()->is('roles/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Roles</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('user_access')
                            <li class="nav-item">
                                <a href="{{ route("users.index") }}" class="nav-link {{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Users</span>
                                </a>
                            </li>
                            @endcan
                            
                            @can('audit_access')
                            <li class="nav-item">
                                <a href="{{ route("audit-trails.index") }}" class="nav-link {{ request()->is('audit-trails') || request()->is('audit-trails/*') ? 'active' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Audit Trails</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <li class="nav-item logout-link">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>