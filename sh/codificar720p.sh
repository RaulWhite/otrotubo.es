ffmpeg \
-i "$1" \
-c:v libx264 -profile:v main -crf 22 -maxrate 5M -bufsize 3M \
-c:a libfdk_aac -b:v 128K -ac 2 \
-vf scale=w=1280:h=720:force_original_aspect_ratio=decrease \
-threads 2 \
-r "$3" \
"/var/www/html/otrotubo/videos/720/$2.mp4" \
2> "/var/www/html/otrotubo/videos/tmp/log/$2.log"
