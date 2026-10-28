<div class="installer-actions">
    @if ($previous)
        <a class="btn btn-outline-secondary" href="{{ route('install.step', ['step' => $previous]) }}"><x-icon name="arrow-left" class="me-1" /> Geri</a>
    @else
        <span></span>
    @endif
    <button class="btn btn-primary" type="submit" @disabled(! empty($disabled)) @if (! empty($busy)) data-busy="{{ $busy }}" @endif>
        {{ $label ?? 'Kaydet ve Devam Et' }} <x-icon name="arrow-right" class="ms-1" />
    </button>
</div>
