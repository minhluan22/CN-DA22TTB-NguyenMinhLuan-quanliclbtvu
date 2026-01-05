{{-- ===================== MODAL ADD CLUB ===================== --}}
<div class="modal fade" id="modalAddClub" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background:#0B3D91;color:white;">
                <h5 class="modal-title">+ Thêm Câu lạc bộ mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.clubs.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- HIỂN THỊ LỖI --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tên CLB</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col">
                            <label class="form-label">Mã CLB (tự tạo)</label>
                                <input type="text" id="create_code" class="form-control" value="Tự tạo khi lưu" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">MSSV</label>
                            <input list="students_mssv" id="create_student_code" name="student_code" class="form-control" required style="width:100%" value="{{ old('student_code') }}" placeholder="Chọn hoặc nhập MSSV">
                            <datalist id="students_mssv">
                                @if(isset($students) && $students->count())
                                    @foreach($students as $s)
                                        <option value="{{ $s->student_code }}" label="{{ $s->name }}"></option>
                                    @endforeach
                                @endif
                            </datalist>

                            {{-- Hiển thị lỗi nếu có --}}
                            @error('student_code')
                                <div class="text-danger mt-1"><strong>⚠️ {{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col">
                            <label class="form-label">Chủ nhiệm</label>
                            <input list="students_chairman" id="create_chairman_input" class="form-control" placeholder="Tìm tên chủ nhiệm" style="width:100%" value="{{ old('chairman') }}">
                            <input type="hidden" id="create_owner_id" name="owner_id" value="{{ old('owner_id') }}">
                            <input type="hidden" id="create_chairman" name="chairman" value="{{ old('chairman') }}">
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
                            <label class="form-label">Lĩnh vực</label>
                            <select name="field" class="form-control" required>
                                <option value="">-- Chọn lĩnh vực --</option>
                                @foreach(\App\Models\Club::getFieldOptions() as $option)
                                    <option value="{{ $option }}" {{ old('field') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status')=='active'?'selected':'' }}>✅ Hoạt động</option>
                                <option value="archived" {{ old('status')=='archived'?'selected':'' }}>🔒 Ngừng hoạt động</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- AUTO OPEN MODAL WHEN ERROR --}}
@if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Mở modal nếu có lỗi
            new bootstrap.Modal(document.getElementById('modalAddClub')).show();
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Datalist-based inputs: map selected chairman display text to user id
        var studentsMap = {};
        @if(isset($students) && $students->count())
            @foreach($students as $s)
                studentsMap["{{ $s->name }} ({{ $s->student_code }})"] = "{{ $s->id }}";
            @endforeach
        @endif

        var createChairmanInput = document.getElementById('create_chairman_input');
        var createChairmanHidden = document.getElementById('create_chairman');
        var createOwnerHidden = document.getElementById('create_owner_id');
        if (createChairmanInput && createChairmanHidden) {
            createChairmanInput.addEventListener('input', function(e){
                var v = e.target.value;
                if (studentsMap[v]) {
                    createChairmanHidden.value = v;
                    if (createOwnerHidden) createOwnerHidden.value = studentsMap[v];
                } else {
                    createChairmanHidden.value = v;
                    if (createOwnerHidden) createOwnerHidden.value = '';
                }
            });
            if (createChairmanHidden.value && !createChairmanInput.value) {
                var found = Object.keys(studentsMap).find(k => studentsMap[k] === createChairmanHidden.value);
                if (found) createChairmanInput.value = found;
            }
        }

        var addModal = document.getElementById('modalAddClub');
        if (addModal) {
            addModal.addEventListener('show.bs.modal', function () {
                fetch('{{ route('admin.clubs.next-code') }}')
                    .then(r => r.json())
                    .then(d => {
                        var el = document.getElementById('create_code');
                        if (el) el.value = d.code;
                    })
                    .catch(e => console.log('Không thể tải mã CLB', e));
            });
        }

        // Form validation trước khi submit
        var form = document.querySelector('#modalAddClub form');
        if (form) {
            form.addEventListener('submit', function(e) {
                var mssvInput = document.getElementById('create_student_code');
                var nameInput = document.querySelector('input[name="name"]');
                var fieldInput = document.querySelector('input[name="field"]');
                var chairInput = document.getElementById('create_chairman');

                // Kiểm tra các trường bắt buộc
                if (!mssvInput || !mssvInput.value.trim()) {
                    e.preventDefault();
                    alert('⚠️ Vui lòng nhập MSSV');
                    return false;
                }
                if (!nameInput || !nameInput.value.trim()) {
                    e.preventDefault();
                    alert('⚠️ Vui lòng nhập Tên CLB');
                    return false;
                }
                if (!fieldInput || !fieldInput.value.trim()) {
                    e.preventDefault();
                    alert('⚠️ Vui lòng nhập Lĩnh vực');
                    return false;
                }
                // Chairman sẽ được đặt thành MSSV fallback nếu không chọn, nên không bắt buộc
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

    /* Styling cho datalist options (Chrome/Edge hỗ trợ) */
    datalist option {
        padding: 8px;
        background: white;
        color: #333;
    }
</style>
