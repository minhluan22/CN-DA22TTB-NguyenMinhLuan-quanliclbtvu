<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">

            <!-- HEADER -->
            <div class="modal-header" 
                 style="background: var(--primary-blue); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Sửa tài khoản
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" value="{{ $user->id }}">

                <div class="modal-body">

                    <div class="row g-3">

                        <!-- MSSV -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">MSSV</label>
                            <input type="text"
                                   name="student_code"
                                   class="form-control form-control-lg rounded-3 mssv-input @error('student_code') is-invalid @enderror"
                                   value="{{ old('student_code', $user->student_code) }}"
                                   placeholder="Nhập MSSV (9 số)"
                                   required
                                   maxlength="9"
                                   pattern="^[0-9]{9}$"
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                   data-email-target="email{{ $user->id }}">

                            @error('student_code')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Họ tên -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Họ tên</label>
                            <input type="text"
                                   name="name"
                                   class="form-control form-control-lg rounded-3"
                                   value="{{ $user->name }}"
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Email</label>
                            <input type="email"
                                   id="email{{ $user->id }}"
                                   name="email"
                                   class="form-control form-control-lg rounded-3"
                                   value="{{ $user->email }}"
                                   readonly>

                            <small class="text-muted fst-italic">
                                * Email sẽ tự cập nhật theo MSSV
                            </small>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-md-12">
                            <p class="text-danger fw-semibold mt-1">
                                * Vai trò chỉ chỉnh tại trang Gán quyền hệ thống
                            </p>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer d-flex justify-content-between"
                     style="background: #f8f9fa; border-radius: 0 0 12px 12px;">

                    <button type="button" class="btn btn-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">
                        Hủy
                    </button>

                    <button type="submit"
                            class="btn px-4 py-2 rounded-3"
                            style="background: var(--primary-blue); color: white; font-weight: 600;">
                        Cập nhật
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
