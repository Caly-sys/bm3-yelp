<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::withCount('reviews')->withAvg('reviews', 'overall_rating');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject')) {
            $query->where('subject', $request->input('subject'));
        }

        $sort = $request->input('sort', 'name_asc');
        match ($sort) {
            'name_desc' => $query->orderByDesc('name'),
            'reviews_desc' => $query->orderByDesc('reviews_count'),
            'rating_desc' => $query->orderByDesc('reviews_avg_overall_rating'),
            default => $query->orderBy('name'),
        };

        $teachers = $query->paginate(15)->withQueryString();
        $subjects = Teacher::distinct()->pluck('subject')->filter()->values();

        return view('admin.teachers.index', compact('teachers', 'subjects'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        Teacher::create($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher added successfully!');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted.');
    }
}
