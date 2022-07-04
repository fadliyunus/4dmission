<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <?= form_hidden('id_skema_pembayaran', $id_skema_pembayaran) ?>
            <div id="toolbar" class="p-3">
                <a href="<?= base_url('backoffice/skema_pembayaran/create_detail/' . $id_skema_pembayaran) ?>" class="btn btn-primary">Create</a>
            </div>
            <table id="datatable" data-toolbar="#toolbar"></table>
        </div>
    </div>

</div>