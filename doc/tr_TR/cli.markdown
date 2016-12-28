Komut satırı arayüzü-CLI
======================

Kanboard, herhangi bir Unix terminalinden kullanılabilen basit bir komut satırı arabirimi sağlar.
Bu araç yalnızca yerel makinede kullanılabilir.

Bu özellik, komutları web sunucusu işlemleri dışında çalıştırmak için kullanışlıdır.

Kullanımı
-----

- Bir terminal açın ve Kanboard dizinine gidin (örneğin: `cd /var/www/kanboard`)
- `./cli` veya `php cli` komutunu çalıştırın

```bash
Kanboard version master

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  cronjob                            Execute daily cronjob
  help                               Displays help for a command
  list                               Lists commands
  worker                             Execute queue worker
 db
  db:migrate                         Execute SQL migrations
  db:version                         Show database schema version
 export
  export:daily-project-column-stats  Daily project column stats CSV export (number of tasks per column and per day)
  export:subtasks                    Subtasks CSV export
  export:tasks                       Tasks CSV export
  export:transitions                 Task transitions CSV export
 locale
  locale:compare                     Compare application translations with the fr_FR locale
  locale:sync                        Synchronize all translations based on the fr_FR locale
 notification
  notification:overdue-tasks         Send notifications for overdue tasks
 plugin
  plugin:install                     Install a plugin from a remote Zip archive
  plugin:uninstall                   Remove a plugin
  plugin:upgrade                     Update all installed plugins
 projects
  projects:daily-stats               Calculate daily statistics for all projects
 trigger
  trigger:tasks                      Trigger scheduler event for all tasks
 user
  user:reset-2fa                     Remove two-factor authentication for a user
  user:reset-password                Change user password
```

Kullanılabilir komutlar
------------------

### Görevleri CSV olarak dışa aktarma

Kullanımı:

```bash
./cli export:tasks <project_id> <start_date> <end_date>
```

Örnek:

```bash
./cli export:tasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

CSV verileri şu adrese gönderilir; `stdout`.

### Alt görevleri CSV olarak dışa aktarma

Kullanımı:

```bash
./cli export:subtasks <project_id> <start_date> <end_date>
```

Örnek:

```bash
./cli export:subtasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Görev geçişlerini CSV olarak dışa aktarma

Kullanımı:

```bash
./cli export:transitions <project_id> <start_date> <end_date>
```

Örnek:

```bash
./cli export:transitions 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### CSV'de günlük özet verilerini dışa aktar

Dışa aktarılan veriler standart çıktıda bastırılacaktır:

```bash
./cli export:daily-project-column-stats <project_id> <start_date> <end_date>
```

Örnek:

```bash
./cli export:daily-project-column-stats 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Gecikmiş görevler için bildirim gönder

E-postalar, bildirimlerin etkinleştirildiği tüm kullanıcılara gönderilecektir.

```bash
./cli notification:overdue-tasks
```

İsteğe bağlı parametreler:

- `--show`: Ekran bildirimleri gönderin
- `--group`: Bir kullanıcı için tüm gecikmiş görevleri tek bir e-postayla gruplandırın (tüm projelerden)
- `--manager`: Gecikmiş tüm görevleri tek bir e-postayla proje yöneticisine gönderin

Gecikmiş görevleri bayrağıyla da görüntüleyebilirsiniz `--show`:

```bash
./kanboard notification:overdue-tasks --show
+-----+---------+------------+------------+--------------+----------+
| Id  | Title   | Due date   | Project Id | Project name | Assignee |
+-----+---------+------------+------------+--------------+----------+
| 201 | Test    | 2014-10-26 | 1          | Project #0   | admin    |
| 202 | My task | 2014-10-28 | 1          | Project #0   |          |
+-----+---------+------------+------------+--------------+----------+
```

### Günlük proje istatistikleri hesaplamasını çalıştır

Bu komut, her projenin istatistiklerini hesaplar:

```bash
./cli projects:daily-stats
Run calculation for Project #0
Run calculation for Project #1
Run calculation for Project #10
```

### Görevler için tetikleyici

Bu komut, her projenin açık görevlerine "günlük cronjob etkinliği" gönderir.

```bash
./cli trigger:tasks
Trigger task event: project_id=2, nb_tasks=1
```

### Kullanıcı şifresini sıfırla

```bash
./cli user:reset-password my_user
```

Bir şifre ve onay istenir. Karakterler ekrana yazdırılmaz.

### Bir kullanıcı için iki-kademeli kimlik doğrulamayı kaldırma

```bash
./cli user:reset-2fa my_user
```

### Bir eklenti kurma

```bash
./cli plugin:install https://github.com/kanboard/plugin-github-auth/releases/download/v1.0.1/GithubAuth-1.0.1.zip
```

Not: Yüklü dosyalar, geçerli kullanıcıyla aynı izinlere sahip olacak

### Eklentiyi kaldır

```bash
./cli plugin:uninstall Budget
```

### Tüm eklentileri güncelle

```bash
./cli plugin:upgrade
* Updating plugin: Budget Planning
* Plugin up to date: Github Authentication
```

### Arkaplan çalışanını çalıştır

```bash
./cli worker
```

### Veritabanı geçişlerini yürütün

`DB_RUN_MIGRATIONS` parametresi `false` olarak ayarlanırsa, veritabanı geçişlerini manuel olarak çalıştırmışsınızdır:

```bash
./cli db:migrate
```

### Veritabanı şema sürümünü denetle

```bash
./cli db:version
Current version: 95
Last version: 96
```
