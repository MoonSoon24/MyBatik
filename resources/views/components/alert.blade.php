@props(['type' => 'success', 'message' => ''])

<div
    x-data="{ show: false, type: '{{ $type }}', message: '{{ $message }}' }"
    x-show="show"
    x-on:alert.window="show = true; type = $event.detail.type; message = $event.detail.message; setTimeout(() => show = false, 4000)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-4"
    class="fixed top-5 right-5 text-white py-3 px-6 rounded-lg shadow-lg z-50 flex items-center"
    :class="{
        'bg-green-500': type === 'success',
        'bg-red-500': type === 'error'
    }"
    x-cloak
>
    <template x-if="type === 'success'">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </template>

    <template x-if="type === 'error'">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </template>

    <span x-text="message"></span>
</div>