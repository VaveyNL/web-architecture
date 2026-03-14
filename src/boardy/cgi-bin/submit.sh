#!/bin/bash
read -n $CONTENT_LENGTH POST_DATA
NAME=$(echo "$POST_DATA" | sed 's/.*name=\([^&]*\).*/\1/' | sed 's/+/ /g')
MESSAGE=$(echo "$POST_DATA" | sed 's/.*message=\([^&]*\).*/\1/' | sed 's/+/ /g')
echo "$(date '+%Y-%m-%d %H:%M:%S')|$NAME|$MESSAGE" >> /var/www/boardy/data/messages.txt
echo "Content-Type: text/html; charset=utf-8"
echo ""
echo "<!DOCTYPE html><html lang='ru'>"
echo "<head><meta charset='utf-8'><title>Boardy</title>"
echo "<link rel='stylesheet' href='/css/style.css'></head>"
echo "<body>"
echo "<header><h1><a href='/'>Boardy</a></h1></header>"
echo "<main>"
echo "<h2>Спасибо, $NAME!</h2>"
echo "<p>Ваше сообщение получено.</p>"
echo "<p><a href='/'>На главную</a> | <a href='/cgi-bin/messages.sh'>Все сообщения</a></p>"
echo "</main>"
echo "</body></html>"
