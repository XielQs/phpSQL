# phpsql

MySql İçin Bir Php Kütüphanesi

Herhangi Bir Sorun Olursa [Buradan](mailto:offical.gamerboytr@yandex.com) Ulaşabilirsiniz
Ayrıca Dosyada `Php Doc`'da Kullanılmaktadır, Modern Editörlerde İşinize Yarayabilir (Sıkıştırılmış Dosya İçin Geçerli **Değildir**!)

## Kurulum

İlk Önce Dosyamızı Dahil Edelim

```php
require __DIR__."/phpsql.class.php";
```

Sınıfımızı Başlatalım

```php
$phpsql = new GamerboyTR\PhpSql();
```

Eğerki MySQL Ayarlarınız(kullanıcı adı, şifre vs.) Farklıysa Bunu Ayarlayabiliriz

```php
$phpsql = new GamerboyTR\PhpSql("host", "kullanici_adi", "sifre");
// Veya
$phpsql->setMysqli("host", "kullanici_adi", "sifre");
```

## Komutlar

Hadi Bir Veritabanına Bağlanalım !

```php
$phpsql->setDatabase("veritabani_adi");
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
$dize = $phpsql->getMysqliDetails(); // Bir Array Döndürür
```

Kolay Bir Şekilde MySql Sınıfını Alalım

```php
// phpsql ile
$mysqli = $phpsql->connect();
// phpsql olmadan
try {
    @$mysqli = new mysqli("host", "kullanici_adi", "şifre", "veritabanı");
    if($mysqli->connect_errno)
        die("<br>Mysqli Bağlanma Hatası : ".$mysqli->connect_error);
} catch (\Throwable $th) {
    die("<br>Mysqli Bağlanma Hatası : $th");
}
```

## Tablo İşlemleri

### Oluşturma

Alabileceği Değerler

- length - Satırın Uzunluğu / int
- AI (AUTO_INCREMENT) - Otomatik Artış / boolean
- unique - Ekstra Olarak (PRIMARY_KEY gibi) / string|null
- isnull - Boş mu / boolean
- comment - Açıklana / string|null

```php
$phpsql->createTable("tablo_adi",[
    [
        "name" => "satir_adi",
        "type" => "varchar"
    ]
]);
```

### Yakında Daha Fazla Özellik Eklenicektir

Author : GamerboyTR Mail : offical.gamerboytr@yandex.com Web : <https://www.gamerboytr.ml>
