# Mini ORM

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