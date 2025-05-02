<nav class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4">
    <div class="w-full mx-auto items-center flex justify-between md:flex-nowrap flex-wrap md:px-10 px-4">
        <a class="text-white text-sm uppercase hidden lg:inline-block font-semibold" href="#">
        </a>

        <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
            <div class="relative inline-block text-left">
               

                @livewire('notifications-dropdown')
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
