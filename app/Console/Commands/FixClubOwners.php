<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FixClubOwners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clubs:fix-owners {--apply : Actually apply changes} {--limit=100 : Limit preview rows}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preview and optionally apply owner_id updates for clubs based on student_code';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $apply = $this->option('apply');

        $this->info('Scanning clubs with student_code and missing owner_id...');

        $matches = DB::table('clubs as c')
            ->leftJoin('users as u', 'u.student_code', '=', 'c.student_code')
            ->select('c.id as club_id', 'c.name as club_name', 'c.student_code as club_mssv', 'c.owner_id', 'u.id as user_id', 'u.name as user_name', 'u.student_code as user_mssv')
            ->whereNotNull('c.student_code')
            ->where('c.student_code', '<>', '')
            ->where(function($q){ $q->whereNull('c.owner_id')->orWhere('c.owner_id', 0); })
            ->limit($limit)
            ->get();

        if ($matches->isEmpty()) {
            $this->info('No candidate clubs found (owner_id null/0 and student_code present).');
            return 0;
        }

        $rows = [];
        foreach ($matches as $m) {
            $rows[] = [
                'club_id' => $m->club_id,
                'club_name' => $m->club_name,
                'club_mssv' => $m->club_mssv,
                'user_id' => $m->user_id ?? '(no match)',
                'user_name' => $m->user_name ?? '(no match)'
            ];
        }

        $this->table(['club_id','club_name','club_mssv','user_id','user_name'], $rows);

        if (! $apply) {
            $this->info('Preview only. Rerun with --apply to perform updates.');
            return 0;
        }

        $this->info('Applying updates...');

        DB::beginTransaction();
        try {
            $updated = 0;
            foreach ($matches as $m) {
                if ($m->user_id) {
                    $club = Club::find($m->club_id);
                    if ($club) {
                        $club->owner_id = $m->user_id;
                        // update chairman name to user's name for display
                        $club->chairman = $m->user_name;
                        $club->save();
                        $updated++;
                    }
                }
            }

            DB::commit();
            $this->info("Done. Updated {$updated} clubs.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to apply updates: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
