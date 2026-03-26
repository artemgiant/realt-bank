<div class="modal fade" id="delete-employee-modal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3 justify-content-between">
                    <h2 class="modal-title" id="deleteEmployeeModalLabel">Удаление сотрудника</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <p class="mb-1">Вы уверены, что хотите удалить сотрудника?</p>
                <p class="fw-bold" id="delete-employee-name"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-employee">Удалить</button>
            </div>
        </div>
    </div>
</div>
