<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <p style="font-size:14px;">
        Hai <strong><?= $payment->full_name; ?></strong>,
    </p>
    <?php if ($payment->payment_status == 1) : ?>
        <?php if ($payment->payment_type == 2) : ?>
            <p>Terima kasih telah melakukan konfirmasi LoA dan pembayaran tahap I untuk <?= $payment->nama_program_studi ?>.<br>Berikut ialah bukti pembayaran Anda.</p>
            <p>Silakan lakukan pembayaran angsuran selanjutnya sesuai dengan tanggal di LoA yg sudah ditentukan.</p>
            <p>Jika terdapat kendala, hubungi tim admisi PPM School of Management:<br>S1: 0878-7618-5364 (WhatsApp).</p>
        <?php else : ?>
            <p>Terima kasih telah melakukan pembayaran <?= $payment->nama_program_studi ?>.<br>Berikut ialah bukti pembayaran Anda.</p>
        <?php endif; ?>
    <?php elseif ($payment->payment_status == 2) : ?>
        <?php if ($payment->payment_type == 2) : ?>
            <p>Dokumen LoA dan bukti pembayaran yang telah di unggah belum dapat kami verifikasi.<br>Silakan unggah kembali LoA dan bukti pembayaran yang telah dilakukan.</p>
            <p>Jika terdapat kendala, silakan hubungi tim admisi PPM School of Management:<br>S1: 0878-7618-5364 (WhatsApp)</p>
        <?php else : ?>
            <p>Bukti pembayaran yang telah di unggah belum dapat kami verifikasi.</p>
        <?php endif; ?>
    <?php endif; ?>

    <p>Terima kasih,<br></p>
    <p>Salam,<br>Admisi PPM School Management</p>
</div>