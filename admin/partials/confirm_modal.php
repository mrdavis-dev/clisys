<!-- Confirmation modal (shared, included once via menu.php) -->
<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog"
     aria-labelledby="modalConfirmLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmLabel">
                    <i class="fa fa-trash mr-2" aria-hidden="true"></i>Confirmar eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalConfirmBody">
                ¿Seguro que deseas borrar este registro? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-sm" id="modalConfirmOk">
                    <i class="fa fa-trash mr-1" aria-hidden="true"></i>Borrar
                </button>
            </div>
        </div>
    </div>
</div>
