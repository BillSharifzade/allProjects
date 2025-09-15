<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="/dashboard" class="logo logo-normal">
            <img src="/admin/assets/img/logo.svg" alt="Logo">
        </a>
        <a href="/dashboard" class="logo-small">
            <img src="/admin/assets/img/logo-small.svg" alt="Logo">
        </a>
        <a href="/dashboard" class="dark-logo">
            <img src="/admin/assets/img/logo-white.svg" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>MAIN MENU</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="active subdrop">
                                <i class="ti ti-smart-home"></i>
                                <span>Dashboard</span>
                                <span class="badge badge-danger fs-10 fw-medium text-white p-1">Hot</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="/dashboard" class="active">Site Dashboard</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="menu-title"><span>Site Content</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{route('aboutUs')}}">
                                <i class="ti ti-layout-navbar"></i><span>About Us</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('aboutQwasar')}}">
                                <i class="ti ti-layout-navbar"></i><span>About Qwasar</span> 
                            </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="">
                                <i class="ti ti-smart-home"></i>
                                <span>Courses</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{route('courseCategory')}}" class="">Category</a></li>
                                <li><a href="{{route('courses')}}" class="">Courses</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{route('teams')}}">
                                <i class="ti ti-layout-navbar"></i><span>Team</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('events')}}">
                                <i class="ti ti-layout-navbar"></i><span>Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('services')}}">
                                <i class="ti ti-layout-navbar"></i><span>Services</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('blogs')}}">
                                <i class="ti ti-layout-navbar"></i><span>Blog</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('partners')}}">
                                <i class="ti ti-layout-navbar"></i><span>Partners</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-title"><span>Qwasar</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{route('qwasarServices')}}">
                                <i class="ti ti-layout-navbar"></i><span>Qwasar Services</span> 
                            </a>
                        </li>
                        <li>
                            <a href="{{route('qwasarPaths')}}">
                                <i class="ti ti-layout-navbar"></i><span>Qwasar Paths</span> 
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
