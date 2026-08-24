@props(['averages'])

<div class="rating-breakdown">
    @php
        $categories = [
            'overall' => 'Overall',
            'teaching' => 'Teaching Quality',
            'explanation' => 'Explanation',
            'fairness' => 'Fairness',
            'workload' => 'Workload',
        ];
    @endphp

    @foreach($categories as $key => $label)
        <div class="breakdown-row">
            <span class="breakdown-label">{{ $label }}</span>
            <div class="breakdown-bar-container">
                <div class="breakdown-bar" style="width: {{ ($averages[$key] / 5) * 100 }}%"></div>
            </div>
            <span class="breakdown-value">{{ number_format($averages[$key], 1) }}</span>
        </div>
    @endforeach
</div>
