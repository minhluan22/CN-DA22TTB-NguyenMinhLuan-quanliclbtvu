{{-- ===================== MODAL EDIT CLUB ===================== --}}
<div class="modal fade" id="modalEditClub" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background:#0B3D91;color:white;">
                <h5 class="modal-title">Cập nhật Câu lạc bộ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="editClubForm" method="POST" enctype="multipart/form-data" action="{{ route('admin.clubs.update', 0) }}">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- HIỂN THỊ LỖI --}}
                    @if ($errors->any() && session('editMode') === true)
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $club = $club ?? null;
                    @endphp
                    <input type="hidden" id="edit_id" name="id" value="{{ old('id', optional($club)->id ?? '') }}">
                    <input type="hidden" id="edit_page" name="page" value="{{ request()->query('page', 1) }}">

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tên CLB</label>
                            <input type="text" id="edit_name" name="name" class="form-control" value="{{ old('name', optional($club)->name ?? '') }}" required>
                        </div>

                        <div class="col">
                            <label class="form-label">Mã CLB</label>
                            <input type="text" id="edit_code" name="code" class="form-control" value="{{ old('code', optional($club)->code ?? '') }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">MSSV</label>
                            @php $currentStudentCode = old('student_code', optional($club)->student_code ?? ''); @endphp
                            <input list="students_mssv" id="edit_student_code" name="student_code" class="form-control" required style="width:100%" value="{{ $currentStudentCode }}">
                            <datalist id="students_mssv">
                                @if(isset($students) && $students->count())
                                    @foreach($students as $s)
                                        <option value="{{ $s->student_code }}" label="{{ $s->name }}"></option>
                                    @endforeach
                                @endif
                            </datalist>
                        </div>

                        <div class="col">
                            <label class="form-label">Chủ nhiệm</label>
                            @php $selectedChairmanId = old('owner_id') ?? optional(optional($club)->owner ?? null)->id ?? null; @endphp
                            @php $selectedChairmanDisplay = null; @endphp
                            @if($selectedChairmanId && isset($students))
                                @php $owner = $students->where('id', $selectedChairmanId)->first(); @endphp
                                @if($owner) @php $selectedChairmanDisplay = $owner->name . ' (' . $owner->student_code . ')'; @endphp @endif
                            @endif
                            <input list="students_chairman" id="edit_chairman_input" class="form-control" placeholder="Tìm tên chủ nhiệm" style="width:100%" value="{{ $selectedChairmanDisplay ?: old('chairman', '') }}">
                            <input type="hidden" id="edit_owner_id" name="owner_id" value="{{ $selectedChairmanId ?? '' }}">
                            <input type="hidden" id="edit_chairman" name="chairman" value="{{ $selectedChairmanDisplay ?: old('chairman', '') }}">
                            <datalist id="students_chairman">
                                @if(isset($students) && $students->count())
                                    @foreach($students as $s)
                                        <option value="{{ $s->name }} ({{ $s->student_code }})" data-id="{{ $s->id }}"></option>
                                    @endforeach
                                @endif
                            </datalist>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Lĩnh vực hoạt động</label>
                            <select id="edit_club_type" name="club_type" class="form-control">
                                <option value="">-- Chọn lĩnh vực --</option>
                                @foreach(\App\Models\Club::getFieldOptions() as $option)
                                    <option value="{{ $option }}" {{ old('club_type', optional($club)->field_display ?? '') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Trạng thái</label>
                            <select id="edit_status" name="status" class="form-control" required>
                                <option value="active" {{ old('status', optional($club)->status ?? 'active') == 'active' ? 'selected' : '' }}>✅ Hoạt động</option>
                                <option value="archived" {{ old('status', optional($club)->status ?? 'active') == 'archived' ? 'selected' : '' }}>🔒 Ngừng hoạt động</option>
                            </select>
                        </div>
                    </div>

                    {{-- Logo removed from edit modal per request --}}

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- AUTO OPEN EDIT MODAL WHEN EDIT VALIDATION ERROR --}}
@if ($errors->any() && session('editMode') === true)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('modalEditClub')).show();
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Datalist mapping for chairman selection
        var studentsMapEdit = {};
        @if(isset($students) && $students->count())
            @foreach($students as $s)
                studentsMapEdit["{{ $s->name }} ({{ $s->student_code }})"] = "{{ $s->id }}";
            @endforeach
        @endif

        var editChairmanInput = document.getElementById('edit_chairman_input');
        var editChairmanHidden = document.getElementById('edit_chairman');
        var editOwnerHidden = document.getElementById('edit_owner_id');
        if (editChairmanInput && editChairmanHidden) {
            editChairmanInput.addEventListener('input', function(e){
                var v = e.target.value.trim();
                if (studentsMapEdit[v]) {
                    editChairmanHidden.value = v;
                    if (editOwnerHidden) editOwnerHidden.value = studentsMapEdit[v];
                } else {
                    editChairmanHidden.value = v;
                    if (editOwnerHidden) editOwnerHidden.value = '';
                }
            });
            // On page load, if hidden has a value but visible doesn't, try to set visible
            if (editChairmanHidden.value && !editChairmanInput.value) {
                var found = Object.keys(studentsMapEdit).find(k => studentsMapEdit[k] === editChairmanHidden.value);
                if (found) editChairmanInput.value = found;
            }
        }

        // Form validation for edit
        var editForm = document.querySelector('#modalEditClub form');
        if (editForm) {
            // Lấy số trang từ URL hiện tại khi mở modal
            var pageInput = document.getElementById('edit_page');
            if (pageInput) {
                var urlParams = new URLSearchParams(window.location.search);
                var currentPage = urlParams.get('page') || '1';
                pageInput.value = currentPage;
            }
            
            editForm.addEventListener('submit', function(e) {
                // Đảm bảo số trang được set trước khi submit
                if (pageInput) {
                    var urlParams = new URLSearchParams(window.location.search);
                    var currentPage = urlParams.get('page') || '1';
                    pageInput.value = currentPage;
                    console.log('Form submit - page value:', pageInput.value); // Debug
                } else {
                    console.warn('Page input not found!'); // Debug
                }
                
                var mssvInput = document.getElementById('edit_student_code');
                var nameInput = document.getElementById('edit_name');
                var fieldInput = document.getElementById('edit_field');
                var chairInput = document.getElementById('edit_chairman');

                if (!mssvInput || !mssvInput.value.trim()) {
                    e.preventDefault();
                    alert('⚠️ Vui lòng nhập MSSV');
                    mssvInput.focus();
                    return false;
                }
                if (!nameInput || !nameInput.value.trim()) {
                    e.preventDefault();
                    alert('⚠️ Vui lòng nhập Tên CLB');
                    nameInput.focus();
                    return false;
                }
                if (!fieldInput || !fieldInput.value.trim()) {
                    // Không bắt buộc field nữa vì có club_type
                }

                // Nếu Chairman input có giá trị nhưng hidden vẫn trống, gán Chairman input vào hidden
                var chairVisible = document.getElementById('edit_chairman_input');
                if (chairVisible && chairVisible.value && !chairInput.value) {
                    chairInput.value = chairVisible.value;
                }
            });
        }
    });
</script>

<!-- CSS tùy chỉnh cho datalist input -->
<style>
    input[list] {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 14px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: white;
        transition: all 0.3s ease;
    }

    input[list]:focus {
        outline: none;
        border-color: #0B3D91;
        box-shadow: 0 0 0 3px rgba(11, 61, 145, 0.1);
        background-color: #fafafa;
    }

    input[list]:hover {
        border-color: #0B3D91;
    }

    datalist option {
        padding: 8px;
        background: white;
        color: #333;
    }
</style>
