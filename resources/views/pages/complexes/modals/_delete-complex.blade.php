<div class="modal fade" id="delete-complex-modal" tabindex="-1" aria-labelledby="deleteComplexModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">
            <div class="modal-body p-0">
                <div style="padding:16px 16px 12px;border-bottom:1px solid #eee;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold" style="font-size:15px;">Удалить комплекс #<span id="delete-complex-id"></span>?</span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size:12px;"></button>
                    </div>
                </div>
                <div style="padding:12px 16px;">
                    <p class="mb-1" style="font-size:13px;">Вы уверены, что хотите удалить комплекс?</p>
                    <p class="fw-bold mb-0" style="font-size:13px;" id="delete-complex-name"></p>
                </div>
            </div>
            <div style="padding:0 16px 16px;display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirm-delete-complex">Удалить</button>
            </div>
        </div>
    </div>
</div>
