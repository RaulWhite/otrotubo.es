ffmpeg \
	-i "$1" \
	-c:v libx264 -profile:v baseline -level 3.0 -crf 25 -maxrate 1M -bufsize 3M \
	-c:a libfdk_aac -b:v 96K -ac 2 \
	-vf scale=w=640:h=360:force_original_aspect_ratio=decrease \
	-threads 2 \
	-r "$3" \
	"$4/videos/360/$2.mp4"