<?php

namespace App\Http\Controllers;

use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class LearningController extends Controller
{
    private function getCurrentUser()
    {
        return auth()->user();
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function storePublicUpload(UploadedFile $file, string $directory): string
    {
        $targetDirectory = public_path('storage/' . $directory);
        File::ensureDirectoryExists($targetDirectory);

        $filename = $file->hashName();
        $file->move($targetDirectory, $filename);

        return $directory . '/' . $filename;
    }

    // Index - View all materials (for users)
    public function index()
    {
        $query = LearningMaterial::query();
        
        // Search functionality
        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('content', 'like', '%' . request('search') . '%');
            });
        }
        
        // Filter by type
        if (request('type')) {
            $query->where('material_type', request('type'));
        }
        
        $materials = $query->latest()->paginate(12);
        return view('learning.index', compact('materials'));
    }

    // Show single material
    public function show($id)
    {
        $material = LearningMaterial::findOrFail($id);
        return view('learning.show', compact('material'));
    }

    // Admin functions for midwives
    public function adminIndex()
    {
        if (!$this->getCurrentUser()?->isMidwife()) {
            abort(403);
        }

        $query = LearningMaterial::query();

        // Search functionality
        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('content', 'like', '%' . request('search') . '%');
            });
        }

        // Filter by type
        if (request('type')) {
            $query->where('material_type', request('type'));
        }

        $materials = $query->latest()->paginate(10);
        return view('midwife.learning.index', compact('materials'));
    }

    public function create()
    {
        if (!$this->getCurrentUser()?->isMidwife()) {
            abort(403);
        }

        return view('midwife.learning.create');
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentUser()?->isMidwife()) {
            abort(403);
        }

        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'material_type' => 'required|in:article,link,file,video,quiz',
            'link_url'      => 'nullable|url|required_if:material_type,link',
            'video_url'     => 'nullable|required_if:material_type,video',
            'category'      => 'nullable|in:general,nutrition,warning-signs,family-planning,postpartum,pregnancy-guide',
            'week_number'   => 'nullable|integer|min:1|max:42',
            'quiz_data'     => 'nullable|json',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,avi,mov,wmv,mp3,wav,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:102400', // 100MB max
        ]);

        $data = $request->except('image', 'file', 'quiz_data');
        $data['category']   = $request->category ?? 'general';
        $data['quiz_data']  = $request->quiz_data ? json_decode($request->quiz_data, true) : null;

        if ($request->hasFile('image')) {
            $data['image'] = $this->storePublicUpload($request->file('image'), 'learning_materials');
        }

        if ($request->hasFile('file')) {
            $data['file'] = $this->storePublicUpload($request->file('file'), 'learning_files');
        }

        LearningMaterial::create($data);

        return redirect()->route('midwife.learning.index')
            ->with('success', 'Learning material created successfully');
    }

    public function edit($id)
    {
        if (!$this->getCurrentUser()?->isMidwife()) {
            abort(403);
        }

        $material = LearningMaterial::findOrFail($id);
        return view('midwife.learning.edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentUser()?->isMidwife()) {
            abort(403);
        }

        $material = LearningMaterial::findOrFail($id);

        $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'material_type' => 'required|in:article,link,file,video,quiz',
            'link_url'      => 'nullable|url|required_if:material_type,link',
            'video_url'     => 'nullable|required_if:material_type,video',
            'category'      => 'nullable|in:general,nutrition,warning-signs,family-planning,postpartum,pregnancy-guide',
            'week_number'   => 'nullable|integer|min:1|max:42',
            'quiz_data'     => 'nullable|json',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file'          => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,avi,mov,wmv,mp3,wav,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:102400', // 100MB max
        ]);

        $data = $request->except('image', 'file', 'quiz_data');
        $data['category']   = $request->category ?? $material->category;
        $data['quiz_data']  = $request->quiz_data ? json_decode($request->quiz_data, true) : $material->quiz_data;

        if ($request->hasFile('image')) {
            $data['image'] = $this->storePublicUpload($request->file('image'), 'learning_materials');
        }

        if ($request->hasFile('file')) {
            $data['file'] = $this->storePublicUpload($request->file('file'), 'learning_files');
        }

        $material->update($data);

        return redirect()->route('midwife.learning.index')
            ->with('success', 'Learning material updated successfully');
    }

    public function destroy($id)
    {
        if (!$this->getCurrentUser()?->isMidwife()) {
            abort(403);
        }

        $material = LearningMaterial::findOrFail($id);
        $material->delete();

        return redirect()->route('midwife.learning.index')
            ->with('success', 'Learning material deleted successfully');
    }

    // Filter by type
    public function articles()
    {
        $materials = LearningMaterial::articles()->latest()->paginate(12);
        return view('learning.index', compact('materials'));
    }

    public function links()
    {
        $materials = LearningMaterial::links()->latest()->paginate(12);
        return view('learning.index', compact('materials'));
    }

    public function files()
    {
        $materials = LearningMaterial::files()->latest()->paginate(12);
        return view('learning.index', compact('materials'));
    }

    // Quiz submission
    public function submitQuiz(Request $request, $id)
    {
        $material = LearningMaterial::whereNotNull('quiz_data')->findOrFail($id);
        $questions = $material->quiz_data ?? [];
        $answers   = $request->input('answers', []);
        $score = 0;
        $total = count($questions);
        $results = [];

        foreach ($questions as $i => $q) {
            $correct = $q['answer'] ?? null;
            $given   = $answers[$i] ?? null;
            $isRight = ($given === $correct);
            if ($isRight) $score++;
            $results[] = [
                'question' => $q['question'],
                'correct'  => $correct,
                'given'    => $given,
                'is_right' => $isRight,
            ];
        }

        return view('learning.quiz-result', compact('material', 'score', 'total', 'results'));
    }

    // Pregnancy week-by-week guide
    public function weekGuide($week)
    {
        $week      = (int)$week;
        $materials = LearningMaterial::weekGuide($week)->latest()->get();
        return view('learning.week-guide', compact('materials', 'week'));
    }

    // BHW Learning Materials
    public function bhwIndex()
    {
        if (!$this->getCurrentUser()?->isBhw()) {
            abort(403);
        }

        $query = LearningMaterial::query();
        if (request('search')) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('content', 'like', '%' . request('search') . '%');
            });
        }
        if (request('type')) {
            $query->where('material_type', request('type'));
        }
        if (request('category')) {
            $query->where('category', request('category'));
        }

        $materials = $query->latest()->paginate(12);
        return view('learning.index', compact('materials'));
    }

    public function bhwShow($id)
    {
        if (!$this->getCurrentUser()?->isBhw()) {
            abort(403);
        }
        $material = LearningMaterial::findOrFail($id);
        return view('learning.show', compact('material'));
    }
}
