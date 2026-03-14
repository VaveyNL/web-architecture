#!/bin/bash
echo "Content-Type: text/html; charset=utf-8"
echo ""
echo "<!DOCTYPE html><html lang='ru'>"
echo "<head><meta charset='utf-8'><title>Boardy — Сообщения</title>"
echo "<link rel='stylesheet' href='/css/style.css'></head>"
echo "<body>"
echo "<header><h1><a href='/'>Boardy</a></h1></header>"
echo "<main><h2>Все сообщения</h2>"
if [ -f /var/www/boardy/data/messages.txt ]; then
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%'>"
echo "<tr><th>Дата</th><th>Имя</th><th>Сообщение</th></tr>"
while IFS='|' read -r date name message; do
echo "<tr><td>$date</td><td>$name</td><td>$message</td></tr>"
done < /var/www/boardy/data/messages.txt
echo "</table>"
else
echo "<p>Сообщений пока нет.</p>"
fi
echo "<p style='margin-top:20px'><a href='/feedback.html'>Написать</a> | <a href='/'>На главную</a></p>"
echo "</main></body></html>"
