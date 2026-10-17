@php
    use App\Constants\UserConst;
    $hasAnyMenu = false;
@endphp

@foreach ($sidebarMenus as $groupKey => $menus)
    @if (!empty($menus))
        @php $hasAnyMenu = true; @endphp
        @foreach ($menus as $menu)
            @include('_admin._layout.sidebar._menu_item', ['menu' => $menu])
        @endforeach
    @endif
@endforeach

@if (!$hasAnyMenu)
    {{-- Fallback: no menus configured for this role --}}
    <li class="px-3 py-4">
        <p class="text-xs text-gray-400 dark:text-neutral-500 text-center">Tidak ada menu tersedia.</p>
    </li>
@endif