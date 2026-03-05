<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Exports teams and their members in a flat table format.
 *
 * Each row is one team-member pair:
 * - Teams with no members still produce 1 row (member fields blank).
 */
class TeamsExport implements FromCollection, WithHeadings, WithMapping
{
    /** @var \Illuminate\Support\Collection<int, \App\Models\Team> */
    private Collection $teams;

    public function __construct()
    {
        $this->teams = Team::with('users')->orderBy('id')->get();
    }

    public function collection(): Collection
    {
        return $this->teams->flatMap(function (Team $team) {
            if ($team->users->isEmpty()) {
                return collect([['team' => $team, 'user' => null]]);
            }
            return $team->users->map(fn ($user) => ['team' => $team, 'user' => $user]);
        })->values();
    }

    public function headings(): array
    {
        return [
            'team_id',
            'team_name',
            'team_profile_photo_url',
            'member_id',
            'member_name',
            'member_email',
            'member_role',
        ];
    }

    /** @param array{team:\App\Models\Team,user:?\App\Models\User} $row */
    public function map($row): array
    {
        /** @var \App\Models\Team $team */
        $team = $row['team'];
        /** @var \App\Models\User|null $user */
        $user = $row['user'];

        return [
            $team->id,
            $team->name,
            $team->profile_photo_path ? url("/api/teams/{$team->id}/profile-photo") : null,
            $user?->id,
            $user?->name,
            $user?->email,
            $user?->role,
        ];
    }
}

