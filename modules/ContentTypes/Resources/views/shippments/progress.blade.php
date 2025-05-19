@php
    $totalLocations            = $locations->count() + ($from ? 1 : 0);
    $progressPercentagePerStep = 100 / ($totalLocations ?? 1);
    $deliveredLocations = $locations->filter(function ($location) {
        return $location->delivered_at;
    })->count() + ($from && $from->delivered_at ? 1 : 0);
    $progressPercentage = $deliveredLocations * $progressPercentagePerStep;
@endphp
<div class="row">
    <div class="col-lg-12 mt-4 mb-4">
        <div class="progress-container">
            <div class="progress-bar"></div>
            <div class="progress-fill" style="width: {{ $progressPercentage }}%;"></div>
            <div class="step-points">
                @if ($from)
                    <div class="step {{ $from->delivered_at ? 'active' : '' }}">
                        <span class="title">{{ __('contenttypes::inputs.source.label') }}</span>
                    </div>
                @endif
                @foreach ($locations as $index => $location)
                    <div class="step {{ $location->delivered_at ? 'active' : '' }}">
                        <span class="title">
                            {{ __('contenttypes::strings.shippments.destination') }} #{{ $index + 1 }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>