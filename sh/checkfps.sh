ffprobe \
  -v quiet \
  -select_streams v \
  -show_entries stream=r_frame_rate \
  -of default=nokey=1:noprint_wrappers=1 "$1" \
  | head -n 1
