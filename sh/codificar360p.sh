ffmpeg \
	-i "$1" \
	-c:v libx264 -profile:v main -crf 25 -maxrate 1M -bufsize 3M \
	-c:a libfdk_aac -b:v 96K -ac 2 \
	-vf scale=w=640:h=360:force_original_aspect_ratio=decrease \
	-threads 2 \
	-r "$3" \
	"/var/www/html/otrotubo/videos/360/$2.mp4" \
	2> "/var/www/html/otrotubo/videos/log/$2.log"
