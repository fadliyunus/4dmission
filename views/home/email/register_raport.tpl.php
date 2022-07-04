<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <?php if ($admission->status == 103) : ?>
        <p>Hai <strong><?= $admission->full_name; ?></strong>,</p>
        <p>Pendaftaran Anda belum dapat kami verifikasi.<br>Silakan lengkapi kembali pendaftaran Anda.</p>
        <p>Jika terdapat kendala, hubungi tim admisi PPM School of Management:<br>S1: 0878-7618-5364 (WhatsApp)</p>
        <p>Terima kasih,<br>
        </p>
    <?php elseif ($admission->status == 200) : ?>
        <p>
            Terima kasih <strong><?= $admission->full_name; ?></strong>, telah melakukan pendaftaran sebagai mahasiswa baru di PPM School of Management.
            <br>
            Berikut informasi pendaftaran anda.
        </p>
        <table>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td><?= $admission->nama_program_studi ?></td>
            </tr>
            <tr>
                <td>Jenis Seleksi</td>
                <td>:</td>
                <td><?= $admission->nama_seleksi ?></td>
            </tr>
        </table>

        <p>Langkah selanjutnya, silakan melengkapi formulir pendaftaran Anda di dashboard admisi PPM School of Management.
            <br>Terima kasih,<br>
        </p>
    <?php endif; ?>
    <p>Salam,<br>Admisi PPM School Management</p>
</div>