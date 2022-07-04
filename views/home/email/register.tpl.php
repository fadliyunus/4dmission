<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
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
        <?php if ($admission->tgl_seleksi) : ?>
            <tr>
                <td>Tanggal Seleksi</td>
                <td>:</td>
                <td><?= strtoupper(date('j F Y', strtotime($admission->tgl_seleksi))) ?></td>
            </tr>
        <?php endif; ?>
    </table>
    <p>Terima kasih,<br></p>
    <p>Salam,<br>Admisi PPM School Management</p>
</div>