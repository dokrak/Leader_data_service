<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UploadedFile;
use App\Models\StorageQuota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;

class FileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize storage quota
        StorageQuota::create([
            'total_quota' => 10737418240, // 10GB
            'used_space' => 0,
        ]);

        Storage::fake('public');
    }

    public function test_can_get_storage_stats(): void
    {
        $response = $this->getJson('/api/storage/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_quota',
                'used_space',
                'available_space',
                'percentage_used',
                'total_files',
            ])
            ->assertJson([
                'total_quota' => 10737418240,
                'used_space' => 0,
                'total_files' => 0,
            ]);
    }

    public function test_can_list_files(): void
    {
        $response = $this->getJson('/api/files');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data',
                'total',
            ]);
    }

    public function test_can_upload_file(): void
    {
        $file = HttpUploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/files', [
            'file' => $file,
            'uploaded_by' => 'Test User',
            'description' => 'Test document',
            'category' => 'documents',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'file' => [
                    'id',
                    'original_name',
                    'stored_name',
                    'file_type',
                    'mime_type',
                    'file_size',
                    'uploaded_by',
                    'description',
                    'category',
                ],
            ])
            ->assertJson([
                'message' => 'File uploaded successfully',
            ]);

        $this->assertDatabaseHas('uploaded_files', [
            'original_name' => 'document.pdf',
            'uploaded_by' => 'Test User',
        ]);
    }

    public function test_cannot_upload_invalid_file_type(): void
    {
        $file = HttpUploadedFile::fake()->create('video.mp4', 100, 'video/mp4');

        $response = $this->postJson('/api/files', [
            'file' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'File type not allowed. Only office files and images are permitted.',
            ]);
    }

    public function test_can_get_specific_file(): void
    {
        $uploadedFile = UploadedFile::create([
            'original_name' => 'test.pdf',
            'stored_name' => 'test-uuid.pdf',
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_by' => 'Test User',
            'category' => 'documents',
        ]);

        $response = $this->getJson("/api/files/{$uploadedFile->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $uploadedFile->id,
                'original_name' => 'test.pdf',
            ]);
    }

    public function test_can_update_file_details(): void
    {
        $uploadedFile = UploadedFile::create([
            'original_name' => 'test.pdf',
            'stored_name' => 'test-uuid.pdf',
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_by' => 'Test User',
            'category' => 'documents',
        ]);

        $response = $this->putJson("/api/files/{$uploadedFile->id}", [
            'description' => 'Updated description',
            'category' => 'reports',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'File updated successfully',
            ]);

        $this->assertDatabaseHas('uploaded_files', [
            'id' => $uploadedFile->id,
            'description' => 'Updated description',
            'category' => 'reports',
        ]);
    }

    public function test_can_delete_file(): void
    {
        $uploadedFile = UploadedFile::create([
            'original_name' => 'test.pdf',
            'stored_name' => 'test-uuid.pdf',
            'file_type' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_by' => 'Test User',
            'category' => 'documents',
        ]);

        $response = $this->deleteJson("/api/files/{$uploadedFile->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'File deleted successfully',
            ]);

        $this->assertSoftDeleted('uploaded_files', [
            'id' => $uploadedFile->id,
        ]);
    }

    public function test_cannot_upload_when_quota_exceeded(): void
    {
        // Set quota to nearly full
        $quota = StorageQuota::first();
        $quota->update(['used_space' => 10737418240 - 50]); // 50 bytes left

        $file = HttpUploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/files', [
            'file' => $file,
        ]);

        $response->assertStatus(507)
            ->assertJsonStructure([
                'message',
                'available_space',
                'required_space',
            ]);
    }
}
