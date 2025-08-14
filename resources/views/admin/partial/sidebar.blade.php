<nav id="sidebar">
    <div class="shadow-bottom"></div>
    <ul class="list-unstyled menu-categories ps ps--active-y" id="accordionExample">
        <li class="menu @routeis('admin.dashboard') active @endrouteis">
            <a href="{{ route('admin.dashboard') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-house"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </li>
        @if(Helper::userCan(107))
            <li class="menu @routeis('admin.rewards') active @endrouteis">
                <a href="{{ route('admin.rewards') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-trophy"></i>
                        <span>Rewards</span>
                    </div>
                </a>
            </li>
        @endif
        @if(Helper::userCan(109))
            <li class="menu @routeis('admin.testimonials') active @endrouteis">
                <a href="{{ route('admin.testimonials') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span>Testimonial</span>
                    </div>
                </a>
            </li>
        @endif
       @if(Helper::userCan(118))
        <li class="menu @routeis('admin.blogs') active @endrouteis">
            <a href="{{ route('admin.blogs') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-blog"></i>
                    <span>Blogs</span>
                </div>
            </a>
        </li>
    @endif

        @if(Helper::userCan(112))
            <li class="menu @routeis('admin.add-ons') active @endrouteis">
                <a href="{{ route('admin.add-ons') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-puzzle-piece"></i>
                        <span>Add On</span>
                    </div>
                </a>
            </li>
        @endif

        @if(Helper::userCan(118))
            <li class="menu @routeis('admin.blogs') active @endrouteis">
                <a href="{{ route('admin.blogs') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-blog"></i>
                        <span>Blogs</span>
                    </div>
                </a>
            </li>
        @endif

        @if(Helper::userCan(114))
            <li class="menu @routeis('admin.reviews') active @endrouteis">
                <a href="{{ route('admin.reviews') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-star"></i>
                        <span>Review</span>
                    </div>
                </a>
            </li>
        @endif

        @if(Helper::userCan(108))
        <li class="menu @routeis('admin.booking') active @endrouteis">
            <a href="{{ route('admin.booking') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Bookings</span>
                </div>
            </a>
        </li>
        @endif

        @if(Helper::userCan(110))
         <li class="menu @routeis('admin.vehicles') active @endrouteis">
            <a href="{{ route('admin.vehicles') }}" class="dropdown-toggle">
                <div class="">
                  <i class="fa-solid fa-car"></i>
                    <span>vehicle</span>
                </div>
            </a>
        </li>
        @endif

        @if(Helper::userCan(111))
        <li class="menu @routeis('admin.brands') active @endrouteis">
            <a href="{{ route('admin.brands') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-trademark"></i>
                    <span>Brands</span>
                </div>
            </a>
        </li>
        @endif
        @if(Helper::userCan(113))
        <li class="menu @routeis('admin.plans') active @endrouteis">
                <a href="{{ route('admin.plans') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-table-list"></i>
                        <span>Plans</span>
                    </div>
                </a>
            </li>
        @endif

       @if(Helper::userCan(104))
        <li class="menu @routeis('admin.roles') active @endrouteis">
            <a href="{{ route('admin.roles') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Roles</span>
                </div>
            </a>
        </li>
       @endif

        @if(Helper::userCan(104))
            <li class="menu @routeis('admin.users') active @endrouteis">
                <a href="{{ route('admin.users') }}" class="dropdown-toggle">
                    <div class="">
                        <i class="fa-solid fa-user-crown"></i>
                        <span>Sub Admin</span>
                    </div>
                </a>
            </li>
        @endif

        @if(Helper::userCan([115]))
        <li class="menu @routeis('admin.sliders,admin.cms,admin.faq,admin.enquiries,admin.admin-banners,admin.banners,admin.tags,admin.services') active @endrouteis">
            <a href="#static_content" data-bs-toggle="collapse"
                aria-expanded="{{ Helper::routeis('admin.sliders,admin.cms,admin.faq,admin.enquiries,admin.admin-banners,admin.banners,admin.tags,admin.services') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-sharp fa-solid fa-photo-film"></i>
                    <span>Content</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('admin.sliders,admin.cms,admin.faq,admin.enquiries,admin.admin-banners,admin.banners,admin.tags,admin.services') show @endrouteis"
                id="static_content" data-bs-parent="#accordionExample">

                @if(Helper::userCan(115))
                <li class="@routeis('admin.banners') active @endrouteis">
                    <a href="{{ route('admin.banners') }}">Banners</a>
                </li>
                @endif





                @if(Helper::userCan(115))
                <li class="@routeis('admin.cms') active @endrouteis">
                    <a href="{{ route('admin.cms') }}">CMS</a>
                </li>
                @endif

                @if(Helper::userCan(115))
                <li class="@routeis('admin.active-deals') active @endrouteis">
                    <a href="{{ route('admin.active-deals') }}">Active Deals</a>
                </li>
                @endif
                @if(Helper::userCan(115))
                <li class="@routeis('admin.discount') active @endrouteis">
                    <a href="{{ route('admin.discount') }}">Discount</a>
                </li>
                @endif

                @if(Helper::userCan(115))
                <li class="@routeis('admin.price-details') active @endrouteis">
                    <a href="{{ route('admin.price-details') }}">Price Details</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(Helper::userCan([116]))
        <li class="menu @routeis('admin.cleaners,admin.transactions,admin.cleaning-logs,admin.service-days,admin.order-status-logs,admin.leaves') active @endrouteis">
            <a href="#cleaner_content" data-bs-toggle="collapse"
                aria-expanded="{{ Helper::routeis('admin.cleaners,admin.transactions,admin.cleaning-logs,admin.service-days,admin.order-status-logs,admin.leaves') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-spray-can-sparkles"></i>
                    <span>Cleaners</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('admin.cleaners,admin.transactions,admin.cleaning-logs,admin.service-days,admin.order-status-logs,admin.leaves') show @endrouteis"
                id="cleaner_content" data-bs-parent="#accordionExample">

                @if(Helper::userCan(116))
                <li class="@routeis('admin.cleaners') active @endrouteis">
                    <a href="{{ route('admin.cleaners') }}">Cleaners</a>
                </li>
                @endif

                @if(Helper::userCan(116))
                <li class="@routeis('admin.cleaner-earning') active @endrouteis">
                    <a href="{{ route('admin.cleaner-earning') }}">Cleaner Price List</a>
                </li>
                @endif

                 @if(Helper::userCan(116))
                <li class="@routeis('admin.cleaner-earn') active @endrouteis">
                    <a href="{{ route('admin.cleaner-earn') }}">Cleaner Earning</a>
                </li>
                @endif

                @if(Helper::userCan(116))
                <li class="@routeis('admin.leaves') active @endrouteis">
                    <a href="{{ route('admin.leaves') }}">Leaves</a>
                </li>
                @endif


                @if(Helper::userCan(116))
                <li class="@routeis('admin.transactions') active @endrouteis">
                    <a href="{{ route('admin.transactions') }}">Transactions</a>
                </li>
                @endif

                @if(Helper::userCan(116))
                <li class="@routeis('admin.bank-details') active @endrouteis">
                    <a href="{{ route('admin.bank-details') }}">Bank Details</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(Helper::userCan(117))
        <li class="menu @routeis('admin.app-users,admin.addresses,admin.user-vehicles') active @endrouteis">
            <a href="#user_content" data-bs-toggle="collapse"
                aria-expanded="{{ Helper::routeis('admin.app-users,admin.addresses,admin.user-vehicles') }}"
                class="dropdown-toggle">
                <div class="">
                  <i class="fa-solid fa-users"></i>
                    <span>Users</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('admin.app-users,admin.addresses,admin.user-vehicles') show @endrouteis"
                id="user_content" data-bs-parent="#accordionExample">


                <li class="@routeis('admin.app-users') active @endrouteis">
                    <a href="{{ route('admin.app-users') }}">Users</a>
                </li>

                <li class="@routeis('admin.addresses') active @endrouteis">
                    <a href="{{ route('admin.addresses') }}">Addresses</a>
                </li>

                <li class="@routeis('admin.user-vehicles') active @endrouteis">
                    <a href="{{ route('admin.user-vehicles') }}">User Vehicles</a>
                </li>
            </ul>
        </li>
        @endif

        @if(Helper::userCan([105,106]))
        <li class="menu @routeis('admin.states,admin.cities') active @endrouteis">
            <a href="#location_content" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('admin.states,admin.cities') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-location-dot"></i>
                    <span>Location</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('admin.states,admin.cities') show @endrouteis" id="location_content"
                data-bs-parent="#accordionExample">
                @if(Helper::userCan(105))
                <li class="@routeis('admin.states') active @endrouteis">
                    <a class="nav-link" href="{{ route('admin.states') }}">States</a>
                </li>
                @endif

                @if(Helper::userCan(106))
                <li class="@routeis('admin.cities') active @endrouteis">
                    <a class="nav-link" href="{{ route('admin.cities') }}">Cities</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(Helper::userCan(101))
        <li class="menu @routeis('admin.setting') active @endrouteis">
            <a href="#setting" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('admin.setting') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa fa-cog my-auto"></i>
                    <span>App Setting</span>
                </div>
                <div><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('admin.setting') show @endrouteis" id="setting"
                data-bs-parent="#accordionExample">
                @foreach(config('constant.setting_array', []) as $key => $setting)
                <li class="@if(request()->path() == 'admin/setting/'.$key) active @endif">
                    <a class="nav-link" href="{{ route('admin.setting', ['id' => $key]) }}">
                        {{ $setting }}
                    </a>
                </li>
                @endforeach
            </ul>
        </li>

        <li class="menu">
            <a href="{{route('admin.database_backup')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-database"></i>
                    <span>Database Backup</span>
                </div>
            </a>
        </li>

        <li class="menu  @routeis('admin.server-control') active @endrouteis">
            <a href="{{ route('admin.server-control') }}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-server"></i>
                    <span>Server Control Panel</span>
                </div>
            </a>
        </li>
        @endif
    </ul>
</nav>
