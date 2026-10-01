<x-layout title="Invalid Invitation">
    <section class="section">
        <div class="container container-sm">
            <div class="empty-state card" style="margin-top: 2rem;">
                <span class="empty-icon">❌</span>
                <h3>Invitation Not Valid</h3>
                <p>{{ $message }}</p>
                <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-2">Go to Home</a>
            </div>
        </div>
    </section>
</x-layout>
