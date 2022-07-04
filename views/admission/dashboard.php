<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">
            <img src="<?= base_url() ?>assets/public/images/icons/avatar.jpg" class="alignleft img-circle img-thumbnail my-0" alt="Avatar" style="max-width: 84px;">

            <div class="heading-block border-0">
                <h3><?= $user->full_name ?></h3>
                <span class="mt-0"><?= $user->email ?></span>
            </div>
            <div class="clear"></div>
            <?php if ($personal_information) : ?>
                <form>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tempat Tanggal Lahir</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $personal_information ? $personal_information->birthplace . ', ' . strtoupper(strftime('%e %B %Y', strtotime($personal_information->birthdate))) : '-' ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alamat</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $personal_information ? $personal_information->address_1 . ' ' . $personal_information->address_2 : '-' ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">No Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" value="<?= $personal_information ? $personal_information->mobile_phone : '-' ?>">
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            <div id="toolbar">
                <a href="<?= base_url('admission/create') ?>" class="btn btn-primary">Pendaftaran Baru</a>
            </div>
            <table id="datatable" data-toolbar="#toolbar"></table>

        </div>
    </div>
</section><!-- #content end -->