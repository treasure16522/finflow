# Create a temporary PHP script to generate password hash
php -r "
require '/var/www/html/finflow/vendor/autoload.php';
\$security = new yii\base\Security();
echo \$security->generatePasswordHash('password123');
"
