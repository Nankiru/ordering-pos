<div class="inline-flex items-center space-x-1" role="group" aria-label="Language switch">
    @php($current = app()->getLocale())

    <a href="{{ route('locale.set', ['locale' => 'en']) }}" title="English" class="inline-flex items-center gap-2 px-2 py-1 rounded-md text-sm {{ $current==='en' ? 'bg-white text-slate-800' : 'bg-white/10 text-white' }}">
        <img src="{{ asset('assets/flags/english-flag.png') }}" alt="English" loading="lazy" class="w-5 h-3 object-cover rounded-sm">
        <span class="sr-only">English</span>
    </a>

    <a href="{{ route('locale.set', ['locale' => 'km']) }}" title="Khmer" class="inline-flex items-center gap-2 px-2 py-1 rounded-md text-sm {{ $current==='km' ? 'bg-white text-slate-800' : 'bg-white/10 text-white' }}">
        <img src="{{ asset('assets/flags/khmer-flag.png') }}" alt="Khmer" loading="lazy" class="w-5 h-3 object-cover rounded-sm">
        <span class="sr-only">Khmer</span>
    </a>

    <a href="{{ route('locale.set', ['locale' => 'zh']) }}" title="中文" class="inline-flex items-center gap-2 px-2 py-1 rounded-md text-sm {{ $current==='zh' ? 'bg-white text-slate-800' : 'bg-white/10 text-white' }}">
        <img src="{{ asset('assets/flags/china-flag.png') }}" alt="中文" loading="lazy" class="w-5 h-3 object-cover rounded-sm">
        <span class="sr-only">中文</span>
    </a>
</div>


