@php($fx = \App\Models\SystemSetting::uiEffects())
@if (! empty($fx['bubble']['enabled']))

    <canvas id="bubbleEffectCanvas" aria-hidden="true" style="position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:-1;" data-accent="{{ $fx['accent'] }}" data-bubble="{{ json_encode($fx['bubble']) }}"></canvas>
@endif
