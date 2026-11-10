<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Certificate;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function show(string $courseSlug, ?string $lessonSlug = null): View|RedirectResponse
    {
        $course = Course::with(['category', 'instructor', 'lessons'])
            ->where('slug', $courseSlug)
            ->firstOrFail();

        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'progress_percent' => 0,
            ]);
            $course->increment('students_count');

            \App\Services\NotificationService::send(
                $user,
                'Pendaftaran Kursus Berhasil',
                "Anda telah berhasil terdaftar pada kursus '{$course->title}'. Selamat belajar!",
                route('classroom.show', $course->slug),
                'course',
                'graduation-cap'
            );
        }

        // Determine active lesson with quiz & assignment relations
        if ($lessonSlug) {
            $activeLesson = $course->lessons()
                ->with(['quiz.questions', 'assignment'])
                ->where('slug', $lessonSlug)
                ->firstOrFail();
        } else {
            $completedLessonIds = LessonProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            $activeLesson = $course->lessons()
                ->with(['quiz.questions', 'assignment'])
                ->whereNotIn('id', $completedLessonIds)
                ->orderBy('order')
                ->first() ?? $course->lessons()->with(['quiz.questions', 'assignment'])->orderBy('order')->first();

            if ($activeLesson) {
                return redirect()->route('classroom.show', [$course->slug, $activeLesson->slug]);
            }
        }

        $completedLessonsMap = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->flip()
            ->toArray();

        $allLessons = $course->lessons()->orderBy('order')->get();
        $currentIndex = $allLessons->search(fn($item) => $item->id === $activeLesson->id);
        
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < ($allLessons->count() - 1) ? $allLessons[$currentIndex + 1] : null;

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // Quiz attempt data
        $latestQuizAttempt = null;
        if ($activeLesson->quiz) {
            $latestQuizAttempt = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $activeLesson->quiz->id)
                ->latest('id')
                ->first();
        }

        // Assignment submission data
        $userSubmission = null;
        if ($activeLesson->assignment) {
            $userSubmission = AssignmentSubmission::where('user_id', $user->id)
                ->where('assignment_id', $activeLesson->assignment->id)
                ->first();
        }

        return view('classroom.show', compact(
            'course',
            'activeLesson',
            'enrollment',
            'completedLessonsMap',
            'prevLesson',
            'nextLesson',
            'certificate',
            'latestQuizAttempt',
            'userSubmission'
        ));
    }

    public function toggleComplete(Request $request, string $courseSlug, string $lessonSlug): RedirectResponse
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = $course->lessons()->where('slug', $lessonSlug)->firstOrFail();
        $user = Auth::user();

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => false]
        );

        $progress->is_completed = !$progress->is_completed;
        $progress->completed_at = $progress->is_completed ? now() : null;
        $progress->save();

        $this->updateCourseProgress($user->id, $course);

        if ($progress->is_completed) {
            $allLessons = $course->lessons()->orderBy('order')->get();
            $currentIndex = $allLessons->search(fn($item) => $item->id === $lesson->id);
            $nextLesson = $currentIndex < ($allLessons->count() - 1) ? $allLessons[$currentIndex + 1] : null;

            if ($nextLesson) {
                return redirect()->route('classroom.show', [$course->slug, $nextLesson->slug])
                    ->with('success', 'Materi berhasil diselesaikan!');
            }

            return redirect()->route('classroom.show', [$course->slug, $lesson->slug])
                ->with('success', 'Selamat! Anda telah menuntaskan seluruh materi kursus ini 🎉');
        }

        return redirect()->route('classroom.show', [$course->slug, $lesson->slug]);
    }

    public function submitQuiz(Request $request, string $courseSlug, string $lessonSlug): RedirectResponse
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = $course->lessons()->with('quiz.questions')->where('slug', $lessonSlug)->firstOrFail();
        $quiz = $lesson->quiz;

        if (!$quiz) {
            return back()->with('error', 'Kuis tidak ditemukan.');
        }

        $user = Auth::user();
        $answers = $request->input('answers', []);
        $correctCount = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $q) {
            $submitted = $answers[$q->id] ?? null;
            if ($submitted && strtoupper($submitted) === strtoupper($q->correct_answer)) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'is_passed' => $isPassed,
            'submitted_answers' => $answers,
            'completed_at' => now(),
        ]);

        if ($isPassed) {
            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['is_completed' => true, 'completed_at' => now()]
            );

            $this->updateCourseProgress($user->id, $course);

            return redirect()->route('classroom.show', [$course->slug, $lesson->slug])
                ->with('success', "Luar biasa! Anda LULUS dengan skor {$score}/100.");
        }

        return redirect()->route('classroom.show', [$course->slug, $lesson->slug])
            ->with('info', "Skor Anda {$score}/100 (Ambang kelulusan: {$quiz->passing_score}%). Silakan pelajari kembali materi dan coba ulang.");
    }

    public function submitAssignment(Request $request, string $courseSlug, string $lessonSlug): RedirectResponse
    {
        $request->validate([
            'submission_notes' => ['required', 'string', 'min:10'],
            'file_url' => ['nullable', 'url'],
        ], [
            'submission_notes.required' => 'Catatan penjelasan tugas wajib diisi minimal 10 karakter.',
        ]);

        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = $course->lessons()->with('assignment')->where('slug', $lessonSlug)->firstOrFail();
        $assignment = $lesson->assignment;

        if (!$assignment) {
            return back()->with('error', 'Tugas tidak ditemukan.');
        }

        $user = Auth::user();

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $user->id],
            [
                'submission_notes' => $request->submission_notes,
                'file_url' => $request->file_url,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $this->updateCourseProgress($user->id, $course);

        return redirect()->route('classroom.show', [$course->slug, $lesson->slug])
            ->with('success', 'Tugas praktik berhasil dikirimkan dan modul ditandai selesai!');
    }

    private function updateCourseProgress(int $userId, Course $course): void
    {
        $totalLessons = $course->lessons()->count();
        $completedCount = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $newPercent = $totalLessons > 0 ? (int) round(($completedCount / $totalLessons) * 100) : 0;

        $enrollment = Enrollment::where('user_id', $userId)->where('course_id', $course->id)->first();
        if ($enrollment) {
            $enrollment->progress_percent = $newPercent;
            if ($newPercent >= 100 && !$enrollment->completed_at) {
                $enrollment->completed_at = now();

                if ($course->certificate_available) {
                    $cert = Certificate::firstOrCreate(
                        ['user_id' => $userId, 'course_id' => $course->id],
                        [
                            'certificate_number' => 'CERT-LMS-' . date('Y') . '-' . str_pad($course->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT),
                            'issued_at' => now(),
                            'verification_hash' => Str::upper(Str::random(12)),
                        ]
                    );

                    \App\Services\NotificationService::send(
                        $userId,
                        'Selamat! Kursus Selesai & E-Sertifikat Terbit',
                        "Anda telah menyelesaikan seluruh modul pada kursus '{$course->title}'. E-Sertifikat Anda telah terbit dan siap diunduh.",
                        route('certificates.show', $cert->certificate_number),
                        'certificate',
                        'award'
                    );
                }
            }
            $enrollment->save();
        }
    }
}
