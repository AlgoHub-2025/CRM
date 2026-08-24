@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#2376D6] focus:ring-[#2376D6] rounded-md shadow-sm']) }}>
