<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    /**
     * Display the release page
     */
    public function index()
    {
        try {
            return view('Release.index');
        } catch (\Exception $e) {
            return 'Release controller working, but view error: ' . $e->getMessage();
        }
    }
    
    /**
     * Display specific release details
     */
    public function show($folderName)
    {
        try {
            return view('Release.index', ['folderName' => $folderName]);
        } catch (\Exception $e) {
            return 'Release show working, but view error: ' . $e->getMessage();
        }
    }
}
