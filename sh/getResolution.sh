ffprobe \
  -v quiet \
  -select_streams v \
  -show_entries stream=width,height \
  -of default=nokey=1:noprint_wrappers=1 "$1"