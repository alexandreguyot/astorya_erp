<nav class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4">
    <div class="w-full mx-auto items-center flex justify-between md:flex-nowrap flex-wrap md:px-10 px-4">
        <a class="text-white text-sm uppercase hidden lg:inline-block font-semibold" href="#">
            {{-- Dashboard --}}
        </a>

        {{-- If you use user icon and menu add margin mr-3 to search --}}
        {{-- <form class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3"> --}}
        <form class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto">

        </form>
        {{-- <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
        @foreach(auth()->user()->unreadNotifications as $notification)
            <li class="inline-block relative">
                <div class="alert alert-success">
                    {{ $notification->data['message'] }}
                </div>
            </li>
        @endforeach
        </ul> --}}



        {{-- @if(file_exists(app_path('Http/Livewire/LanguageSwitcher.php')))
            <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
                <livewire:language-switcher />
            </ul>
        @endif --}}

        {{-- User icon and menu --}}

        <ul class="flex-col md:flex-row list-none items-center hidden md:flex">

            <div class="relative inline-block text-left">
                <div>
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                            id="notifications-menu-button" aria-expanded="true" aria-haspopup="true">
                        🔔 Notifications
                        @if(auth()->user()->unreadNotifications->count())
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                </div>

                <div class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                     id="user-dropdown" role="menu" aria-orientation="vertical" aria-labelledby="notifications-menu-button">

                    <div class="py-1 px-3 max-h-96 overflow-y-auto">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <div class="flex items-start bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 mb-2">
                                <div class="flex-shrink-0 mt-1">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11V5a1 1 0 10-2 0v2a1 1 0 002 0zm-1 2a1 1 0 00-.993.883L9 10v3a1 1 0 001.993.117L11 13v-3a1 1 0 00-1-1z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    {{ $notification->data['message'] }}
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm text-center py-3">Aucune nouvelle notification.</p>
                        @endforelse
                    </div>

                    @if(auth()->user()->unreadNotifications->count())
                        <div class="border-t px-4 py-2">
                            <form method="POST" action="{{ route('admin.notifications.markAllRead') }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                                    ✅ Marquer toutes comme lues
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </ul>
    </div>
</nav>

<script>
    document.getElementById('notifications-menu-button').addEventListener('click', function () {
        let dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('hidden');
    });
</script>
