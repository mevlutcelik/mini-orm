# Mini ORM - Laravel Eloquent Benzeri ORM Kütüphanesi

Mini ORM, Laravel Eloquent'e benzer şekilde tasarlanmış, sıfırdan geliştirilmiş bir PHP ORM kütüphanesidir. Bu proje, nesne yönelimli mimari, fluent query builder, güvenli SQL oluşturma ve ilişkisel veritabanı desteği sunar.

## 🚀 Özellikler

- **Fluent Query Builder**: Zincirleme metodlarla sorgu oluşturma
- **Temel CRUD İşlemleri**: Create, Read, Update, Delete
- **İlişkisel Destekler**: HasOne, HasMany, BelongsTo, BelongsToMany
- **SQL Injection Güvenliği**: Parametrik sorgular
- **Model-based Yaklaşım**: Abstract base sınıf desteği
- **Kolay Genişletilebilirlik**: SOLID prensiplere uygun yapı

## 📁 Proje Yapısı

```
mini-orm/
├── src/
│   ├── Database.php            # PDO bağlantı yöneticisi
│   ├── QueryBuilder.php        # Fluent query builder
│   ├── Model.php               # Abstract model sınıfı
│   ├── Models/
│   │   ├── User.php            # Örnek User modeli
│   │   └── Post.php            # Örnek Post modeli
│   └── Relations/
│       ├── Relation.php        # Base ilişki sınıfı
│       ├── HasOne.php          # Tek-tek ilişki
│       ├── HasMany.php         # Bir-çok ilişki
│       ├── BelongsTo.php       # Ters tek-tek ilişki
│       └── BelongsToMany.php   # Çok-çok ilişki
├── tests/
│   └── ModelTest.php           # PHPUnit testleri
├── database.sql                # Veritabanı şeması
├── example.php                 # Kullanım örneği
├── docker-compose.yml          # Docker konfigürasyonu
├── Dockerfile.php              # PHP Docker image
└── README.md
```

## 🔧 Kurulum

### Docker ile Kurulum (Önerilen)

1. Projeyi klonlayın:
```bash
git clone https://github.com/mevlutcelik/mini-orm.git
cd mini-orm
```

2. Docker containerları başlatın:
```bash
docker-compose up -d
```

3. Composer bağımlılıklarını yükleyin:
```bash
composer install
```

4. Veritabanı şemasını oluşturun:
```bash
docker-compose exec -T mysql mysql -u root -proot testdb < database.sql
```

### Manuel Kurulum

1. PHP 7.4+ ve MySQL 8.0+ yüklü olduğundan emin olun
2. Composer ile bağımlılıkları yükleyin: `composer install`
3. `database.sql` dosyasını MySQL'de çalıştırın
4. `src/Database.php` dosyasında veritabanı bağlantı ayarlarını yapılandırın

## 📖 Kullanım

### Temel CRUD İşlemleri

```php
<?php
require_once 'vendor/autoload.php';

use MiniOrm\Database;
use MiniOrm\Models\User;

// Database bağlantısını konfigüre et
Database::setConfig([
    'host' => 'localhost',
    'database' => 'testdb',
    'username' => 'root',
    'password' => 'root'
]);

// Create - Yeni kullanıcı oluştur
$user = User::create([
    'name' => 'Ali Veli',
    'email' => 'ali@example.com',
    'age' => 25
]);

// Read - Kullanıcı bul
$user = User::find(1);
$user = User::where('email', 'ali@example.com')->first();

// Update - Kullanıcı güncelle
User::update(1, ['name' => 'Ali Veli Güncellenmiş']);

// Delete - Kullanıcı sil
User::delete(1);
```

### Fluent Query Builder

```php
// Zincirleme sorgular
$users = User::where('status', 'active')
             ->where('age', '>', 18)
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();

// Aggregate fonksiyonlar
$count = User::count();
$exists = User::where('status', 'active')->exists();
$firstUser = User::first();
```

### Model Tanımlama

```php
<?php
namespace MiniOrm\Models;

use MiniOrm\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = ['name', 'price', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
```

### İlişkiler

```php
// HasMany - Bir kullanıcının birden fazla postu
$user = User::find(1);
$posts = $user->posts()->getResults();

// BelongsTo - Bir postun sahibi
$post = Post::find(1);
$owner = $post->user()->getResults();

// HasOne - Kullanıcının profili
$profile = $user->profile()->getResults();

// BelongsToMany - Çok-çok ilişki
$user = User::find(1);
$roles = $user->roles()->getResults();
```

### Bağımsız QueryBuilder Kullanımı

```php
use MiniOrm\QueryBuilder;

$queryBuilder = new QueryBuilder('users');
$results = $queryBuilder
    ->select(['name', 'email'])
    ->where('age', '>', 25)
    ->orderBy('name', 'asc')
    ->limit(5)
    ->get();
```

## 🧪 Test Etme

```bash
# PHPUnit testlerini çalıştır
./vendor/bin/phpunit

# Örnek dosyayı çalıştır
docker-compose exec php php example.php
```

## 🏗️ Mimari

### SOLID Prensipleri

- **Single Responsibility**: Her sınıf tek bir sorumluluğa sahip
- **Open/Closed**: Genişletmeye açık, değişikliğe kapalı
- **Liskov Substitution**: Alt sınıflar üst sınıfların yerine geçebilir
- **Interface Segregation**: Küçük ve spesifik arayüzler
- **Dependency Inversion**: Soyutlamalara bağımlılık

### Güvenlik

- Tüm SQL sorguları parametrik (prepared statements)
- SQL injection saldırılarına karşı koruma
- Input validation ve sanitization

### Performans

- Lazy loading desteği
- Efficient query building
- Connection pooling ready

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'e push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 📞 İletişim

- **Geliştirici**: [Mevlut Celik](https://github.com/mevlutcelik)
- **Proje Linki**: [https://github.com/mevlutcelik/mini-orm](https://github.com/mevlutcelik/mini-orm)

Laravel Eloquent'e benzer, sıfırdan geliştirilmiş PHP tabanlı mini ORM kütüphanesi.

## 🚀 Özellikler

- **Fluent Query Builder**: Zincirleme metodlarla SQL sorguları
- **ORM Model Sistemi**: Abstract base class ile kolay model oluşturma
- **İlişkiler**: HasOne, HasMany, BelongsTo, BelongsToMany desteği
- **SQL Injection Güvenliği**: Parametrik sorgular
- **CRUD Operasyonlar**: Create, Read, Update, Delete
- **Eager Loading**: N+1 problem çözümü ile `with()` desteği
- **JSON Çıktı**: `toArray()` ve `toJson()` metodları

## 📦 Kurulum

### Docker ile (Önerilen)

```bash
# Repo'yu klonla
git clone https://github.com/mevlutcelik/mini-orm.git
cd mini-orm

# Docker servisleri başlat
docker-compose up -d

# Composer bağımlılıklarını yükle
docker-compose exec php composer install

# Veritabanı tablolarını oluştur
docker-compose exec mysql mysql -u root -proot testdb < database.sql
```

### Manuel Kurulum

```bash
composer install
```

Veritabanı konfigürasyonu için `Database::setConfig()` kullanın.

## 📝 Kullanım

### Temel Konfigürasyon

```php
<?php

require_once 'vendor/autoload.php';

use MiniOrm\Database;

Database::setConfig([
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'testdb',
    'username' => 'root',
    'password' => 'root'
]);
```

### Model Oluşturma

```php
<?php

use MiniOrm\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'age', 'status'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### CRUD Operasyonları

```php
// Oluşturma
$user = User::create([
    'name' => 'Ali Veli',
    'email' => 'ali@example.com',
    'age' => 25
]);

// Okuma
$user = User::find(1);
$users = User::where('age', '>', 18)->get();

// Güncelleme
User::update(1, ['name' => 'Yeni İsim']);

// Silme
User::delete(1);
```

### Fluent Query Builder

```php
$users = User::where('status', 'active')
             ->where('age', '>', 18)
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();
```

### İlişkiler

```php
// HasMany
$user = User::find(1);
$posts = $user->posts();

// BelongsTo
$post = Post::find(1);
$user = $post->user();

// Eager Loading
$posts = Post::with(['user'])->get();
```

### QueryBuilder (Bağımsız Kullanım)

```php
use MiniOrm\QueryBuilder;

$query = new QueryBuilder('users');
$users = $query->where('age', '>', 25)
              ->orderBy('name')
              ->get();
```

## 🧪 Testler

```bash
# PHPUnit testlerini çalıştır
docker-compose exec php ./vendor/bin/phpunit

# Veya manuel
./vendor/bin/phpunit
```

## 📁 Proje Yapısı

```
mini-orm/
├── src/
│   ├── Database.php          # PDO bağlantı yöneticisi
│   ├── QueryBuilder.php      # Fluent query builder
│   ├── Model.php            # Abstract model sınıfı
│   ├── Models/
│   │   ├── User.php         # Örnek User model
│   │   └── Post.php         # Örnek Post model
│   └── Relations/
│       ├── Relation.php     # Base relation sınıfı
│       ├── HasOne.php       # 1:1 ilişki
│       ├── HasMany.php      # 1:N ilişki
│       ├── BelongsTo.php    # N:1 ilişki
│       └── BelongsToMany.php # N:N ilişki
├── tests/
│   └── ModelTest.php        # PHPUnit testleri
├── composer.json            # Composer konfigürasyonu
├── docker-compose.yml       # Docker servisleri
├── database.sql            # Veritabanı şeması
├── example.php             # Örnek kullanım
└── README.md              # Bu dosya
```

## 🔧 API Referansı

### Model Metodları

#### Statik Metodlar
- `Model::create(array $attributes)` - Yeni kayıt oluştur
- `Model::find($id)` - ID ile kayıt bul
- `Model::where($column, $operator, $value)` - Koşullu sorgu
- `Model::get()` - Tüm sonuçları getir
- `Model::first()` - İlk sonucu getir
- `Model::count()` - Kayıt sayısı
- `Model::exists()` - Kayıt var mı kontrol et
- `Model::update($id, array $attributes)` - Kayıt güncelle
- `Model::delete($id)` - Kayıt sil

#### Instance Metodları
- `$model->save()` - Modeli kaydet
- `$model->fill(array $attributes)` - Toplu atama
- `$model->toArray()` - Array'e çevir
- `$model->toJson()` - JSON'a çevir

### QueryBuilder Metodları
- `where($column, $operator, $value)` - WHERE koşulu
- `orderBy($column, $direction)` - Sıralama
- `limit($count)` - Limit
- `offset($count)` - Offset
- `join($table, $first, $operator, $second)` - JOIN
- `get()` - Sonuçları getir
- `first()` - İlk sonuç
- `count()` - Sayım
- `insert(array $data)` - Ekleme
- `update(array $data)` - Güncelleme
- `delete()` - Silme

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add some amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 🎯 Geliştirici Notları

Bu mini ORM, iş görüşmesi taskı olarak geliştirilmiştir ve aşağıdaki konuları kapsamaktadır:

- ✅ Nesne yönelimli mimari tasarım
- ✅ Abstract base sınıflar ve inheritance
- ✅ Encapsulation ve SOLID prensipler
- ✅ Fluent Query Builder
- ✅ SQL injection güvenliği
- ✅ Performans optimizasyonları
- ✅ Test edilebilirlik
- ✅ Genişletilebilir yapı

### Performans Özellikleri
- Hazır statement'lar ile SQL injection koruması
- Lazy loading ile gereksiz sorgu önleme
- Eager loading ile N+1 problem çözümü
- Connection pooling desteği