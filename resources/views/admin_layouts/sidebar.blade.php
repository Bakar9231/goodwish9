<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ url('/dashboard') }}">
                <?php
                $data = \App\Settings::first();
                ?>
                <img src="{{url('/uploads/GoodWish-Logo-2.png')}}" width="70%" alt="Image"/>

                 <!--<img src="{{ URL::to('/') }}/assets/uploads/GoodWish-Logo-1.png"/> -->
                <!-- <h3 class="mt-3 logo_name">{{ $data->app_name }}</h3> -->
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Main</li>

            @if( session()->get('role')== "admin")

            <li class="dropdown {{ active_class(['dashboard']) }}">
                <a href="{{ url('/dashboard') }}" class="nav-link"><i
                        data-feather="monitor"></i><span>Dashboard</span></a>
            </li>
            <li class="dropdown {{ active_class(['user*']) }}">
                <a href="{{ url('/user/list') }}" class="nav-link"><i data-feather="users"></i><span>App User</span></a>
            </li>
            
               <li class="dropdown {{ active_class(['partner*']) }}">
                <a href="{{ url('/partner/list') }}" class="nav-link"><i data-feather="user-plus"></i><span>Partners</span></a>
            </li>

            <li
                class="dropdown {{ request()->is('content*') || request()->is('movie/source*') || request()->is('series/source*') || request()->is('movie/cast*') || request()->is('movie/subtitle*') || request()->is('series/subtitle*') || request()->is('series/season*') ? 'active' : '' }}">
                <a href="{{ url('/content/list') }}" class="nav-link"><i
                        data-feather="video"></i><span>Content</span></a>
            </li>

            <li
                class="dropdown {{ active_class(['offer*']) }}">
                <a href="{{ url('/offer/list') }}" class="nav-link"><i
                        data-feather="video"></i><span>Add Offer</span></a>
            </li>

            <!-- <li class="dropdown {{ request()->is('tv/channel*') ? 'active' : '' }}">
                <a href="{{ url('/tv/channel/list') }}" class="nav-link"><i data-feather="tv"></i><span>Live TV
                        Channel</span></a>
            </li>


            <li class="dropdown {{ active_class(['tv/category/list']) }}">
                <a href="{{ url('/tv/category/list') }}" class="nav-link"><i data-feather="box"></i><span>Live TV
                        Categories</span></a>
            </li>


            <li class="dropdown {{ active_class(['actor/list']) }}">
                <a href="{{ url('/actor/list') }}" class="nav-link"><i
                        class="fas fa-users ml-0"></i><span>Actors</span></a>
            </li>

            <li class="dropdown {{ active_class(['genre/list']) }}">
                <a href="{{ url('/genre/list') }}" class="nav-link"><i data-feather="tag"></i><span>Genres</span></a>
            </li>

            <li class="dropdown {{ active_class(['language/list']) }}">
                <a href="{{ url('/language/list') }}" class="nav-link"><i
                        class="fas fa-language ml-0"></i><span>Language</span></a>
            </li> -->

            <li class="dropdown {{ active_class(['subscription/package']) }}">
                <a href="{{ url('/subscription/package') }}" class="nav-link"><i
                        data-feather="package"></i><span>Packages</span></a>
            </li>

            <li class="dropdown {{ active_class(['subscription/list']) }}">
                <a href="{{ url('/subscription/list') }}" class="nav-link"><i
                        data-feather="credit-card"></i><span>Subscribers</span></a>
            </li>

            <li class="dropdown {{ active_class(['notification/list']) }}">
                <a href="{{ url('/notification/list') }}" class="nav-link"><i
                        data-feather="bell"></i><span>Notification</span></a>
            </li>
<!-- 
            <li class="dropdown {{ active_class(['ads']) }}">
                <a href="{{ url('/ads') }}" class="nav-link"><i data-feather="trending-up"></i><span>Admob
                        Ads</span></a>
            </li>

            <li class="dropdown {{ active_class(['ads/customAdsList']) }}">
                <a href="{{ url('/ads/customAdsList') }}" class="nav-link"><i data-feather="layout"></i><span>
                    Custom Ads</span></a>
            </li> -->

            <!--<li class="dropdown {{ active_class(['settings']) }}">-->
            <!--    <a href="{{ url('/settings') }}" class="nav-link"><i-->
            <!--            data-feather="settings"></i><span>Settings</span></a>-->
            <!--</li>-->

            <li class="dropdown {{ active_class(['privacypolicy']) }}">
                <a href="{{ url('/privacypolicy') }}" class="nav-link"><i data-feather="target"></i><span>Privacy
                        Policy</span></a>
            </li>

            <li class="dropdown {{ active_class(['termscondition']) }}">
                <a href="{{ url('/termscondition') }}" class="nav-link"><i data-feather="lock"></i><span>Terms &
                        Condition</span></a>
            </li>
            
            <li class="dropdown {{ active_class(['settings']) }}">
                <a href="{{ url('/settings') }}" class="nav-link"><i
                        data-feather="settings"></i><span>Settings</span></a>
             </li>
            @endif

            @if( session()->get('role')== "partner")
                <li class="dropdown {{ active_class(['dashboard']) }}">
                    <a href="{{ url('partner/dashboard') }}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
                </li>
                <li class="dropdown {{ active_class(['user*']) }}">
                    <a href="{{ url('partner/users/list') }}" class="nav-link"><i data-feather="users"></i><span>App Users</span></a>
                </li>
                <li class="dropdown {{ active_class(['income*']) }}">
                    <a href="{{ url('partner/reports/list') }}" class="nav-link"><i data-feather="pie-chart"></i><span>Income Reports</span></a>
                </li>
                <li class="dropdown {{ active_class(['user*']) }}">
                    <a href="{{ url('partner/offers/list') }}" class="nav-link"><i data-feather="percent"></i><span>Offers</span></a>
                </li>
                <li class="dropdown {{ active_class(['user*']) }}">
                    <a href="{{route('partner/payout/list')}}" class="nav-link"><i data-feather="percent"></i><span>Payout Request</span></a>
                </li>
                <li class="dropdown {{ active_class(['user*']) }}">
                    <a href="{{ url('partner/my-profile') }}" class="nav-link"><i data-feather="settings"></i><span>Settings</span></a>
                </li>
            @endif
        </ul>
    </aside>
</div>
