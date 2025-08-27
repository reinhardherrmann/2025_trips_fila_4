<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        <div>
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-error">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('warning'))
                <div class="alert alert-warning">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
