#!/bin/bash

# Comprobar ROOT
if [ "$(id -u)" != "0" ]; then
	echo "Para instalar FFMPEG debe ser root"
	exit 1
fi

# Instalar dependencias desde repos oficiales del sistema
sudo apt-get update
sudo apt-get -y install autoconf automake build-essential libass-dev \
  libfreetype6-dev libsdl2-dev libtheora-dev libtool libva-dev libvdpau-dev \
  libvorbis-dev libxcb1-dev libxcb-shm0-dev libxcb-xfixes0-dev pkg-config \
  texinfo zlib1g-dev yasm libx264-dev libfdk-aac-dev libmp3lame-dev \
  libopus-dev libvpx-dev checkinstall

# Descargar código fuente de ffmpeg
mkdir ~/ffmpeg_sources
cd ~/ffmpeg_sources
wget http://ffmpeg.org/releases/ffmpeg-snapshot.tar.bz2
tar xjf ffmpeg-snapshot.tar.bz2
cd ffmpeg

# Configure
PATH="$HOME/bin:$PATH" \
PKG_CONFIG_PATH="$HOME/ffmpeg_build/lib/pkgconfig" \
./configure \
  --pkg-config-flags="--static" \
  --extra-cflags="-I$HOME/ffmpeg_build/include" \
  --extra-ldflags="-L$HOME/ffmpeg_build/lib" \
  --enable-gpl \
  --enable-libass \
  --enable-libfdk-aac \
  --enable-libfreetype \
  --enable-libmp3lame \
  --enable-libopus \
  --enable-libtheora \
  --enable-libvorbis \
  --enable-libvpx \
  --enable-libx264 \
  --enable-nonfree

# Make
PATH="$HOME/bin:$PATH" make -j4

# Crear paquete .deb e instalar
checkinstall \
--pkgname=ffmpeg \
--pkgversion="10:$(date +%Y%m%d%H%M)-git" \
--backup=no \
--deldoc=yes \
--fstrans=no \
--default

hash -r
cd 
