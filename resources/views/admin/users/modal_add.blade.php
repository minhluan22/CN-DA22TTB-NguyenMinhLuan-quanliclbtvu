<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form" value="add">

                <div class="modal-header" style="background: var(--primary-blue); color:#fff;">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus-fill me-2"></i>Thêm tài khoản mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Họ tên --}}
                        <div class="col-md-6">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                   id="createEmail"
                                   class="form-control"
                                   placeholder="Sẽ tự tạo theo MSSV"
                                   readonly>
                        </div>

                        {{-- MSSV --}}
                        <div class="col-md-6">
                            <label class="form-label">Mã số sinh viên (MSSV)</label>
                            <input type="text"
                                   id="mssvCreate"
                                   name="student_code"
                                   class="form-control"
                                   maxlength="9"
                                   required
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                            <div id="mssvError" class="text-danger fw-bold mt-1" style="display:none;"></div>
                        </div>

                        <small class="default-role mt-2">
                            <i class="bi bi-shield-check text-danger"></i>
                            Mặc định quyền: Sinh Viên
                        </small>

                        <input type="hidden" name="role_id" value="2">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>

                    <button type="submit"
                            class="btn btn-primary px-4"
                            id="btnAddUser">
                        Thêm
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
