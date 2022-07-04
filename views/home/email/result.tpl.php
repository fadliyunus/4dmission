<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <?php if ($admission->status == 400) : ?>
        <?php if ($admission->beasiswa) : ?>
            <?php if ($acceptance->scholarship == 100) : ?>
                <p style="font-size:14px;">Selamat <strong><?= $admission->full_name; ?></strong>,</p>
                <p>Kamu dinyatakan lulus menjadi calon keluarga besar PPM School of Management.<br>Berikut hasil seleksi Program <?= $admission->nama_program_studi ?>.</p>
                <p>Tahap selanjutnya adalah konfirmasi Letter of Acceptance.<br>Jangan ragu untuk menghubungi tim admisi jika ada informasi yang ingin diketahui lebih lanjut.
                    S1: 0878-7618-5364 (WA Only)</p>
            <?php else : ?>
                <p style="font-size:14px;">Selamat <strong><?= $admission->full_name; ?></strong>,</p>
                <p>Setelah mengkaji hasil seleksi untuk program <?= $admission->nama_program_studi ?> yang telah di laksanakan, Kamu dinyatakan lulus menjadi calon keluarga besar PPM School of Management.</p>
                <p>Walaupun belum lolos ke tahap selanjutnya untuk seleksi beasiswa penuh 100%, kamu mendapatkan potongan biaya pendidikan dari PPM SoM.Berikut hasil seleksi Program <?= $admission->nama_program_studi ?>.</p>
                <p>Tahap selanjutnya adalah konfirmasi LoA dan pembayaran tahap I.Jangan ragu untuk menghubungi tim admisi jika ada informasi yang ingin diketahui lebih lanjut.
                <br>S1: 0878-7618-5364 (WA Only)</p>

            <?php endif; ?>
        <?php else : ?>
            <p style="font-size:14px;">Selamat <strong><?= $admission->full_name; ?></strong>,</p>
            <p>Kamu <strong>dinyatakan lulus</strong> menjadi calon keluarga baru PPM School of Management.</p>
            <p>Berikut hasil seleksi Program <?= $admission->nama_program_studi ?> <?= $admission->nama_seleksi ?></p>
            <p>Tahap selanjutnya adalah konfirmasi LoA dan pembayaran tahap I.</p>
            <p>Jangan ragu untuk menghubungi tim admisi jika ada informasi yang ingin diketahui lebih lanjut.<br>S1: 0878-7618-5364 (WA Only)</p>
        <?php endif; ?>
    <?php elseif ($admission->status == 401) : ?>
        <p style="font-size:14px;">Kepada <strong><?= $admission->full_name; ?></strong>,</p>
        <p>Berdasarkan tes seleksi yang telah dilakukan untuk program <?= $admission->nama_program_studi ?> <?= $admission->nama_seleksi ?>, kamu <strong>belum dapat dinyatakan lulus</strong>.</p>
        <p>Silakan menghubungi tim admisi PPM SoM untuk mengikuti tes ulang atau opsi lainnya.<br>S1: 0878-7618-5364 (WA Only)</p>
    <?php elseif ($admission->status == 402) : ?>
        <p>Kepada <strong><?= $admission->full_name; ?></strong>,</p>
        <p>Berdasarkan hasil seleksi beasiswa PPM School of Management yang telah dilaksanakan pada <?= strftime('%e %B %Y', strtotime($admission->tgl_seleksi)); ?>, kami mengucapkan selamat kepada Anda karena berhasil Lulus Seleksi Tes Tahap I dan berhak mengikuti seleksi Tahap II yaitu Psikotest.<br>
            Adapun waktu untuk seleksi Psikotest dan teknis pelaksanaanya akan kami informasikan dalam waktu dekat.</p>
        <p>Jangan ragu untuk menghubungi tim admisi jika ada informasi yang ingin diketahui lebih lanjut.<br>
            S1: 0878-7618-5364 (WA Only)</p>
    <?php elseif ($admission->status == 403) : ?>
        <p>Kepada <strong><?= $admission->full_name; ?></strong>,</p>
        <p>Berdasarkan hasil seleksi beasiswa PPM School of Management yang telah di laksanakan, kami mengucapkan selamat kepada Anda karena berhasil Lulus Seleksi Psikotest dan berhak mengikuti seleksi terakhir yaitu Wawancara.</p>
        <p>Adapun waktu untuk seleksi wawancara akan diinfokan dalam waktu dekat.</p>
        <p>Jangan ragu untuk menghubungi tim admisi jika ada informasi yang ingin diketahui lebih lanjut.<br>
            S1: 0878-7618-5364 (WA Only)</p>
    <?php endif; ?>
    <p>Terima kasih,<br></p>
    <p>Salam,<br>Admisi PPM School Management</p>
</div>