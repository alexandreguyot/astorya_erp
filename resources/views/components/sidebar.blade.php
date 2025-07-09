<nav class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6">
    <div class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto">
        <button class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent" type="button" onclick="toggleNavbar('example-collapse-sidebar')">
            <i class="fas fa-bars"></i>
        </button>
        <a class="md:block text-left md:pb-2 text-blueGray-700 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0" href="{{ route('admin.home') }}">
            {{ trans('panel.site_title') }}
        </a>
        <div class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded hidden" id="example-collapse-sidebar">
            <div class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-blueGray-300">
                <div class="flex flex-wrap">
                    <div class="w-6/12">
                        <a class="md:block text-left md:pb-2 text-blueGray-700 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0" href="{{ route('admin.home') }}">
                            {{ trans('panel.site_title') }}
                        </a>
                    </div>
                    <div class="w-6/12 flex justify-end">
                        <button type="button" class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent" onclick="toggleNavbar('example-collapse-sidebar')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>



            <!-- Divider -->
            <div class="flex md:hidden">
                @if(file_exists(app_path('Http/Livewire/LanguageSwitcher.php')))
                    <livewire:language-switcher />
                @endif
            </div>
            <hr class="mb-6 md:min-w-full" />
            <!-- Heading -->

            <ul class="md:flex-col md:min-w-full flex flex-col list-none">
                <li class="items-center">
                    <a href="{{ route("admin.home") }}" class="{{ request()->is("tableau-de-bord") ? "sidebar-nav-active" : "sidebar-nav" }}">
                        <i class="fas fa-tv"></i>
                        {{ trans('global.dashboard') }}
                    </a>
                </li>

                @can('contract_access')
                    <li class="items-center">
                        <a class="{{ request()->is("contrats*") && !request()->is("contrats/annuels*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.contracts.index") }}">
                            <i class="fa-fw c-sidebar-nav-icon fas fa-folder-open">
                            </i>
                            {{ trans('cruds.contract.title') }}
                        </a>
                    </li>
                @endcan
                @can('contract_annual_access')
                    <li class="items-center">
                        <a class="{{ request()->is("contrats/annuels*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.contracts.annual-index") }}">
                            <i class="fa-fw c-sidebar-nav-icon fas fa-folder-open">
                            </i>
                            {{ trans('cruds.contract.title') }} annuel
                        </a>
                    </li>
                @endcan
                @can('bill_access')
                    <li class="items-center">
                        <a class="{{ request()->is("factures*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.bills.index") }}">
                            <i class="fa-fw c-sidebar-nav-icon fas fa-list-ul">
                            </i>
                            {{ trans('cruds.bill.title') }}
                        </a>
                    </li>
                @endcan
                @can('company_access')
                    <li class="items-center">
                        <a class="{{ request()->is("clients*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.companies.index") }}">
                            <i class="fa-fw c-sidebar-nav-icon fas fa-user-friends">
                            </i>
                            {{ trans('cruds.company.title') }}
                        </a>
                    </li>
                @endcan
                @can('parametre_access')
                    <li class="items-center">
                        <a class="has-sub {{ request()->is("nos-coordonnees*")||request()->is("type-de-contract*")||request()->is("type-de-periode*")||request()->is("type-de-tva*")||request()->is("type-de-produit*")  ? "sidebar-nav-active" : "sidebar-nav" }}" href="#" onclick="window.openSubNav(this)">
                            <i class="fa-fw fas c-sidebar-nav-icon fa-cogs">
                            </i>
                            {{ trans('cruds.parametre.title') }}
                        </a>
                        <ul class="ml-4 subnav hidden">
                            @can('owner_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("nos-coordonnees*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.owners.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-home">
                                        </i>
                                        {{ trans('cruds.owner.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('type_contract_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("type-de-contract*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.type-contract.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.typeContract.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('type_period_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("type-de-periode*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.type-period.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.typePeriod.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('type_vat_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("type-de-tva*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.type-vat.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.typeVat.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('type_product_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("type-de-produit*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.type-product.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.typeProduct.title') }}
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('city_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("villes*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.cities.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon far fa-building">
                                        </i>
                                        {{ trans('cruds.city.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('contact_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("contacts*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.contacts.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.contact.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('bank_account_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("bank-accounts*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.bank-accounts.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-cogs">
                                        </i>
                                        {{ trans('cruds.bankAccount.title') }}
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcan
                @can('user_management_access')
                    <li class="items-center">
                        <a class="has-sub {{ request()->is("permissions*")||request()->is("roles*")||request()->is("utilisateurs*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="#" onclick="window.openSubNav(this)">
                            <i class="fa-fw fas c-sidebar-nav-icon fa-users">
                            </i>
                            {{ trans('cruds.userManagement.title') }}
                        </a>
                        <ul class="ml-4 subnav hidden">
                            @can('permission_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("permissions*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.permissions.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-unlock-alt">
                                        </i>
                                        {{ trans('cruds.permission.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("roles*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.roles.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-briefcase">
                                        </i>
                                        {{ trans('cruds.role.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="items-center">
                                    <a class="{{ request()->is("utilisateurs*") ? "sidebar-nav-active" : "sidebar-nav" }}" href="{{ route("admin.users.index") }}">
                                        <i class="fa-fw c-sidebar-nav-icon fas fa-user">
                                        </i>
                                        {{ trans('cruds.user.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @if(file_exists(app_path('Http/Controllers/Auth/UserProfileController.php')))
                    @can('auth_profile_edit')
                        <li class="items-center">
                            <a href="{{ route("profile.show") }}" class="{{ request()->is("profile") ? "sidebar-nav-active" : "sidebar-nav" }}">
                                <i class="fa-fw c-sidebar-nav-icon fas fa-user-circle"></i>
                                {{ trans('global.my_profile') }}
                            </a>
                        </li>
                    @endcan
                @endif

                <li class="items-center">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();" class="sidebar-nav">
                        <i class="fa-fw fas fa-sign-out-alt"></i>
                        {{ trans('global.logout') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
