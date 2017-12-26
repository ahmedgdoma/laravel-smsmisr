# laravel-smsmisr
sending sms with smsmisr account

# install
<pre>composer require bakly-systems/smsmisr</pre>

add this in <code>config/app.php</code> in <code>providers[]</code>
<pre>baklysystems\smsmisr\SmsServiceProvider::class,</pre>

then run in your terminal
<pre>php artisan vendor:publish</pre>

after that you should migrate and take care we are <code>setting</code> table 
  if no conflict then run
<pre>php artisan migrate</pre>

next step you need to add in <code>setting</code> table your sending number with your credit and credit will update automaticly

and you should fill empty strings in the array <code>config/Sms.php</code>

finally you have <code>SmsController.php</code> in <code>App/Http/Controllers</code>

finally add a route to <code>send_sms</code> with request[sms, mobiles, encode] 
