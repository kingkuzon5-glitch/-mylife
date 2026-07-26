@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-discipline-green']) }}>
        {{ $status }}
    </div>
@endif
