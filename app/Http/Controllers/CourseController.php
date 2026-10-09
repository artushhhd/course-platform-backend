<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Course;
use App\Models\CourseComment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $courses = Course::with(['author:id,name', 'comments.user'])
            ->withCount('likes')
            ->when($user, fn($q) => $q->withExists([
                'likes as is_liked' => fn($q) => $q->where('user_id', $user->id),
            ]))
            ->when(
                $user,
                fn($q) => $q->where(fn($q) => $q->where('status', 'published')->orWhere('user_id', $user->id)),
                fn($q) => $q->where('status', 'published'),
            )
            ->latest()
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $courses]);
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $user = auth('sanctum')->user();

        $this->ensureVisibleTo($course, $user);

        $course->load(['author:id,name', 'comments.user']);

        return response()->json(['success' => true, 'data' => $course]);
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['status'] = 'draft';
        $data['published_at'] = null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course = Course::create($data);

        return response()->json(['success' => true, 'data' => $course], 201);
    }

    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        Gate::authorize('update', $course);

        $data = $request->validated();

        if (isset($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $course->id);
        }

        if ($request->hasFile('image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($data);

        return response()->json(['success' => true, 'data' => $course]);
    }

    public function destroy(Course $course): JsonResponse
    {
        Gate::authorize('delete', $course);

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json(['success' => true]);
    }

    public function toggleLike(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        $this->ensureVisibleTo($course, $user);

        $result = $course->likes()->toggle($user->id);

        return response()->json([
            'success' => true,
            'is_liked' => count($result['attached']) > 0,
            'likes_count' => $course->likes()->count(),
        ]);
    }

    public function storeComment(StoreCommentRequest $request, Course $course): JsonResponse
    {
        $this->ensureVisibleTo($course, $request->user());

        $comment = CourseComment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'content' => $request->validated('content'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment->load('user'),
        ], 201);
    }

    private function ensureVisibleTo(Course $course, ?User $user): void
    {
        if (
            $course->status !== 'published'
            && (!$user || ($course->user_id !== $user->id && !$user->isStaff()))
        ) {
            abort(404);
        }
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'course';
        $original = $slug;
        $suffix = 2;

        while (Course::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$original}-" . $suffix++;
        }

        return $slug;
    }
}
