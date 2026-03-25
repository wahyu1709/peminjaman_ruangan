<div class="modal fade" id="manageCatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">

            <div class="modal-header text-white border-0"
                 style="background:linear-gradient(90deg,#7c3aed,#6d28d9);padding:14px 20px;">
                <div class="d-flex align-items-center">
                    <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.2);
                                display:flex;align-items:center;justify-content:center;margin-right:10px;">
                        <i class="fas fa-tags" style="font-size:.85rem;"></i>
                    </div>
                    <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                        Kelola Kategori Inventaris
                    </h5>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:.8;">&times;</button>
            </div>

            <div class="modal-body" style="padding:20px;">

                {{-- Form tambah kategori baru --}}
                <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;
                            border-left:3px solid #7c3aed;margin-bottom:18px;">
                    <div style="font-size:.72rem;font-weight:700;color:#7c3aed;
                                text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">
                        <i class="fas fa-plus-circle mr-1"></i>Tambah Kategori Baru
                    </div>
                    <form id="addCatForm">
                        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                            <div style="flex:3;min-width:140px;">
                                <label class="field-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="newCatName" class="field-input"
                                       required placeholder="Contoh: Drone, Tenda, Kipas Angin...">
                            </div>
                            <div style="flex:2;min-width:120px;">
                                <label class="field-label">Ikon
                                    <a href="https://fontawesome.com/icons" target="_blank"
                                       style="font-size:.65rem;color:#7c3aed;">(FontAwesome)</a>
                                </label>
                                <div style="position:relative;">
                                    <span id="newCatIconPreview"
                                          style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#7c3aed;">
                                        <i class="fas fa-box"></i>
                                    </span>
                                    <input type="text" name="icon" id="newCatIcon" class="field-input"
                                           placeholder="fas fa-box" style="padding-left:32px;"
                                           value="fas fa-box">
                                </div>
                            </div>
                            <div style="flex:1;min-width:80px;">
                                <label class="field-label">Urutan</label>
                                <input type="number" name="sort_order" class="field-input"
                                       value="500" min="0" placeholder="500">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-sm text-white font-weight-bold"
                                        style="border-radius:8px;background:linear-gradient(135deg,#7c3aed,#5b21b6);
                                               border:none;padding:8px 16px;white-space:nowrap;">
                                    <i class="fas fa-plus mr-1"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Daftar kategori --}}
                <div style="font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;
                            letter-spacing:.06em;margin-bottom:10px;">
                    <i class="fas fa-list mr-1"></i>Daftar Kategori
                    <span id="catCount" style="font-weight:400;color:#94a3b8;margin-left:4px;"></span>
                </div>

                <div id="catListWrap" style="max-height:360px;overflow-y:auto;">
                    <table class="table mb-0" id="catTable" style="font-size:.875rem;">
                        <thead style="position:sticky;top:0;background:#fff;z-index:1;">
                            <tr style="background:#f8fafc;">
                                <th style="padding:8px 10px;font-size:.7rem;font-weight:700;color:#64748b;
                                           text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e2e8f0;">
                                    Ikon</th>
                                <th style="padding:8px 10px;font-size:.7rem;font-weight:700;color:#64748b;
                                           text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e2e8f0;">
                                    Nama</th>
                                <th style="padding:8px 10px;font-size:.7rem;font-weight:700;color:#64748b;
                                           text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e2e8f0;">
                                    Key</th>
                                <th style="padding:8px 10px;font-size:.7rem;font-weight:700;color:#64748b;
                                           text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e2e8f0;
                                           text-align:center;">
                                    Urutan</th>
                                <th style="padding:8px 10px;font-size:.7rem;font-weight:700;color:#64748b;
                                           text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e2e8f0;
                                           text-align:center;">
                                    Status</th>
                                <th style="padding:8px 10px;font-size:.7rem;font-weight:700;color:#64748b;
                                           text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e2e8f0;
                                           text-align:center;">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="catTableBody">
                            <tr>
                                <td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Memuat...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer border-0" style="padding:12px 20px;background:#f8fafc;">
                <button type="button" class="btn btn-sm btn-light"
                        style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                        data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Kategori (kecil, inline) --}}
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header text-dark border-0"
                 style="background:linear-gradient(90deg,#d97706,#fbbf24);padding:14px 20px;">
                <h5 class="modal-title mb-0 font-weight-bold" style="font-size:.95rem;">
                    <i class="fas fa-edit mr-2"></i>Edit Kategori
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="opacity:.7;">&times;</button>
            </div>
            <form id="editCatForm">
                <input type="hidden" id="editCatId">
                <div class="modal-body" style="padding:18px 20px;">
                    <div class="field-group">
                        <label class="field-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCatName" class="field-input" required>
                    </div>
                    <div class="form-row-2">
                        <div class="field-group">
                            <label class="field-label">Ikon</label>
                            <div style="position:relative;">
                                <span id="editCatIconPreview"
                                      style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#7c3aed;">
                                    <i class="fas fa-box"></i>
                                </span>
                                <input type="text" name="icon" id="editCatIcon" class="field-input"
                                       style="padding-left:32px;">
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Urutan</label>
                            <input type="number" name="sort_order" id="editCatSort" class="field-input" min="0">
                        </div>
                    </div>
                    <div class="field-group mb-0">
                        <label class="field-label">Status</label>
                        <select name="is_active" id="editCatActive" class="field-input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:12px 20px;background:#f8fafc;">
                    <button type="button" class="btn btn-sm btn-light"
                            style="border-radius:8px;font-weight:600;border:1.5px solid #e2e8f0;"
                            data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm text-dark font-weight-bold"
                            style="border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);border:none;padding:7px 16px;">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>