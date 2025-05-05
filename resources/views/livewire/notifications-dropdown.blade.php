<div wire:poll.5s>
    <div>
        <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                id="notifications-menu-button" aria-expanded="true" aria-haspopup="true">
            Notifications
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
            @forelse($notifications as $notification)
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

        @if($notifications->count())
            <div class="border-t px-4 py-2">
                <button wire:click="markAllRead" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                    Marquer toutes comme lues
                </button>
            </div>
        @endif
    </div>
</div>
