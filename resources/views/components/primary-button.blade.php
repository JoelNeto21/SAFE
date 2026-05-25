<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-lg border border-transparent bg-[#e30613] px-4 py-2 text-xs font-semibold uppercase tracking-normal text-white transition duration-150 ease-in-out hover:bg-[#b8000f] focus:bg-[#b8000f] focus:outline-none focus:ring-2 focus:ring-[#e30613] focus:ring-offset-2 active:bg-[#8f000c]']) }}>
    {{ $slot }}
</button>
