<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    /**
     * Get all teams
     */
    public function index(): JsonResponse
    {
        $teams = Team::where('is_active', true)
            ->withCount('users', 'files')
            ->orderBy('name')
            ->get();

        return response()->json($teams);
    }

    /**
     * Get team details with statistics
     */
    public function show($id): JsonResponse
    {
        $team = Team::with(['users', 'files'])->findOrFail($id);

        $stats = [
            'total_files' => $team->files()->count(),
            'total_size' => $team->files()->sum('file_size'),
            'total_members' => $team->users()->count(),
            'recent_files' => $team->files()
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ];

        return response()->json([
            'team' => $team,
            'stats' => $stats,
        ]);
    }

    /**
     * Get team files
     */
    public function files(Request $request, $id): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');

        $query = UploadedFile::where('team_id', $id)
            ->with(['user', 'team'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('original_name', 'like', "%{$search}%");
        }

        $files = $query->paginate($perPage);

        return response()->json($files);
    }

    /**
     * Get team dashboard stats
     */
    public function dashboard($id): JsonResponse
    {
        $team = Team::findOrFail($id);

        $stats = [
            'team' => $team,
            'total_files' => $team->files()->count(),
            'total_size' => $team->files()->sum('file_size'),
            'total_members' => $team->users()->count(),
            'files_this_month' => $team->files()
                ->whereMonth('created_at', now()->month)
                ->count(),
            'categories' => $team->files()
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'recent_uploads' => $team->files()
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}
