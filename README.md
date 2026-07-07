# php-mvc
# Model changed 

# ln -s /var/www/html/src/themes/landing/css /var/www/html/public/css
# ln -s /var/www/html/src/themes/landing/js /var/www/html/public/js
#   IdentityFile ~/.ssh/id_ed25519_shofiul


# ssh-keygen -t rsa -b 4096 -C "shofiul@krost.com.au" -f ~/.ssh/id_rsa_shofiul_krost

# ssh-keygen -t rsa -b 4096 -C "shofi.tafe@gmail.com" -f ~/.ssh/id_rsa_shofi

mysqldump  -P 3305 -u mvc -p mvc --protocol=tcp  > mvc.sql;

mysql -h localhost -P 3305 -u mvc -p --protocol=tcp -D mvc < db.sql;

mysql -h mysql.krost.internal -u mv -p --protocol=tcp -D mvc < mvc.sql;
mysqldump -h mysql.krost.internal -u mvc -p --protocol=tcp -D mvc > mvc.sql;

// Different error types
error_log("User login failed", 0); // System error log (default)
error_log("Email sent successfully", 1, "admin@example.com"); // Email
error_log("Debug info", 3, "/path/to/debug.log"); // File
error_log("Custom message", 4); // SAPI error handler

// Log with context
$context = [
    'user_id' => 123,
    'action' => 'login_attempt',
    'ip' => $_SERVER['REMOTE_ADDR']
];

error_log(json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'level' => 'ERROR',
    'message' => 'Login attempt failed',
    'context' => $context
]));

// Read the entire error log
$errorLog = file_get_contents('/Users/shofiul/Development/krost-ecommerce-2025/krost-php-mvc/storage/logs/php_errors.log');

// Read last 100 lines
$lines = file('/Users/shofiul/Development/krost-ecommerce-2025/krost-php-mvc/storage/logs/php_errors.log');
$lastLines = array_slice($lines, -100);


# Monitor error log in real-time
tail -f /Users/shofiul/Development/krost-ecommerce-2025/krost-php-mvc/storage/logs/php_errors.log

# Filter for specific error types
tail -f /Users/shofiul/Development/krost-ecommerce-2025/krost-php-mvc/storage/logs/php_errors.log | grep ERROR

// Using trigger_error() - goes to error_log if configured
trigger_error("This is a user error", E_USER_ERROR);

// Using ini_set() to temporarily change settings
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/custom.log');
error_log("This will go to the custom log");


mysqldump  -h mysql.krost.internal  -u mvc -p mvc  --protocol=tcp  > mvc.sql; 

#SQL Tool for format sql script

SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
#SQL Tool for format sql script


VS Code mysql script formatter

SQLTools
mtxr
330,000


composer require symfony/mailer:^6.4



pm2 start npm --name krost-dashboard -- run dev -- --host 0.0.0.0



                        -------------

__/\\\\\\\\\\\\\____/\\\\____________/\\\\____/\\\\\\\\\_____
 _\/\\\/////////\\\_\/\\\\\\________/\\\\\\__/\\\///////\\\___
  _\/\\\_______\/\\\_\/\\\//\\\____/\\\//\\\_\///______\//\\\__
   _\/\\\\\\\\\\\\\/__\/\\\\///\\\/\\\/_\/\\\___________/\\\/___
    _\/\\\/////////____\/\\\__\///\\\/___\/\\\________/\\\//_____
     _\/\\\_____________\/\\\____\///_____\/\\\_____/\\\//________
      _\/\\\_____________\/\\\_____________\/\\\___/\\\/___________
       _\/\\\_____________\/\\\_____________\/\\\__/\\\\\\\\\\\\\\\_
        _\///______________\///______________\///__\///////////////__


                          Runtime Edition

        PM2 is a Production Process Manager for Node.js applications
                     with a built-in Load Balancer.

                Start and Daemonize any application:
                $ pm2 start app.js

                Load Balance 4 instances of api.js:
                $ pm2 start api.js -i 4

                Monitor in production:
                $ pm2 monitor

                Make pm2 auto-boot at server restart:
                $ pm2 startup

                To go further checkout:
                http://pm2.io/


                        -------------

[PM2] Spawning PM2 daemon with pm2_home=/home/ubuntu/.pm2
[PM2] PM2 Successfully daemonized
[PM2] Starting /home/ubuntu/.nvm/versions/node/v23.4.0/bin/npm in fork_mode (1 instance)
[PM2] Done.
┌────┬────────────────────┬─────────────┬─────────┬─────────┬──────────┬────────┬──────┬───────────┬──────────┬──────────┬──────────┬──────────┐
│ id │ name               │ namespace   │ version │ mode    │ pid      │ uptime │ ↺    │ status    │ cpu      │ mem      │ user     │ watching │
├────┼────────────────────┼─────────────┼─────────┼─────────┼──────────┼────────┼──────┼───────────┼──────────┼──────────┼──────────┼──────────┤
│ 0  │ krost-dashboard    │ default     │ 0.40.3  │ fork    │ 292044   │ 0s     │ 0    │ online    │ 0%       │ 33.5mb   │ ubuntu   │ disabled │
└────┴────────────────────┴─────────────┴─────────┴─────────┴──────────┴────────┴──────┴───────────┴──────────┴──────────┴──────────┴──────────┘
ubuntu@ip-10-100-1-250:~/krost-dashboard-vue$ pm2 save
[PM2] Saving current process list...
[PM2] Successfully saved in /home/ubuntu/.pm2/dump.pm2
ubuntu@ip-10-100-1-250:~/krost-dashboard-vue$ pm2 startup 
[PM2] Init System found: systemd
[PM2] To setup the Startup Script, copy/paste the following command:
sudo env PATH=$PATH:/home/ubuntu/.nvm/versions/node/v23.4.0/bin /home/ubuntu/.nvm/versions/node/v23.4.0/lib/node_modules/pm2/bin/pm2 startup systemd -u ubuntu --hp /home/ubuntu
ubuntu@ip-10-100-1-250:~/krost-dashboard-vue$ pm2 save
[PM2] Saving current process list...
[PM2] Successfully saved in /home/ubuntu/.pm2/dump.pm2



## Clear Nginx cache after release

Clearing Nginx cache helps remove old files from EC2 or Docker Nginx, but it cannot delete JS/CSS already cached inside a user's browser. For browser cache, use versioned asset URLs such as `app.js?v=20260526` or hashed build files like `app.8f3a1c.js`.

### EC2 host Nginx cache

```bash
sudo rm -rf /var/cache/nginx/* /var/lib/nginx/cache/* 2>/dev/null || true
sudo nginx -t && sudo systemctl reload nginx
```

```bash
docker exec mvc.nginx  sh -c 'rm -rf /var/cache/nginx/* /var/lib/nginx/cache/* 2>/dev/null || true && nginx -s reload'
```


Fix on EC2
# 1. Clean up (run as ubuntu, not sudo if possible)
pm2 delete krost-dashboard
# 2. Start once — prefer WITHOUT sudo
cd /home/ubuntu/krost-dashboard-vue
pm2 start npm --name krost-dashboard -- run dev -- --host 0.0.0.0 --port 5173
# 3. Persist
pm2 save
Check you only have one:

pm2 list
Check for a second PM2 daemon (sudo issue)
# As ubuntu
pm2 list
# As root (common duplicate source)
sudo pm2 list
If sudo pm2 list also shows krost-dashboard, you have two PM2 daemons:

/home/ubuntu/.pm2 (ubuntu)
/root/.pm2 (root)
Clean both:

pm2 delete krost-dashboard
sudo pm2 delete krost-dashboard
Then start only as ubuntu (no sudo):

pm2 start npm --name krost-dashboard -- run dev -- --host 0.0.0.0 --port 5173
pm2 save
Update deploy / startup scripts
Do not use pm2 start on every deploy. Use:

pm2 restart krost-dashboard || pm2 start npm --name krost-dashboard -- run dev -- --host 0.0.0.0 --port 5173
pm2 save
Or safer:

pm2 delete krost-dashboard 2>/dev/null
pm2 start npm --name krost-dashboard -- run dev -- --host 0.0.0.0 --port 5173
pm2 save
Find what starts it twice
On EC2:


# Deploy/cron scripts
grep -r "pm2 start" /home/ubuntu --include="*.sh" 2>/dev/null
# systemd (pm2 startup)
systemctl status pm2-ubuntu
cat /etc/systemd/system/pm2-ubuntu.service 2>/dev/null