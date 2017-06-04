ffmpeg \
  -i "$1" \
  -c:v libx264 -profile:v baseline -level 3.0 -crf 22 -maxrate 4M -bufsize 4M \
  -c:a libfdk_aac -b:v 128K -ac 2 \
  -vf "$5" \
  -threads 2 \
  -r "$3" \
  "$4/videos/720/$2.mp4"