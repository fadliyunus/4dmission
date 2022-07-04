<html>
<style>
    td {
        background-color: #fff;
    }
</style>

<body>
    <div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
        <div style="text-align: left">
            <img src="<?= ($pdf ? FCPATH : base_url()) . 'assets/public/images/logo.png' ?>">
        </div>
        <h2 style="text-align:center">BUKTI PENDAFTARAN</h2>
        <p>Hallo <?= $admission->full_name ?>,<br>
            Pendaftaran Anda telah selesai kami verifikasi.
        </p>
        <table cellpadding="0" cellspacing="5" width="100%">
            <tr>
                <td width="40%">Nama Lengkap</td>
                <td>: <?= $admission->full_name ?></td>
            </tr>
            <?php if ($admission->last_education_school_name) : ?>
            <tr>
                <td width="40%">Asal Sekolah dan Tahun Lulus</td>
                <td>: <?= $admission->last_education_school_name ?> (<?= $admission->last_education_year_to ?>)</td>
            </tr>
            <?php endif; ?>
            <?php if ($admission->tgl_seleksi) : ?>
            <tr>
                <td width="40%">Hari dan Tanggal Seleksi</td>
                <td>: <?= date('j F Y', strtotime($admission->tgl_seleksi)) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td width="40%">Jalur Masuk</td>
                <td>: <?= $admission->nama_seleksi ?></td>
            </tr>
        </table>
        <br></br>
        <p>Dokumen:</p>
        <ul>
            <li>Scan Rapot Semester 1 - 6</li>
            <li>Scan KTP</li>
            <li>Scan KK</li>
            <li>Pas Foto</li>
            <!-- <li>Bukti Pembayaran Seleksi</li> -->
        </ul>
        <br>
        <p>Untuk mengikuti seleksi silakan ikuti panduan terlampir.</p>
        <p>Salam,<br>Admisi PPM School Management</p>
    </div>

</body>

</html>