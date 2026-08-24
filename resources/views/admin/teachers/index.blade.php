<x-layout title="Manage Teachers">
    <section class="section page-header-section">
        <div class="container">
            <div class="page-header-row">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="back-link">← Admin Dashboard</a>
                    <h1 class="page-title">Manage Teachers</h1>
                </div>
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">+ Add Teacher</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Reviews</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                            <tr>
                                <td>
                                    <a href="{{ route('teachers.show', $teacher) }}">{{ $teacher->name }}</a>
                                </td>
                                <td>{{ $teacher->subject }}</td>
                                <td>{{ $teacher->reviews_count }}</td>
                                <td class="admin-actions">
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                        onsubmit="return confirm('Delete {{ $teacher->name }}? All reviews will be deleted too.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm btn-danger-text">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">{{ $teachers->links() }}</div>
        </div>
    </section>
</x-layout>
