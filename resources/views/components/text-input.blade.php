@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-zinc-300 shadow-sm focus:border-[#e30613] focus:ring-[#e30613]']) }}>
