<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a href="javascript:;"><i class="la la-home"></i><span class="menu-title" data-i18n="Dashboard">Home</span></a>
                <ul class="menu-content">
                    <li class="{{ (request()->routeIs('admin.dashboard'))? 'active' : '' }}">
                        <a class="menu-item" href="{{url('admin/dashboard')}}"><i></i>
                            <span data-i18n="eCommerce">Dashboard</span>
                    </a>
                    </li>
                    <li class="{{ (request()->routeIs('home'))? 'active' : '' }}">
                        <a class="menu-item" href="{{ URL('') }}"><i></i>
                            <span data-i18n="Crypto">Visit Website</span>
                        </a>
                    </li>
                    
                </ul>
            </li>
           
           <li class="nav-item">
                <a href="{{url('admin/cars')}}" target="_blank"><i class="la la-tags"></i>
                    <span class="menu-title" data-i18n="eCommerce">Cars </span>
                </a>
            </li>
            
            
            <li class="nav-item">
                <a href="{{url('admin/account/settings')}}"><i class="la la-shopping-cart"></i>
                    <span class="menu-title" data-i18n="eCommerce">Account Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>
