<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <p style="font-size:14px;">
        Terima kasih Bapak/Ibu <strong><?= $admission->full_name; ?></strong>,
    </p>
    <p>Anda sudah melakukan pendaftaran pada website kami. </p>
    <?php if ($admission->status == 103) : ?>
        <p>Pembayaran yang anda lakukan <strong>gagal</strong> kami validasi.</p>
    <?php elseif ($admission->status == 200) : ?>
        <p>Pembayaran yang anda lakukan <strong>berhasil</strong> kami validasi.</p>
    <?php endif; ?>
    <p>
        Terima kasih,<br><br>
    </p>
</div>