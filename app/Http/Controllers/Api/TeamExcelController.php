<?php

namespace App\Http\Controllers\Api;

use App\Exports\TeamsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportTeamsRequest;
use App\Imports\TeamsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Excel import/export for Teams and Members.
 *
 * - GET  /api/export/teams  -> downloads teams.xlsx
 * - POST /api/import/teams  -> reads uploaded file and creates teams + members
 */
class TeamExcelController extends Controller
{
    /**
     * Export teams data in Excel format.
     *
     * GET /api/export/teams
     * Admin only (enforced by route middleware).
     */
    public function export(Request $request)
    {
    
        // Default: download Excel file.
        return Excel::download(new TeamsExport(), 'teams.xlsx');
    }

    /**
     * Import teams + members from Excel.
     *
     * POST /api/import/teams
     * Body: multipart/form-data file=<xlsx>
     * Admin only (enforced by route middleware).
     */
    public function import(ImportTeamsRequest $request)
    {
        $import = new TeamsImport();
        Excel::import($import, $request->file('file'));

        return api_success([
            'teams_created' => $import->teamsCreated,
            'users_created' => $import->usersCreated,
            'users_updated' => $import->usersUpdated,
            'rows_skipped'  => $import->rowsSkipped,
            'note' => 'New users created by import use default password: password123',
        ], 'Import completed.', 200);
    }
}

