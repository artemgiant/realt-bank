{{-- Modal: Confirm Delete --}}
<div class="modal-overlay" id="confirmDeleteOverlay"></div>
<div class="modal-confirm" id="confirmDeleteModal">
    <div class="modal-confirm-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="3 6 5 6 21 6"/>
            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
        </svg>
    </div>
    <h3 class="modal-confirm-title" id="confirmDeleteTitle">Удалить роль?</h3>
    <p class="modal-confirm-text" id="confirmDeleteText">
        Вы уверены, что хотите удалить роль <strong id="confirmDeleteName">Agent</strong>?
        Это действие нельзя отменить.
    </p>
    <div class="modal-confirm-warning" id="confirmDeleteWarning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span id="confirmDeleteWarningText">У этой роли есть 3 пользователя. Они потеряют свои права доступа.</span>
    </div>
    <div class="modal-confirm-actions">
        <button class="btn btn-outline" id="confirmDeleteCancel">Отмена</button>
        <button class="btn btn-danger" id="confirmDeleteSubmit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
            </svg>
            Удалить
        </button>
    </div>
</div>
