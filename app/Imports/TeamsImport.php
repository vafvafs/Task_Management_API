<?php

namespace App\Imports;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Imports teams and members from an Excel file.
 *
 * Expected headings (case-insensitive):
 * - team_name (required)
 * - member_email (optional)
 * - member_name (optional; defaults to email prefix)
 * - member_role (optional; allowed: user, team_leader; admin is ignored)
 *
 * Behavior:
 * - Creates teams by name (firstOrCreate)
 * - Creates users by email (firstOrCreate); if user exists, updates team_id
 * - Default password for created users: password123 (for easy Postman testing)
 */
class TeamsImport implements ToCollection, WithHeadingRow
{
    public int $teamsCreated = 0;
    public int $usersCreated = 0;
    public int $usersUpdated = 0;
    public int $rowsSkipped = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $teamName = isset($row['team_name']) ? trim((string) $row['team_name']) : '';
            if ($teamName === '') {
                $this->rowsSkipped++;
                continue;
            }

            $team = Team::firstOrCreate(['name' => $teamName]);
            if ($team->wasRecentlyCreated) {
                $this->teamsCreated++;
            }

            $email = isset($row['member_email']) ? strtolower(trim((string) $row['member_email'])) : '';
            if ($email === '') {
                continue; // team-only row
            }

            $name = isset($row['member_name']) ? trim((string) $row['member_name']) : '';
            if ($name === '') {
                $name = Str::before($email, '@') ?: 'Member';
            }

            $role = isset($row['member_role']) ? trim((string) $row['member_role']) : '';
            $role = in_array($role, [User::ROLE_USER, User::ROLE_TEAM_LEADER], true) ? $role : User::ROLE_USER;

            $existing = User::where('email', $email)->first();
            if ($existing) {
                $existing->update([
                    'name' => $existing->name ?: $name,
                    'role' => $existing->role === User::ROLE_ADMIN ? $existing->role : $role,
                    'team_id' => $team->id,
                ]);
                $this->usersUpdated++;
                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => $role,
                'team_id' => $team->id,
            ]);
            $this->usersCreated++;
        }
    }
}

