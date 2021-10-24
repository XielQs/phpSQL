# phpSQL

[![Latest Stable Version](http://poser.pugx.org/gamerboytr/phpsql/v)](https://packagist.org/packages/gamerboytr/phpsql) [![Total Downloads](http://poser.pugx.org/gamerboytr/phpsql/downloads)](https://packagist.org/packages/gamerboytr/phpsql) [![License](http://poser.pugx.org/gamerboytr/phpsql/license)](https://packagist.org/packages/gamerboytr/phpsql) [![PHP Version Require](http://poser.pugx.org/gamerboytr/phpsql/require/php)](https://packagist.org/packages/gamerboytr/phpsql)

MySql İçin Bir Php Kütüphanesi

Herhangi Bir Sorun Olursa [Buradan](mailto:offical.gamerboytr@yandex.com) Ulaşabilirsiniz
Ayrıca Dosyada `Php Documentor` Kullanılmaktadır, Modern Editörlerde İşinize Yarayabilir (Sıkıştırılmış Dosya İçin Geçerli **Değildir**!)

## Kurulum

İlk Önce Dosyamızı Dahil Edelim

```php
// Dosya İle
require __DIR__."/phpsql.class.php";
// Composer İle
require __DIR__."/vendor/autoload.php";
```

**Composer** Kullanıyorsanız Bu Komutu **CMD'ye Yazmayı** Unutmayın !

```bat
composer require gamerboytr/phpsql
```

Sınıfımızı Başlatalım

```php
$phpsql = new GamerboyTR\phpSQL();
```

Eğerki MySQL Ayarlarınız(kullanıcı adı, şifre vs.) Farklıysa Bunu Ayarlayabiliriz

```php
$phpsql = new GamerboyTR\phpSQL("host", "kullanici_adi", "sifre");
// Veya
$phpsql->set_config("host", "kullanici_adi", "sifre");
```

## Komutlar

Hadi Bir Veritabanına Bağlanalım !

```php
$phpsql->set_db("veritabani_adi");
```

Veritabanından Veri Çekelim

```php
$veri = $phpsql->select("seçici", "tablo");
// Örnek
$veri = $phpsql->select("*", "üyeler");
```

Veritabanında Sorgu(query) Çalıştıralım

```php
$veri = $phpsql->query("sorgu");
// Örnek
$veri = $phpsql->query("SELECT * FROM üyeler");
```

Sınıfın Kaydettiği MySql Bilgilerini Alalım

```php
$dize = $phpsql->get_config(); // Bir Array Döndürür
```

Kolay Bir Şekilde MySql Sınıfını Alalım

```php
// phpSQL ile
$mysqli = $phpsql->connect();
// phpSQL olmadan
try {
    @$mysqli = new mysqli("host", "kullanici_adi", "şifre", "veritabanı");
    if($mysqli->connect_errno)
        die("<br>Mysqli Bağlanma Hatası : ".$mysqli->connect_error);
} catch (\Throwable $th) {
    die("<br>Mysqli Bağlanma Hatası : $th");
}
```

## Tablo İşlemleri

### Tablo Oluşturma

Alabileceği Değerler

- length - Satırın Uzunluğu / int
- AI (AUTO_INCREMENT) - Otomatik Artış / boolean
- unique - Ekstra Olarak (PRIMARY_KEY gibi) / string|null
- isnull - Boş mu / boolean
- comment - Açıklama / string|null

```php
$phpsql->create_table("tablo_adi", [
    [
        "name" => "satir_adi",
        "type" => "varchar"
    ]
]);
```

### Tablodan Veri Silme

```php
$phpsql->delete("tablo_adi", "seçici");
// Örnek
$phpsql->delete("kullanicilar", "adi='mehmet'");
```

### Tabloları Listeleme

Eğerki Verdiğiniz Değer Boşsa Kütüphanede Tanımladığınız Veritabanını Kullanır

```php
print_r($phpsql->get_tables("phpsql")); // Bir Array Döndürür
```

### Tabloya Veri Ekleme

```php
$phpsql->insert("tablo_adi", [
    "veri_adi" => "veri_degeri"
]);
```

### Tablodaki Veriyi Güncelleme

```php
$phpsql->update("tablo_adi", [
    "veri_adi" => "guncellencek_veri_degeri"
], "Nerede");
// Örnek
$phpsql->update("üyeler", [
    "yetki" => "admin"
], "kullanici_adi='gamerboytr'");
```

## Veritabanı İşlemleri

### Veritabanı Oluşturma

```php
$phpsql->create_db("veritabani_adi");
```

### Veritabanlarını Listeleme

```php
$phpsql->get_dbs(); // Array Döndürür
```

### Veritabanı Silme

```php
$phpsql->drop("tablo_veya_veritabani_adi", "silinecek_tur");
// Örnek Veritabanı Silme
$phpsql->drop("phpsql", "database");
// Örnek Tablo Silme
$phpsql->drop("üyeler", "table");
```

## Kütüphane Ayalarını Kaydetme/Yükleme

Dikkat Kaydedilen Ayarı Yükleyeceğiniz Zaman $phpsql Değişkenini Altına Ayarlamayı Unutmayın !

```php
// Örnek
$phpsql = new GamerboyTR\phpSQL();
$phpsql->restore_config();
```

### Kaydetme

Alabileceği Değerler

- fileSavePath - Dosyayı Kaydedeceği Klasör (Boş Veya "./" İse O Dizine Kaydedilir) / string
- overwriteFile - Klasörde Zaten Kaydedilmiş Bir Yapılandırma Ayarı Varsa Üzerine Yaz / boolean

```php
$phpsql->save_config([
    // Değerler Buraya
]);
```

### Yükleme

```php
$phpsql->restore_config("klasor");
```

Author : GamerboyTR Mail : offical.gamerboytr@yandex.com Web : <https://www.gamerboytr.ml>
