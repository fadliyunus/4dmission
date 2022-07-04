<div style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Segoe UI Light','Helvetica Neue','Segoe UI',Helvetica,Arial,sans-serif;">
    <div style="text-align: center">
        <img src="<?= base_url() . 'assets/public/images/logo.png' ?>">
    </div>
    <?php if ($admission->tes_seleksi) : ?>
        <?php if ($admission->status == 301) : ?>
            <p style="font-size:14px;">
                Kepada Yth. Bapak <?= $admission->father_name ?> dan Ibu <?= $admission->mother_name ?>,
            </p>
            <p>Pendaftaran atas nama <?=$admission->full_name?> telah kami terima.</p>
            <p>Berdasarkan pendaftaran anak Bapak/Ibu di PPM School of Management sebagai calon mahasiswa program sarjana tahun <?= $angkatan->tahun_ajaran ?> dengan ini kami mengundang <?= $admission->full_name; ?> untuk mengikuti tes seleksi via daring (online).</p>
            <p>Tes seleksi akan dilaksanakan pada :</p>
            <table>
                <tr>
                    <td>Hari/Tanggal</td>
                    <td>:</td>
                    <td><?= date('j F Y', strtotime($admission->tgl_seleksi)) ?></td>
                </tr>
                <tr>
                    <td>Jam</td>
                    <td>:</td>
                    <td><?= date('H:i', strtotime($user_seleksi->waktu_seleksi)) ?></td>
                </tr>
                <tr>
                    <td>Aplikasi</td>
                    <td>:</td>
                    <td><?= $user_seleksi->aplikasi ?></td>
                </tr>
                <tr>
                    <td>Pakaian</td>
                    <td>:</td>
                    <td>Casual Sopan</td>
                </tr>
                <tr>
                    <td>Meeting ID</td>
                    <td>:</td>
                    <td><?= $user_seleksi->zoom_meeting_id ?></td>
                </tr>
                <tr>
                    <td>Passcode</td>
                    <td>:</td>
                    <td><?= $user_seleksi->zoom_passcode ?></td>
                </tr>
            </table>
            <?php if (isset($user_seleksi->zoom_link)) : ?>
                <p><a href="<?= $user_seleksi->zoom_link ?>" target="_blank">Link Join Zoom Meeting</a></p>
            <?php endif; ?>
            <p>*Peserta seleksi wajib melakukan konfirmasi kehadiran maksimal tanggal <?= date('j F Y', strtotime($admission->tgl_seleksi. ' - 2 days' )) ?> <?= date('H:i', strtotime($user_seleksi->waktu_seleksi)) ?> dengan chat ke nomor<br>
                <strong>WA Only: 0878-7618-5364</strong><br>
                dengan format chat : <strong>Nama Peserta_Jalur Masuk_Tanggal Tes_Hadir</strong>
            </p>
            <p>**Untuk bantuan registrasi ke <strong>seleksi.ppmschool.ac.id</strong> silakan chat ke nomor<br>
                <strong>WA Only : 0857-7560-0900</strong>
            </p>
            <p>Demikian disampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        <?php elseif ($admission->status == 302) : ?>
            <p style="font-size:14px;">
                Hai <strong><?= $admission->full_name; ?></strong>,
            </p>
            <p>Pendaftaran Anda belum dapat kami verifikasi.<br>Silakan lengkapi kembali pendaftaran Anda.</p>
            <p>Jika terdapat kendala, hubungi tim admisi PPM School of Management:<br>S1: 0878-7618-5364 (WhatsApp)</p>
        <?php endif; ?>
    <?php else : ?>
        <p>Hai <strong><?= $admission->full_name; ?></strong>,</p>

        <?php if ($admission->status == 301) : ?>
            <p>Terima kasih telah menyelesaikan pendaftaran di PPM School of Management Program <?= $admission->nama_program_studi ?> Angkatan <?= $angkatan->tahun_ajaran ?>. </p>
            <p>Harap tunggu maksimal +- 10 hari kerja, hasil tes seleksi anda akan kami kirimkan melalui email.</p>
        <?php else : ?>
            <p>Pendaftaran Anda belum dapat kami verifikasi.<br>Silakan lengkapi kembali pendaftaran Anda.</p>
            <p>Jika terdapat kendala, hubungi tim admisi PPM School of Management:<br>
                S1: 0878-7618-5364 (WhatsApp)</p>

        <?php endif; ?>
    <?php endif; ?>

    <p>
        Terima kasih,<br>
    </p>
    <p>Salam,<br>Admisi PPM School Management</p>

</div>