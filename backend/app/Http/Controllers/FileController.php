<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use App\Models\StorageQuota;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Allowed file types
     */
    private const ALLOWED_TYPES = [
        // Office files
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    /**
     * Get all files with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $category = $request->get('category');
        $search = $request->get('search');
        $teamId = $request->get('team_id');

        $query = UploadedFile::with(['team', 'user'])->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where('original_name', 'like', "%{$search}%");
        }

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        $files = $query->paginate($perPage);

        return response()->json($files);
    }

    /**
     * Upload a new file
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max per file
            'uploaded_by' => 'nullable|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $file = $request->file('file');
        
        // Check file type
        if (!in_array($file->getMimeType(), self::ALLOWED_TYPES)) {
            return response()->json([
                'message' => 'File type not allowed. Only office files and images are permitted.'
            ], 422);
        }

        // Check storage quota
        $quota = StorageQuota::first();
        if (!$quota->hasEnoughSpace($file->getSize())) {
            return response()->json([
                'message' => 'Storage quota exceeded. Please delete some files first.',
                'available_space' => $quota->available_space,
                'required_space' => $file->getSize(),
            ], 507);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $storedName = Str::uuid() . '.' . $extension;
        
        // Store file
        $path = $file->storeAs('uploads', $storedName, 'public');

        // Create database record
        $uploadedFile = UploadedFile::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'file_type' => $extension,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->input('uploaded_by', 'Unknown'),
            'description' => $request->input('description'),
            'category' => $request->input('category', 'general'),
            'team_id' => $request->input('team_id'),
            'user_id' => auth()->id(),
        ]);

        // Update storage quota
        $quota->increment('used_space', $file->getSize());

        return response()->json([
            'message' => 'File uploaded successfully',
            'file' => $uploadedFile,
        ], 201);
    }

    /**
     * Get a specific file
     */
    public function show($id): JsonResponse
    {
        $file = UploadedFile::findOrFail($id);
        return response()->json($file);
    }

    /**
     * Download a file
     */
    public function download($id)
    {
        $file = UploadedFile::findOrFail($id);
        $path = storage_path('app/public/uploads/' . $file->stored_name);

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($path, $file->original_name);
    }

    /**
     * Update file details
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'description' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $file = UploadedFile::findOrFail($id);
        $file->update($request->only(['description', 'category']));

        return response()->json([
            'message' => 'File updated successfully',
            'file' => $file,
        ]);
    }

    /**
     * Delete a file
     */
    public function destroy($id): JsonResponse
    {
        $file = UploadedFile::findOrFail($id);
        
        // Delete physical file
        Storage::disk('public')->delete('uploads/' . $file->stored_name);
        
        // Update storage quota
        $quota = StorageQuota::first();
        $quota->decrement('used_space', $file->file_size);
        
        // Soft delete database record
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
        ]);
    }

    /**
     * Get storage statistics
     */
    public function storageStats(): JsonResponse
    {
        $quota = StorageQuota::first();
        
        return response()->json([
            'total_quota' => $quota->total_quota,
            'used_space' => $quota->used_space,
            'available_space' => $quota->available_space,
            'percentage_used' => $quota->percentage_used,
            'total_files' => UploadedFile::count(),
        ]);
    }
}
