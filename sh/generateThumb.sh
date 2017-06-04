ffmpeg \
  -ss $2 \
  -i $1 \
  -vf "scale=w=400:h=225:force_original_aspect_ratio=increase,crop=400:225" \
  -vframes 1 \
  "$5/videos/tmp/thumbs/$3/$4.png"