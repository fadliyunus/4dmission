<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <p style="font-size:14px;">
        Hai <strong><?= $admission->full_name; ?></strong>,
    </p>
    <?php if ($admission->status == 103) : ?>
        <p>Bukti pembayaran yang telah di unggah belum dapat kami verifikasi.
            <br>Silakan unggah kembali bukti pembayaran yang telah dilakukan.
        </p>
        <p>Jika terdapat kendala, silakan hubungi tim admisi PPM School of Management:</p>
        <p>S1: 0878-7618-5364 (WhatsApp)</p>
    <?php elseif ($admission->status == 200) : ?>
        <p>Terima kasih telah menyelesaikan administrasi pembayaran tes seleksi di PPM School of Management.
            <br>Terlampir bukti tanda terima pembayaran.
        </p>
        <p>Langkah selanjutnya, silakan melengkapi formulir pendaftaran.</p>
    <?php endif; ?>
    <p>
        Terima kasih,<br>
    </p>
    <p>Salam,<br>Admisi PPM School Management</p>
</div>