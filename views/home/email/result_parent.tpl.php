<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <?php if ($admission->status == 400) : ?>
        <p style="font-size:14px;">Kepada Yth. Bapak <?= $admission->father_name ?> dan Ibu <?= $admission->mother_name ?>,</p>
        <p>Selamat kepada <strong><?= $admission->full_name; ?></strong> yang telah lulus mengikuti tes seleksi PPM School of Management <?= $admission->nama_program_studi ?> <?= $admission->nama_seleksi ?></p>
        <p>Tahap selanjutnya adalah konfirmasi LoA dan pembayaran tahap I.</p>
        <p>Jangan ragu untuk menghubungi tim admisi jika ada informasi yang ingin diketahui lebih lanjut.<br>S1: 0878-7618-5364 (WA Only)</p>
    <?php else : ?>
        <p style="font-size:14px;">Kepada Yth. Bapak <?= $admission->father_name ?> dan Ibu <?= $admission->mother_name ?>,</p>
        <p>Berdasarkan tes seleksi yang telah dilakukan <strong><?= $admission->full_name; ?></strong> untuk program <?= $admission->nama_program_studi ?> <?= $admission->nama_seleksi ?>, <strong>belum dapat dinyatakan lulus</strong>.</p>
        <p>Silakan menghubungi tim admisi PPM SoM untuk mengikuti tes ulang atau opsi lainnya.<br>S1: 0878-7618-5364 (WA Only)</p>
    <?php endif; ?>
    <p>Terima kasih,<br></p>
    <p>Salam,<br>Admisi PPM School Management</p>
</div>