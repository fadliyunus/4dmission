<html>

<body>
	<p>Dear <?=$full_name?></p>
	<p>Terima kasih telah melakukan registrasi di sistem admisi PPM School of Management.</p>
	<p>Silakan klik tautan di bawah ini untuk mengaktivasi akun Anda dalam waktu 1 x 24 jam. Jika dalam waktu tersebut tidak dilakukan aktivasi, maka registrasi akun Anda akan terhapus secara otomatis dari data kami.</p>
	<p><a href="<?= base_url('home/activate/' . $id . '/' . $activation) ?>">Klik disini</a> untuk aktivasi</p>
	<p>Salam,<br>Admisi PPM School Management</p>
</body>

</html>