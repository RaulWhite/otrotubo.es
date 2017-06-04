ffprobe \
  -v quiet \
  -show_entries stream=duration \
  -of default=nokey=1:noprint_wrappers=1 "$1"