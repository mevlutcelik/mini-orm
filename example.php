<?php

require_once 'vendor/autoload.php';

use MiniOrm\Database;
use MiniOrm\Models\User;
use MiniOrm\Models\Post;

// Database konfigürasyonu
Database::setConfig([
    'host' => 'mysql',
    'port' => 3306,
    'database' => 'testdb',
    'username' => 'root',
    'password' => 'root'
]);

try {
    echo "=== Mini ORM Örnek Kullanım ===\n\n";

    // 1. Kullanıcı oluşturma
    echo "1. Kullanıcı oluşturma:\n";
    $user = User::create([
        'name' => 'Ali Veli',
        'email' => 'ali@example.com',
        'age' => 25,
        'status' => 'active'
    ]);
    echo "Kullanıcı oluşturuldu: ID = " . $user->id . "\n\n";

    // 2. Kullanıcı bulma
    echo "2. Kullanıcı bulma:\n";
    $foundUser = User::find(1);
    if ($foundUser) {
        echo "Bulunan kullanıcı: " . $foundUser->name . " - " . $foundUser->email . "\n\n";
    }

    // 3. Fluent sorgu örneği
    echo "3. Fluent sorgu örneği:\n";
    $activeUsers = User::where('status', 'active')
                      ->where('age', '>', 18)
                      ->orderBy('name', 'asc')
                      ->limit(5)
                      ->get();
    
    echo "Aktif kullanıcılar (" . count($activeUsers) . " adet):\n";
    foreach ($activeUsers as $user) {
        echo "- " . $user->name . " (Yaş: " . $user->age . ")\n";
    }
    echo "\n";

    // 4. Post oluşturma
    echo "4. Post oluşturma:\n";
    $post = Post::create([
        'title' => 'İlk Post',
        'content' => 'Bu benim ilk postum!',
        'user_id' => 1
    ]);
    echo "Post oluşturuldu: " . $post->title . "\n\n";

    // 5. İlişki sorguları
    echo "5. İlişki sorguları:\n";
    $post = Post::find(1);
    if ($post) {
        $postUser = $post->user();
        if ($postUser) {
            echo "Post sahibi: " . $postUser->name . "\n";
        }
    }

    // 6. Kullanıcının postları
    $user = User::find(1);
    if ($user) {
        $userPosts = $user->posts();
        echo "Kullanıcının post sayısı: " . count($userPosts) . "\n\n";
    }

    // 7. Toplu işlemler
    echo "7. Toplu işlemler:\n";
    $userCount = User::count();
    echo "Toplam kullanıcı sayısı: " . $userCount . "\n";

    $hasActiveUsers = User::where('status', 'active')->exists();
    echo "Aktif kullanıcı var mı? " . ($hasActiveUsers ? 'Evet' : 'Hayır') . "\n\n";

    // 8. JSON çıktı
    echo "8. JSON çıktı:\n";
    $user = User::first();
    if ($user) {
        echo "Kullanıcı JSON: " . $user->toJson() . "\n\n";
    }

    // 9. Güncelleme
    echo "9. Güncelleme:\n";
    $updated = User::update(1, ['age' => 26]);
    echo "Kullanıcı güncellendi: " . ($updated ? 'Evet' : 'Hayır') . "\n\n";

    echo "=== Örnek tamamlandı ===\n";

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
    echo "Not: Veritabanı bağlantısı için docker-compose up -d komutunu çalıştırdığınızdan emin olun.\n";
}