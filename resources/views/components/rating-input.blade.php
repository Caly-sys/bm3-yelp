@props(['name', 'label', 'value' => 0])

<div class="rating-input-group">
    <label class="rating-input-label">{{ $label }}</label>
    <div class="rating-input-stars" data-name="{{ $name }}">
        @for($i = 1; $i <= 5; $i++)
            <button type="button"
                class="star-btn {{ $i <= $value ? 'active' : '' }}"
                data-value="{{ $i }}"
                aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}"
                title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                ★
            </button>
        @endfor
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" required>
    </div>
    @error($name)
        <p class="input-error">{{ $message }}</p>
    @enderror
</div>
