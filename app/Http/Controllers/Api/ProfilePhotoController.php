<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProfilePhotoRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Upload + preview profile photos for users and teams.
 *
 * Storage strategy:
 * - Files are stored on the local disk (storage/app/...)
 * - Preview endpoints stream the file via controller, so no public symlink is required.
 */
class ProfilePhotoController extends Controller
{
    /**
     * Upload/replace a user's profile photo.
     * Admin: any user. Team leader: users in same team. User: self only (UserPolicy@update).
     *
     * POST /api/users/{user}/profile-photo (multipart/form-data: photo=<file>)
     */
    public function uploadUser(UploadProfilePhotoRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $file = $request->file('photo');
        $path = $file->store("profile_photos/users/{$user->id}", 'local');

        // Delete previous file if we’re replacing it.
        if (!empty($user->profile_photo_path) && $user->profile_photo_path !== $path) {
            Storage::disk('local')->delete($user->profile_photo_path);
        }

        $user->update(['profile_photo_path' => $path]);

        return api_success([
            'user_id' => $user->id,
            'profile_photo_url' => url("/api/users/{$user->id}/profile-photo"),
        ], 'Profile photo uploaded.', 200);
    }

    /**
     * Preview a user's profile photo (streams the file).
     * Uses UserPolicy@view so users can only see allowed users.
     *
     * GET /api/users/{user}/profile-photo
     */
    public function previewUser(Request $request, User $user)
    {
        $this->authorize('view', $user);

        if (empty($user->profile_photo_path) || !Storage::disk('local')->exists($user->profile_photo_path)) {
            return api_error('Profile photo not found.', 404);
        }

        $absolutePath = Storage::disk('local')->path($user->profile_photo_path);
        return response()->file($absolutePath, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Upload/replace a team's profile photo.
     * Admin only (TeamPolicy@update + route middleware role:admin).
     *
     * POST /api/teams/{team}/profile-photo (multipart/form-data: photo=<file>)
     */
    public function uploadTeam(UploadProfilePhotoRequest $request, Team $team)
    {
        $this->authorize('update', $team);

        $file = $request->file('photo');
        $path = $file->store("profile_photos/teams/{$team->id}", 'local');

        if (!empty($team->profile_photo_path) && $team->profile_photo_path !== $path) {
            Storage::disk('local')->delete($team->profile_photo_path);
        }

        $team->update(['profile_photo_path' => $path]);

        return api_success([
            'team_id' => $team->id,
            'profile_photo_url' => url("/api/teams/{$team->id}/profile-photo"),
        ], 'Profile photo uploaded.', 200);
    }

    /**
     * Preview a team's profile photo (streams the file).
     * TeamPolicy@view allows all authenticated users to view teams.
     *
     * GET /api/teams/{team}/profile-photo
     */
    public function previewTeam(Request $request, Team $team)
    {
        $this->authorize('view', $team);

        if (empty($team->profile_photo_path) || !Storage::disk('local')->exists($team->profile_photo_path)) {
            return api_error('Profile photo not found.', 404);
        }

        $absolutePath = Storage::disk('local')->path($team->profile_photo_path);
        return response()->file($absolutePath, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

