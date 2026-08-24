<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#2376D6] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#1d65b8] focus:bg-[#1d65b8] active:bg-[#154b8a] focus:outline-none focus:ring-2 focus:ring-[#2376D6] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
