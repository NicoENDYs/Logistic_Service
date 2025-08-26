@props(['active' => false])
@php $cls = $active
    ? 'block px-3 py-2 rounded-lg bg-gray-900 text-white'
    : 'block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100'; @endphp
<a {{ $attributes->merge(['class' => $cls]) }}>
    {{ $slot }}
</a>
