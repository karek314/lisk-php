apt-get remove libsodium-dev
apt-get install php-pear php-bcmath php-memcached php-curl memcached php7.0-dev -y
cd lib
git clone https://github.com/jedisct1/libsodium --branch stable
cd libsodium
./configure
make && make check
make install
pecl install libsodium
cd ..
if ! grep -q sodium.so $(php --ini | grep Loaded | cut -d" " -f12); then
	echo "extension=sodium.so" >> $(php --ini | grep Loaded | cut -d" " -f12)
fi
git clone https://github.com/paragonie/sodium_compat
git clone https://github.com/karek314/bytebuffer/