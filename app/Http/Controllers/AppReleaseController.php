<?php

namespace App\Http\Controllers;

use App\Models\AppRelease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $releases = AppRelease::orderBy('release_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('app-releases.index', compact('releases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app-releases.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|max:50|unique:app_releases,version,NULL,id,platform,' . $request->platform,
            'platform' => 'required|in:android,ios',
            'build_number' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'release_date' => 'nullable|date',
            'changelog' => 'nullable|array',
            'is_force_update' => 'boolean',
            'file' => 'required|file|max:50000', // Max 50MB
            'status' => 'required|in:draft,published,archived',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $fileData = AppRelease::storeFile($request->file('file'), $request->platform);
        }

        $release = AppRelease::create([
            'version' => $request->version,
            'platform' => $request->platform,
            'build_number' => $request->build_number,
            'description' => $request->description,
            'release_date' => $request->release_date ?: now(),
            'changelog' => $request->changelog,
            'is_force_update' => $request->boolean('is_force_update'),
            'status' => $request->status,
            'file_name' => $fileData['file_name'],
            'file_path' => $fileData['file_path'],
            'file_hash' => $fileData['file_hash'],
            'file_size' => $fileData['file_size'],
        ]);

        // Set as latest if published
        if ($request->status === 'published') {
            $release->setAsLatest();
        }

        return redirect()
            ->route('app-releases.index')
            ->with('success', 'Release created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppRelease $appRelease)
    {
        return view('app-releases.show', compact('appRelease'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppRelease $appRelease)
    {
        return view('app-releases.edit', compact('appRelease'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppRelease $appRelease)
    {
        $request->validate([
            'version' => 'required|string|max:50|unique:app_releases,version,' . $appRelease->id . ',id,platform,' . $request->platform,
            'platform' => 'required|in:android,ios',
            'build_number' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'release_date' => 'nullable|date',
            'changelog' => 'nullable|array',
            'is_force_update' => 'boolean',
            'file' => 'nullable|file|max:50000', // Max 50MB
            'status' => 'required|in:draft,published,archived',
        ]);

        $data = [
            'version' => $request->version,
            'platform' => $request->platform,
            'build_number' => $request->build_number,
            'description' => $request->description,
            'release_date' => $request->release_date ?: $appRelease->release_date,
            'changelog' => $request->changelog,
            'is_force_update' => $request->boolean('is_force_update'),
            'status' => $request->status,
        ];

        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            $appRelease->deleteFile();
            
            // Store new file
            $fileData = AppRelease::storeFile($request->file('file'), $request->platform);
            
            $data['file_name'] = $fileData['file_name'];
            $data['file_path'] = $fileData['file_path'];
            $data['file_hash'] = $fileData['file_hash'];
            $data['file_size'] = $fileData['file_size'];
        }

        $appRelease->update($data);

        // Update latest flag
        if ($request->status === 'published') {
            $appRelease->setAsLatest();
        } elseif ($appRelease->is_latest) {
            $appRelease->update(['is_latest' => false]);
        }

        return redirect()
            ->route('app-releases.index')
            ->with('success', 'Release updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppRelease $appRelease)
    {
        // Delete file from storage
        $appRelease->deleteFile();
        
        // Delete record
        $appRelease->delete();

        return redirect()
            ->route('app-releases.index')
            ->with('success', 'Release deleted successfully!');
    }

    /**
     * Download the release file
     */
    public function download(AppRelease $appRelease)
    {
        if (!$appRelease->fileExists()) {
            abort(404, 'File not found');
        }

        // Increment download count
        $appRelease->incrementDownloadCount();

        return Storage::disk('public')->download($appRelease->file_path, $appRelease->file_name);
    }

    /**
     * Toggle latest status
     */
    public function toggleLatest(AppRelease $appRelease)
    {
        if ($appRelease->status !== 'published') {
            return redirect()
                ->route('app-releases.index')
                ->with('error', 'Only published releases can be set as latest!');
        }

        if ($appRelease->is_latest) {
            $appRelease->update(['is_latest' => false]);
        } else {
            $appRelease->setAsLatest();
        }

        return redirect()
            ->route('app-releases.index')
            ->with('success', 'Latest status updated successfully!');
    }

    /**
     * Public download page for users
     */
    public function publicIndex()
    {
        try {
            $releases = AppRelease::published()
                ->orderBy('is_latest', 'desc')
                ->orderBy('release_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('app-releases.public', compact('releases'));
        } catch (\Exception $e) {
            return 'Public release page error: ' . $e->getMessage();
        }
    }

    /**
     * Public download endpoint
     */
    public function publicDownload(AppRelease $appRelease)
    {
        if ($appRelease->status !== 'published') {
            abort(404, 'Release not found');
        }

        return $this->download($appRelease);
    }
}
