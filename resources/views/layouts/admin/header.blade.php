

<!-- BEGIN: Header-->
<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-header expanded">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-lg-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item mr-auto brand-logo-wrapper">
                    <a class="navbar-brand" href="{{url('/')}}">
                        <img class="brand-logo" alt="modern admin logo" src="{{asset('assets/imgs/logo.png')}}">
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand" href="#"><i class="ficon ft-maximize"></i></a></li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    
                    @if(auth()->check())
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="mr-1 user-name text-bold-700">{{auth()->user()->name}}</span>
                            <span class="avatar avatar-online">
                                @if(auth()->check())
                                    @if(empty(auth()->user()->profile->pic))
                                        <img src="{{asset('assets/imgs/noimage.png')}}" alt="avatar">
                                     @else
                                        <img src="{{asset('storage/uploads/users/'.auth()->user()->profile->pic)}}"
                                             alt="user-img">
                                     @endif
                                @endif
                                <i></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                       
                            
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                            
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                            @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</nav>