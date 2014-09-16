Perangkum (Summarizer)
======================

Perangkum adalah Pustaka PHP yang dapat digunakan untuk merangkum (summarize) teks Bahasa Indonesia.

Cara Kerja
----------
* Membaca teks dari file
* Memecah teks menjadi kumpulan paragraf
* Memecah paragraf menjadi kumpulan kalimat
* Melakukan pembobotan kalimat
* Memilih dan mengembalikan kalimat utama


Cara Install
-------------

Sastrawi dapat diinstall dengan [Composer](https://getcomposer.org).

1. Buka terminal (command line) dan arahkan ke directory project Anda.
2. [Download Composer](https://getcomposer.org/download/) dengan cara `php -r "readfile('https://getcomposer.org/installer');" | php`
3. Buat file `composer.json` atau jika sudah ada, tambahkan require sastrawi:

```json
{
    "require": {
        "tediscript/perangkum": "*"
    }
}
```

Kemudian jalankan `php composer.phar install` atau `php composer.phar update` dari `command line`. Jika Anda masih belum memahami bagaimana cara menggunakan Composer, silahkan baca [Getting Started with Composer](https://getcomposer.org/doc/00-intro.md).


Penggunaan
-----------

Clone projek Perangkum dan jalankan file index.php

Pustaka
-------
* Projek ini menggunakan pustaka dari [Sastrawi](https://github.com/sastrawi/sastrawi) untuk melakukan stemming Bahasa Indonesia.


Lisensi
--------

* Lisensi Perangkum adalah MIT License (MIT)
* Lisensi Sastrawi adalah MIT License (MIT)
