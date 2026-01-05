<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\ClubProposal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClubController extends BaseAdminController
{
    private function generateNextCode(): string
    {
        // Lấy tất cả mã CLB có format CLB + số
        $allCodes = Club::whereNotNull('code')
            ->where('code', 'like', 'CLB%')
            ->pluck('code')
            ->filter(function($code) {
                return preg_match('/^CLB\d+$/', $code);
            })
            ->map(function($code) {
                if (preg_match('/^CLB(\d+)$/', $code, $m)) {
                    return intval($m[1]);
                }
                return 0;
            })
            ->filter(function($num) {
                return $num >= 47; // Chỉ lấy các mã từ CLB047 trở đi
            });

        $nextNumber = 47; // Bắt đầu từ CLB047
        if ($allCodes->isNotEmpty()) {
            $maxNumber = $allCodes->max();
            if ($maxNumber >= 47) {
                $nextNumber = $maxNumber + 1;
            }
        }

        return 'CLB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if (!$base) {
            $base = 'clb';
        }
        $slug = $base;
        $i = 1;

        while (
            Club::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }

    public function index(Request $request)
    {
        $query = Club::with('owner');
        
        // Lấy giá trị filter
        $search = $request->input('search', '');
        $field = $request->input('field', '');
        
        // Áp dụng filters chung
        $query = $this->applyFilters($query, $request, [
            'field' => [
                'type' => 'custom',
                'callback' => function($q, $value) {
                    // Chuyển tiếng Việt về tiếng Anh để search trong database
                    $englishValue = Club::getFieldValue($value);
                    $q->where(function($query) use ($englishValue, $value) {
                        // Tìm cả tiếng Việt và tiếng Anh
                        $query->where('field', 'like', "%{$englishValue}%")
                              ->orWhere('club_type', 'like', "%{$englishValue}%")
                              ->orWhere('field', 'like', "%{$value}%")
                              ->orWhere('club_type', 'like', "%{$value}%");
                    });
                }
            ],
        ]);

        // Tìm kiếm với relation
        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('student_code', 'like', "%{$search}%")
                  ->orWhere('chairman', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                  });
            });
        }

        // Filter theo risk_level nếu có
        $riskLevel = $request->input('risk_level');
        if ($riskLevel && in_array($riskLevel, ['good', 'warning', 'danger'])) {
            // Tính risk level cho tất cả clubs và filter
            $hasViolationType = DB::getSchemaBuilder()->hasColumn('events', 'violation_type');
            $violationSubQuery = $hasViolationType 
                ? '(SELECT club_id, COUNT(*) as violation_count FROM events WHERE violation_type IS NOT NULL GROUP BY club_id)'
                : '(SELECT club_id, 0 as violation_count FROM clubs WHERE 1=0)';
            
            $clubsWithRisk = DB::table('clubs')
                ->leftJoin(DB::raw('(SELECT club_id, COUNT(*) as event_count, MAX(start_at) as last_event FROM events WHERE approval_status = "approved" GROUP BY club_id) as event_stats'), 'clubs.id', '=', 'event_stats.club_id')
                ->leftJoin(DB::raw($violationSubQuery . ' as violation_stats'), 'clubs.id', '=', 'violation_stats.club_id')
                ->leftJoin(DB::raw('(SELECT club_id, COUNT(DISTINCT user_id) as member_count FROM club_members WHERE status = "approved" GROUP BY club_id) as member_stats'), 'clubs.id', '=', 'member_stats.club_id')
                ->select('clubs.id')
                ->where('clubs.status', 'active')
                ->get()
                ->map(function($club) use ($hasViolationType) {
                    // Tính risk level (logic giống AdminController)
                    $eventStats = DB::table('events')
                        ->where('club_id', $club->id)
                        ->where('approval_status', 'approved')
                        ->select(DB::raw('COUNT(*) as event_count'), DB::raw('MAX(start_at) as last_event'))
                        ->first();
                    
                    $violationCount = 0;
                    if ($hasViolationType) {
                        $violationCount = DB::table('events')
                            ->where('club_id', $club->id)
                            ->whereNotNull('violation_type')
                            ->count();
                    }
                    
                    $memberCount = DB::table('club_members')
                        ->where('club_id', $club->id)
                        ->where('status', 'approved')
                        ->count();
                    
                    $riskLevel = 'good';
                    
                    // Không có hoạt động > 3 tháng = Nguy cơ
                    if ($eventStats->last_event) {
                        $monthsSinceLastEvent = \Carbon\Carbon::parse($eventStats->last_event)->diffInMonths(now());
                        if ($monthsSinceLastEvent > 3) {
                            $riskLevel = 'danger';
                        }
                    } elseif ($eventStats->event_count == 0) {
                        $riskLevel = 'danger';
                    }
                    
                    // Nhiều vi phạm = Cảnh báo/Nguy cơ
                    if ($violationCount > 0) {
                        if ($violationCount >= 3) {
                            $riskLevel = 'danger';
                        } elseif ($violationCount >= 2) {
                            if ($riskLevel == 'good') $riskLevel = 'warning';
                        }
                    }
                    
                    // Ít thành viên = Cảnh báo
                    if ($memberCount < 10) {
                        if ($riskLevel == 'good') $riskLevel = 'warning';
                    }
                    
                    return ['id' => $club->id, 'risk_level' => $riskLevel];
                })
                ->filter(function($club) use ($riskLevel) {
                    return $club['risk_level'] === $riskLevel;
                })
                ->pluck('id')
                ->toArray();
            
            if (count($clubsWithRisk) > 0) {
                $query->whereIn('id', $clubsWithRisk);
            } else {
                // Nếu không có club nào match, trả về empty result
                $query->whereRaw('1 = 0');
            }
        }

        $clubs = $this->paginateWithQueryString($query, 10, 'created_at', 'desc');

        // Lấy tổng thành viên cho từng CLB (tất cả trạng thái) để hiển thị nhanh
        $memberCounts = DB::table('club_members')
            ->select('club_id', DB::raw('count(*) as total'))
            ->groupBy('club_id')
            ->pluck('total', 'club_id');

        // Lấy thông tin chủ nhiệm từ club_members (ưu tiên) cho từng CLB
        $chairmenFromMembers = DB::table('club_members')
            ->join('users', 'club_members.user_id', '=', 'users.id')
            ->where('club_members.position', 'chairman')
            ->where('club_members.status', 'approved')
            ->select(
                'club_members.club_id',
                'users.id as user_id',
                'users.name',
                'users.student_code'
            )
            ->get()
            ->keyBy('club_id');

        // Danh sách sinh viên (datalist tìm MSSV/Chủ nhiệm)
        $students = User::where('role_id', 2)
            ->orderBy('student_code')
            ->select('id', 'name', 'student_code')
            ->get();

        // Lấy danh sách các lĩnh vực để filter
        $fields = Club::whereNotNull('field')
            ->orWhereNotNull('club_type')
            ->select('field', 'club_type')
            ->get()
            ->map(function($club) {
                return $club->club_type ?? $club->field;
            })
            ->filter()
            ->unique()
            ->values();

        return view('admin.clubs.index', compact('clubs', 'search', 'field', 'memberCounts', 'students', 'fields', 'chairmenFromMembers'));
    }

    public function create()
    {
        return view('admin.clubs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:clubs,code',
            'student_code' => 'nullable|string|max:50',
            'field' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,archived',
            'owner_id' => 'nullable|exists:users,id',
            'chairman' => 'nullable|string|max:255',
        ]);

        // Map owner theo owner_id, nếu không có thì dò theo MSSV
        $owner = null;
        if (!empty($data['owner_id'])) {
            $owner = User::find($data['owner_id']);
        } elseif (!empty($data['student_code'])) {
            $owner = User::where('student_code', $data['student_code'])->first();
            if ($owner) {
                $data['owner_id'] = $owner->id;
            }
        }

        if ($owner) {
            $data['chairman'] = $owner->name . ' (' . $owner->student_code . ')';
            if (empty($data['student_code'])) {
                $data['student_code'] = $owner->student_code;
            }
        }

        // Auto code
        if (empty($data['code'])) {
            $data['code'] = $this->generateNextCode();
        }

        // Chuyển đổi field từ tiếng Việt về tiếng Anh để lưu vào database
        if (isset($data['field']) && !empty($data['field'])) {
            $data['field'] = Club::getFieldValue($data['field']);
        }

        $data['slug'] = $this->makeUniqueSlug($data['name']);
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clubs', 'public');
            $data['logo'] = $path;
        }

        $club = Club::create($data);

        // Tự thêm chủ nhiệm vào bảng club_members (để thành viên = 1)
        if (!empty($data['owner_id'])) {
            DB::table('club_members')->insert([
                'club_id' => $club->id,
                'user_id' => $data['owner_id'],
                'position' => 'chairman',
                'status' => 'approved',
                'joined_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Tạo CLB thành công');
    }

    public function edit(Club $club)
    {
        return view('admin.clubs.edit', compact('club'));
    }

    public function update(Request $request, $id)
    {
        $club = Club::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:clubs,code,'.$club->id,
            'student_code' => 'nullable|string|max:50',
            'field' => 'nullable|string|max:255',
            'club_type' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,archived',
            'owner_id' => 'nullable|exists:users,id',
            'chairman' => 'nullable|string|max:255',
        ]);

        // Map owner tương tự store
        $owner = null;
        if (!empty($data['owner_id'])) {
            $owner = User::find($data['owner_id']);
        } elseif (!empty($data['student_code'])) {
            $owner = User::where('student_code', $data['student_code'])->first();
            if ($owner) {
                $data['owner_id'] = $owner->id;
            }
        }   

        if ($owner) {
            $data['chairman'] = $owner->name . ' (' . $owner->student_code . ')';
            if (empty($data['student_code'])) {
                $data['student_code'] = $owner->student_code;
            }
        }

        // Giữ nguyên code nếu không chỉnh
        if (empty($data['code'])) {
            $data['code'] = $club->code ?? $this->generateNextCode();
        }

        $data['slug'] = $this->makeUniqueSlug($data['name'], $club->id);

        // Chuyển đổi club_type từ tiếng Việt về tiếng Anh để lưu vào database
        if (isset($data['club_type']) && !empty($data['club_type'])) {
            $data['club_type'] = Club::getFieldValue($data['club_type']);
        }

        if ($request->hasFile('logo')) {
            // xóa logo cũ nếu có
            if ($club->logo) Storage::disk('public')->delete($club->logo);
            $path = $request->file('logo')->store('clubs', 'public');
            $data['logo'] = $path;
        }

        // ⭐ LƯU GIÁ TRỊ owner_id CŨ TRƯỚC KHI SAVE
        $oldOwnerId = $club->owner_id;
        $newOwnerId = $data['owner_id'] ?? null;

        $club->fill($data)->save();

        // ⭐ CẬP NHẬT CHỦ NHIỆM TRONG BẢNG club_members
        // Nếu owner_id thay đổi, cần cập nhật position trong club_members
        if (!empty($newOwnerId) && $oldOwnerId != $newOwnerId) {
            // Tìm chủ nhiệm cũ và đổi position từ 'chairman' sang 'member'
            // Không cần kiểm tra status vì có thể chủ nhiệm cũ có status khác
            $oldChairman = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('position', 'chairman')
                ->first();
            
            if ($oldChairman) {
                // Đổi chủ nhiệm cũ thành thành viên thường
                DB::table('club_members')
                    ->where('id', $oldChairman->id)
                    ->update([
                        'position' => 'member',
                        'updated_at' => now(),
                    ]);
            }

            // Kiểm tra chủ nhiệm mới đã có trong club_members chưa
            $newChairmanMember = DB::table('club_members')
                ->where('club_id', $club->id)
                ->where('user_id', $newOwnerId)
                ->first();

            if ($newChairmanMember) {
                // Nếu đã có, cập nhật position thành 'chairman'
                DB::table('club_members')
                    ->where('id', $newChairmanMember->id)
                    ->update([
                        'position' => 'chairman',
                        'status' => 'approved',
                        'updated_at' => now(),
                    ]);
            } else {
                // Nếu chưa có, thêm mới với position = 'chairman'
                DB::table('club_members')->insert([
                    'club_id' => $club->id,
                    'user_id' => $newOwnerId,
                    'position' => 'chairman',
                    'status' => 'approved',
                    'joined_date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Lấy số trang hiện tại từ request
        $page = $request->input('page');
        
        // Debug: Log để kiểm tra
        \Log::info('Update Club - Page from request: ' . ($page ?? 'null'));
        \Log::info('Update Club - All request data: ', $request->all());
        
        // Lấy các query parameters khác (search, field) để giữ lại filter
        $queryParams = [];
        if ($request->has('search') && $request->input('search')) {
            $queryParams['search'] = $request->input('search');
        }
        if ($request->has('field') && $request->input('field')) {
            $queryParams['field'] = $request->input('field');
        }
        
        // Thêm số trang vào query params nếu có
        if ($page) {
            $queryParams['page'] = $page;
        }
        
        \Log::info('Update Club - Redirect params: ', $queryParams);
        
        return redirect()->route('admin.clubs.index', $queryParams)->with('success','Cập nhật CLB thành công');
    }

    public function nextCode()
    {
        return response()->json(['code' => $this->generateNextCode()]);
    }

    public function destroy($id)
    {
        $club = Club::findOrFail($id);
        // xóa logo nếu có
        if ($club->logo) Storage::disk('public')->delete($club->logo);
        try {
            $club->delete();
            return redirect()->route('admin.clubs.index')->with('success','Xóa CLB thành công');
        } catch (\Throwable $e) {
            return redirect()->route('admin.clubs.index')->with('error','Không thể xóa CLB: '.$e->getMessage());
        }
    }

}
