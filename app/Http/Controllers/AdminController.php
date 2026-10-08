<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCourseTitleRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $query = Course::with('author:id,name,role')
            ->withCount('likes')
            ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $user->id)]);

        if ($this->isModeratorOnly($user)) {
            $query->whereHas('author', fn($q) => $q->whereNotIn('role', ['superadmin', 'admin']));
        }

        $courses = $query->latest()->paginate(30);

        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function updateCourse(UpdateCourseTitleRequest $request, Course $course): JsonResponse
    {
        $course->update(['title' => $request->validated('title')]);

        return response()->json(['success' => true, 'course' => $course]);
    }

    public function destroyCourse(Course $course): JsonResponse
    {
        $course->load('author:id,role');

        if ($response = $this->guardAgainstManagingCourse($course)) {
            return $response;
        }

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json(['success' => true]);
    }

    public function approve(Course $course): JsonResponse
    {
        $course->load('author:id,role');

        if ($response = $this->guardAgainstManagingCourse($course)) {
            return $response;
        }

        $course->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json(['success' => true, 'course' => $course]);
    }

    public function users(): JsonResponse
    {
        $this->abortIfModeratorOnly();

        $users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->latest()
            ->paginate(30);

        return response()->json(['success' => true, 'users' => $users]);
    }

    public function toggleBlock(User $user): JsonResponse
    {
        $this->abortIfModeratorOnly();

        if ($response = $this->guardAgainstManagingStaff($user, 'Cannot manage admins')) {
            return $response;
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
        ]);
    }

    public function destroyUser(User $user): JsonResponse
    {
        $this->abortIfModeratorOnly();

        if ($response = $this->guardAgainstManagingStaff($user, 'Cannot delete admins')) {
            return $response;
        }

        $user->courses->each(
            fn(Course $course) => $course->image && Storage::disk('public')->delete($course->image)
        );

        $user->delete();

        return response()->json(['success' => true]);
    }

    private function isModeratorOnly(User $user): bool
    {
        return $user->isModerator() && !$user->isAdmin();
    }

    private function abortIfModeratorOnly(): void
    {
        abort_if($this->isModeratorOnly(Auth::user()), 403);
    }

    private function guardAgainstManagingCourse(Course $course): ?JsonResponse
    {
        $currentUser = Auth::user();
        $author = $course->author;

        if ($this->isModeratorOnly($currentUser) && $author && $author->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Moderators cannot manage courses created by admins.',
            ], 403);
        }

        if ($author?->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return null;
    }

    private function guardAgainstManagingStaff(User $target, string $adminMessage): ?JsonResponse
    {
        $currentUser = Auth::user();

        if ($target->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($target->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['message' => $adminMessage], 403);
        }

        return null;
    }
}
